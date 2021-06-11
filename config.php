<?php

#Arquivos diretórios raízes
$PastaInterna="projeto/correios-rastreio/";

define('DIRPAGE',"http://{$_SERVER['HTTP_HOST']}/{$PastaInterna}");

if(substr($_SERVER['DOCUMENT_ROOT'],-1)=='/') {
	define('DIRREQ',"{$_SERVER['DOCUMENT_ROOT']}{$PastaInterna}");
}
else {
	define('DIRREQ',"{$_SERVER['DOCUMENT_ROOT']}/{$PastaInterna}");
}

define('ENVIRONMENT', 'development');