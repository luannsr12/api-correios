<?php

/**
 * Arquivo de Configuração
 * 
 * Aqui será configurado as constantes:
 * 
 * DIRPAGE: Url da aplicação;
 * DIRREQ: Caminho físico da aplicação;
 * ENVIRONMENT: Tipo do ambiente da aplicação
 */

// Caso a plicação fique num diretório diferenta da rais do servidor
$PastaInterna="projeto/correios-rastreio/";

// Url da aplicação. Ex: http://localhost/projeto/correios-rastreio/
define('DIRPAGE',"http://{$_SERVER['HTTP_HOST']}/{$PastaInterna}");

// Caminho físico da aplicação. Ex: /var/www/html/projeto/correios-rastreio/
if(substr($_SERVER['DOCUMENT_ROOT'],-1)=='/') {
	define('DIRREQ',"{$_SERVER['DOCUMENT_ROOT']}{$PastaInterna}");
}
else {
	define('DIRREQ',"{$_SERVER['DOCUMENT_ROOT']}/{$PastaInterna}");
}

// Tipo do ambiente da aplicação. Ex: Production, homologation, Development
define('ENVIRONMENT', 'development');