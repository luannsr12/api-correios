<?php

class Route{
    // Atributos
    private static $Rota;
    private static $Sufixo = 'Service';

    /**
     * getService function
     * 
     * Verifica se o Serviço (Controlador) fornecido pela requisição existe e, se sim, retorna o nome do Servico
     *
     * @param String $indice
     * @return String
     */
    public static function getService(String $indice): String {
        // Nomes dos Serviços disponíveis para utilização
        self::$Rota = array(
            'correios' => 'Correios'.self::$Sufixo,
        );

        // Verifica se existe o indice
        if(array_key_exists($indice, self::$Rota)){
            // Verifica se existe o Serviço
            if(file_exists(DIRREQ."Api/".self::$Rota[$indice].".php")){
                return self::$Rota[$indice];
                
            }

            // Caso não exista o serviço gera uma exceção
            http_response_code(404);
            throw new \Exception('Request not found');
            return FALSE;
        }

        // Caso não exista o indice gera uma exceção
        http_response_code(400);
        throw new \Exception('Bad Request');
        return FALSE;
    }

    /**
     * getMethod function
     * 
     * Verifica se o Método fornecido pela requisição existe e, se sim, retorna o nome do método
     *
     * @param String $service
     * @param String $method
     * @return String
     */
    public static function getMethod(String $service, String $method): String {
        // Verifica se o método existe
        if(method_exists(new $service(), $method)){
            return $method;
        }

        // Caso não exista gera uma exceção
        http_response_code(404);
        throw new \Exception('Request not found');
        return FALSE;
    }

    /**
     * getArgs function
     *
     * Trata os argumentos enviados na requisição
     * 
     * @param Array $url
     * @return Array
     */
    public static function getArgs(Array $url):Array {
        array_shift($url);
        array_shift($url);

        return $url;

    }
}