<?php

/**
 * Chamada dos arquivos necessários para API.
 */
require_once 'config.php';
require_once 'Src/Route.php';


// Habilita a apresentação de erros caso seja ambiente de desenvolvimento
if (ENVIRONMENT == 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);
}

// Cabeçalho da Requisição informando que a resposta será em JSON
header('Content-Type: application/json');

// Executa a requisição
try {
    // Recupera a rota
    $url = explode("/", rtrim($_GET['url']));

    // Verifica a Rota e retorna a service
    $service = Route::getService($url[0]);

    // Chama a Service
    require 'Api/' . $service . '.php';

    $method = Route::getMethod($service, $url[1]);
    $args = Route::getArgs($url);

    $response = call_user_func_array(array(new $service, $method), $args);

    echo json_encode(array('status' => 'sucess', 'content' => $response));
} catch (\Exception $e) {
    // Tratamento de Exceção caso algum parâmetro não condiza com a API
    echo json_encode(array('status' => 'error', 'content' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
}
