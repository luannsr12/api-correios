# Rastreio de objetos Correios com PHP

As requisições são enviadas para " api/obj.php "

### Buscando status do objeto
```php
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
```

### Resposta JSON
```json

 { "0": {
     "date":"10/06/2019",
     "hour":"14:14",
     "location":"SAO PAULO / SP",
     "action":"Objeto encaminhado",
     "message":"Objeto encaminhado  de Agência dos Correios em SAO PAULO / SP para Unidade de Tratamento em SAO PAULO / SP",
     "change":"há 2 dias"
      }
}

```

### JSON Siglas rastreio
```json

 { 
   "PI": {
      "name": "ENCOMENDA PAC"
   }
 }

```

## Contribuidores

| [<img src="https://github.com/luannsr12.png?size=115" width=115><br><sub>@luannsr12</sub>](https://github.com/luannsr12) | [<img src="https://github.com/germano-rs.png?size=115" width=115><br><sub>@germano-rs</sub>](https://github.com/germano-rs) |
| :---: | :---: |
