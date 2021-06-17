<?php

class Route{
    // Atributos
    private static $Rota;
    private static $Sufixo = 'Service';

    
    public static function getService(String $indice):String {

        self::$Rota = array(
            'correios' =>'Correios'.self::$Sufixo,
        );

        if(array_key_exists($indice, self::$Rota)){
            if(file_exists(DIRREQ."Api/".self::$Rota[$indice].".php")){
                return self::$Rota[$indice];
                
            }

            http_response_code(404);
            throw new \Exception('Request not found');
            return FALSE;
        }

        http_response_code(400);
        throw new \Exception('Bad Request');
        return FALSE;
    }

    public static function getMethod(String $service, String $method):String {
        if(method_exists(new $service(), $method)){
            return $method;
        }

        http_response_code(404);
        throw new \Exception('Request not found');
        return FALSE;
    }

    public static function getArgs(Array $url):Array {
        array_shift($url);
        array_shift($url);

        return $url;

    }
}