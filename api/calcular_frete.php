<?php
header('Content-Type: application/json; charset=utf-8');

$ORIGEM_CEP = '85506290';
$ORIGEM_COORDENADAS_PADRAO = [
    'lat' => -26.2295,
    'lon' => -52.6716,
];

$global_pdo = null;

function lerConfiguracao(string $chave, string $padrao): string
{
    global $global_pdo;

    try {
        if ($global_pdo === null) {
            $global_pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
            $global_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        $stmt = $global_pdo->prepare('SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1');
        $stmt->execute([':chave' => $chave]);
        $valor = $stmt->fetchColumn();

        return $valor !== false && $valor !== null ? (string) $valor : $padrao;
    } catch (Throwable $e) {
        return $padrao;
    }
}

function httpGetJson(string $url): array
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'ResinoirFrete/1.0',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
        ],
    ]);

    $resposta = curl_exec($ch);
    $erro = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($erro !== '' || $status >= 400) {
        return [];
    }

    $dados = json_decode((string) $resposta, true);
    return is_array($dados) ? $dados : [];
}

function brasilApiCep(string $cep): array
{
    $cep = preg_replace('/\D+/', '', $cep);
    if (!preg_match('/^\d{8}$/', $cep)) {
        return [];
    }

    $url = 'https://brasilapi.com.br/api/cep/v1/' . urlencode($cep);
    $dados = httpGetJson($url);
    return is_array($dados) ? $dados : [];
}

function geocodificarCep(string $cep): array
{
    $cep = preg_replace('/\D+/', '', $cep);
    $fallback = $GLOBALS['ORIGEM_COORDENADAS_PADRAO'];

    if (!preg_match('/^\d{8}$/', $cep)) {
        return $fallback;
    }

    $dadosCep = brasilApiCep($cep);
    $componentes = [];

    if (!empty($dadosCep['street'])) {
        $componentes[] = trim((string) $dadosCep['street']);
    }
    if (!empty($dadosCep['neighborhood'])) {
        $componentes[] = trim((string) $dadosCep['neighborhood']);
    }
    if (!empty($dadosCep['city'])) {
        $componentes[] = trim((string) $dadosCep['city']);
    }
    if (!empty($dadosCep['state'])) {
        $componentes[] = trim((string) $dadosCep['state']);
    }

    $buscas = [];
    $buscas[] = implode(', ', $componentes !== [] ? $componentes : [$cep]);
    $buscas[] = $cep . ', ' . (!empty($dadosCep['city']) ? $dadosCep['city'] : 'Pato Branco') . ', ' . (!empty($dadosCep['state']) ? $dadosCep['state'] : 'PR');
    $buscas[] = 'Pato Branco, PR';

    foreach ($buscas as $busca) {
        if (trim($busca) === '') {
            continue;
        }

        $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=br&q=' . urlencode($busca);
        $dados = httpGetJson($url);

        if (!empty($dados) && isset($dados[0])) {
            $item = $dados[0];
            return [
                'lat' => (float) ($item['lat'] ?? $fallback['lat']),
                'lon' => (float) ($item['lon'] ?? $fallback['lon']),
            ];
        }
    }

    return $fallback;
}

function geocodificarEndereco(string $endereco): array
{
    $valor = trim($endereco);
    $cep = preg_replace('/\D+/', '', $valor);

    if (preg_match('/^\d{8}$/', $cep) === 1) {
        return geocodificarCep($cep);
    }

    $busca = $valor;
    if ($busca !== '' && stripos($busca, 'Pato Branco') === false) {
        $busca .= ', Pato Branco, PR';
    }

    $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=br&q=' . urlencode($busca);
    $dados = httpGetJson($url);

    if (empty($dados) || !isset($dados[0])) {
        throw new RuntimeException('Não foi possível localizar o endereço ou CEP informado.');
    }

    $item = $dados[0];

    return [
        'lat' => (float) ($item['lat'] ?? 0),
        'lon' => (float) ($item['lon'] ?? 0),
    ];
}

function rotaOsrm(float $lat1, float $lon1, float $lat2, float $lon2): float
{
    $url = 'https://router.project-osrm.org/route/v1/driving/'
        . $lon1 . ',' . $lat1 . ';' . $lon2 . ',' . $lat2
        . '?overview=false&alternatives=false';

    $dados = httpGetJson($url);

    if (empty($dados['routes'] ?? [])) {
        throw new RuntimeException('Não foi possível calcular uma rota viável até esse endereço.');
    }

    $distanciaMetros = (float) ($dados['routes'][0]['distance'] ?? 0);
    if ($distanciaMetros <= 0) {
        throw new RuntimeException('Distância inválida calculada pela rota.');
    }

    return $distanciaMetros / 1000;
}

try {
    $precoGasolina = (float) lerConfiguracao('preco_gasolina', '5.80');
    $origemCep = trim((string) lerConfiguracao('cep_origem', $ORIGEM_CEP));
    $origemCep = preg_replace('/\D+/', '', $origemCep);
    if (strlen($origemCep) !== 8) {
        $origemCep = $ORIGEM_CEP;
    }

    $inputBruto = file_get_contents('php://input');
    $dados = json_decode($inputBruto, true);

    if (!is_array($dados)) {
        throw new InvalidArgumentException('Dados de envio corrompidos ou inválidos.');
    }

    $endereco = trim((string) ($dados['endereco'] ?? ''));
    if ($endereco === '') {
        throw new InvalidArgumentException('Por favor, informe o endereço de entrega.');
    }

    $origem = geocodificarCep($origemCep);

    $cepDestino = preg_replace('/\D+/', '', $endereco);
    $dadosCepDestino = [];
    if (preg_match('/^\d{8}$/', $cepDestino)) {
        $dadosCepDestino = brasilApiCep($cepDestino);
    }

    if ($cepDestino !== '' && $cepDestino === $origemCep) {
        $distanciaKm = 0.0;
    } else {
        $destino = geocodificarEndereco($endereco);
        $distanciaKm = rotaOsrm($origem['lat'], $origem['lon'], $destino['lat'], $destino['lon']);
    }

    if ($distanciaKm < 0) {
        throw new RuntimeException('Erro crítico na verificação de quilometragem.');
    }

    if ($distanciaKm <= 0) {
        $valorTotal = 0.00;
    } else {
        $valorTotal = $distanciaKm * ($precoGasolina / 5.0);
    }

    echo json_encode([
        'sucesso' => true,
        'distancia' => round($distanciaKm, 1),
        'total' => number_format($valorTotal, 2, ',', '.'),
        'valor_numero' => round($valorTotal, 2),
        'fator' => $precoGasolina / 5.0,
        'rua' => $dadosCepDestino['street'] ?? '',
        'bairro' => $dadosCepDestino['neighborhood'] ?? '',
        'cidade' => $dadosCepDestino['city'] ?? '',
        'estado' => $dadosCepDestino['state'] ?? '',
        'mensagem' => 'Frete calculado com sucesso.',
    ]);
    exit;
} catch (Throwable $erro) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro' => $erro->getMessage(),
    ]);
    exit;
}
