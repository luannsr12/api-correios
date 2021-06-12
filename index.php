<?php
/**
 * Chamada dos arquivos necessários para API.
 */
require_once 'config.php';
require_once 'api/ApiCorreios.php';

// Habilita a apresentação de erros caso seja ambiente de desenvolvimento
if (ENVIRONMENT == 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);
}

// Cabeçalho da Requisição informando que a resposta será em JSON
header('Content-Type: application/json');

// URL base
$apiUrl = DIRPAGE.'api';

// Chamada da Classe
$api = new ApiCorreios();

// Recupera os serviços oferecidos pelos Correios
$servicos = $api->getServices();
echo $servicos;

/**
 * Exemplo de Encomenda para o cálculo do frete
 * 
 * Para o tipo de encomenda, segue as definições abaixo:
 * 1 - Caixa/Pacote;
 * 2 - Rolo/Prisma;
 * 3 - Envelope;
 * 
 * OBSERVAÇÔES IMPORTANTES:
 * => Caso seja Envelope, informe 0 na altura;
 * => Caso seja Envelope, o peso não pode ultrapassar 1kg;
 * 
 * => Caso seja Rolo/Prisma, informe o diâmetro da embalagem;
 * => Caso seja Rolo/Prisma, informe 0 na altura e largura;
 * 
 * => Caso seja Caixa/Pacote, informe 0 no diâmetro;
 * 
 * => Para os serviços de "Mão Própria" e/ou "Aviso de Recebimento" informe "S" - sim ou "N" - não;
 * 
 * => O valor Declarado é opcional, sendo que caso não deseje declarar, informe 0;
 */
$produto = array(
    'tipo'              => 1,
    'comprimento'       => 20,
    'altura'            => 20,
    'largura'           => 20,
    'diametro'          => 0,
    'peso'              => 0.500,
    'maoPropria'        => 's',
    'valorDeclarado'    => 150,
    'avisoRecebimento'  => 's'
);

// Recupera o cálculo do frete
$retorno = $api->getFrete('04510', '12460-000', '37530-000', $produto);
echo $retorno;

// Recupera o rastreio
$rastreio = $api->tracking('QE460690785BR');
echo $rastreio;

// Recupera as siglas
$rastreio = $api->getSiglas();
echo $rastreio;

$rastreio = $api->getSiglas('AL');
echo $rastreio;
