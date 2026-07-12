<?php
    namespace Controllers;

use PDO;

    class infosController
    {
        protected function BDlog(){
            try {
                    $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');

                } catch (\Exception $mnsg) {
                    echo "<li>";
                    echo "Erro ao conectar oa banco: ". $mnsg->getMessage();
                    echo "</li>";
                }

                return $BD;
        }

        public function pageInfo(int $id){
            $BD = new infosController;
            $BD = $BD->BDlog();

            $query = $BD->prepare('SELECT * FROM produtos where id = :id;');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $retorno = $query->fetch(PDO::FETCH_ASSOC);
            return $retorno;
        }
    }
?>