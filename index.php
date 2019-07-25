<?php

 // Para buscar o status do objeto , basta enviar uma requisição
 // para o arquivo " obj.php " dentro de " api " .
 // O arquivo " obj.php " buscara o status do objeto no próprio site dos correios

 // PHP >= 5.6
 // @author : Luan Alves
 // Jul / 2019


 $obj = "OH215715636BR";
 $url = "http://localhost/rastreio/api/obj.php?obj={$obj}";
 $rastreio = json_decode(file_get_contents($url));

 echo '<pre>';
 var_dump($rastreio);




?>
