<?php

 // Para buscar o status do objeto , basta enviar uma requisição
 // para o arquivo " obj.php " dentro de " api " .
 // O arquivo " obj.php " buscara o status do objeto no próprio site dos correios

 // PHP >= 5.6
 // @author : Luan Alves
 // Jul / 2019

 // Api Url
 $apiUrl = "http://localhost/rastreio/api";


 // Rastreio objeto - Para mais código adicionar ;
 $obj      = "CODIGO DE RASTREIO";
 $rastreio = file_get_contents("{$apiUrl}/obj.php?obj={$obj}");  // Json return

 // Nome do envio conforme a sigla do código
 $json          = json_decode(file_get_contents("{$apiUrl}/siglas_rastreio.json"));
 $sigla         = substr($obj,0,2);
 $tipoEncomenda = $json->$sigla->name; // SEDEX



?>
