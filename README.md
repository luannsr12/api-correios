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


 $obj = "CODIGO DE RASTREIO;CODIGO DE RASTREIO";
 $url = "http://localhost/rastreio/api/obj.php?obj={$obj}";
 $rastreio = json_decode(file_get_contents($url));

 echo '<pre>';
 var_dump($rastreio);




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

## Contribuidores

| [<img alt="murilohns" src="https://github.com/germano-rs.png?size=115" width="115"><br><sub>@germano-rs</sub>](https://github.com/germano-rs) |

## Autor

| [<img src="https://github.com/luannsr12.png?size=115" width=115><br><sub>@luannsr12</sub>](https://github.com/luannsr12) |
