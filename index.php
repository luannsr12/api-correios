<?php
require_once "config.php";

if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);
}

$apiUrl = DIRPAGE.'api';

// Rastreio objeto - Para mais código adicionar ;
$obj      = "QE460690785BR";
$rastreio = file_get_contents("{$apiUrl}/obj.php?obj={$obj}");  // Json return

// Nome do envio conforme a sigla do código
$json          = json_decode(file_get_contents("{$apiUrl}/siglas_rastreio.json"));
$sigla         = substr($obj, 0, 2);
$tipoEncomenda = $json->$sigla->name; // SEDEX
