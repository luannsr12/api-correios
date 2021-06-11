<?php
require_once 'config.php';
require_once 'api/ApiCorreios.php';

if (ENVIRONMENT == 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);
}

// header('Content-Type: application/json');

$apiUrl = DIRPAGE.'api';

$produto = array(
    'tipo'              => 1,
    'comprimento'       => 20,
    'altura'            => 20,
    'largura'           => 20,
    'diametro'          => 0,
    'peso'              => 0.500,
    'maoPropria'        => 'n',
    'valorDeclarado'    => 0,
    'avisoRecebimento'  => 'n'
);

$api = new ApiCorreios();

// $servicos = $api->getServices();
// echo $servicos;

// $retorno = $api->getFrete('04510', '12460-000', '37530-000', $produto);
// echo $retorno;

// $rastreio = $api->tracking('QE460690785BR');
// echo $rastreio;

$rastreio = $api->getSiglas('AL');
echo $rastreio;

// // Rastreio objeto - Para mais código adicionar ;
// $obj      = "QE460690785BR";
// $rastreio = file_get_contents("{$apiUrl}/obj.php?obj={$obj}");  // Json return

// // Nome do envio conforme a sigla do código
// $json          = json_decode(file_get_contents("{$apiUrl}/siglas_rastreio.json"));
// $sigla         = substr($obj, 0, 2);
// $tipoEncomenda = $json->$sigla->name; // SEDEX
