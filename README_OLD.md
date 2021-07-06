# API Correios com PHP

## Introdução

Esta API aborda os seguintes serviços:

* Serviços oferecidos pelos Correios;
* Cálculo de Frete de um produto;
* Rastreio de uma encomenda;
* Retorna os significados das siglas contidas no código de rastreio.

## Como utilizar a API

Segue abaixo todas as requisições que podem ser feitas nesta API:

**URL_BASE: http://localhost/**

### GET /correios/getServices

**Parâmetros**

Nenhum

**Retorno**

```
{
  "status": "sucess",
  "content": {
    "PAC": "04510",
    "SEDEX": "04014",
    "SEDEX 12": "04782",
    "SEDEX 10": "04790",
    "SEDEX Hoje": "04804"
  }
}
```

### GET /correios/getFrete

**Parâmetros**

Passados no corpo da requisição especificando o conteudo como JSON

```
{
  "servico": "04510",
  "origem": "12460-000",
  "destinatario": "37530-000",
  "tipo": 1,
  "comprimento": 20,
  "altura": 20,
  "largura": 20,
  "diametro": 0,
  "peso": 0.500,
  "maoPropria": "s",
  "valorDeclarado": 150,
  "avisoRecebimento": "s"
}
```

**Retorno**

```
{
  "status": "sucess",
  "content": {
    "cServico": {
      "Codigo": "04510",
      "Valor": "45,83",
      "PrazoEntrega": "11",
      "ValorSemAdicionais": "29,40",
      "ValorMaoPropria": "7,50",
      "ValorAvisoRecebimento": "6,35",
      "ValorValorDeclarado": "2,58",
      "EntregaDomiciliar": "S",
      "EntregaSabado": "N",
      "obsFim": [],
      "Erro": "0",
      "MsgErro": []
    }
  }
}
```

### GET /correios/tracking/{CODIGO_RASTREIO}

**Parâmetros**

Nenhum

**Retorno**

```
{
  "status": "sucess",
  "content": {
    "QE460690785BR": {
      "0": {
        "date": "19\/04\/2021",
        "hour": "16:22",
        "location": "CAMPOS DO JORDAO\/SP",
        "action": "Objeto entregue ao destinatário",
        "message": "Objeto entregue ao destinatário ",
        "change": "há 59 dias"
      },
      "1": {
        "date": "19\/04\/2021",
        "hour": "14:41",
        "location": "CAMPOS DO JORDAO\/SP",
        "action": "Objeto saiu para entrega ao destinatário",
        "message": "Objeto saiu para entrega ao destinatário ",
        "change": "há 59 dias"
      },
      "2": {
        "date": "16\/04\/2021",
        "hour": "15:16",
        "location": "SAO JOSE DOS CAMPOS \/ SP",
        "action": "Objeto em trânsito - por favor aguarde",
        "message": "Objeto em trânsito - por favor aguarde  de Unidade de Tratamento em SAO JOSE DOS CAMPOS \/ SP para Unidade de Distribuição em CAMPOS DO JORDAO \/ SP",
        "change": "há 62 dias"
      },
      "3": {
        "date": "14\/04\/2021",
        "hour": "00:42",
        "location": "CAMPO GRANDE \/ MS",
        "action": "Objeto em trânsito - por favor aguarde",
        "message": "Objeto em trânsito - por favor aguarde  de Unidade de Tratamento em CAMPO GRANDE \/ MS para Unidade de Tratamento em SAO JOSE DOS CAMPOS \/ SP",
        "change": "há 64 dias"
      },
      "4": {
        "date": "13\/04\/2021",
        "hour": "16:48",
        "location": "TRES LAGOAS \/ MS",
        "action": "Objeto em trânsito - por favor aguarde",
        "message": "Objeto em trânsito - por favor aguarde  de Agência dos Correios em TRES LAGOAS \/ MS para Unidade de Tratamento em CAMPO GRANDE \/ MS",
        "change": "há 65 dias"
      },
      "5": {
        "date": "13\/04\/2021",
        "hour": "16:42",
        "location": "TRES LAGOAS\/MS",
        "action": "Objeto postado",
        "message": "Objeto postado ",
        "change": "há 65 dias"
      }
    }
  }
}
```

### GET /correios/getSiglas/{SIGLA}

O parâmetro SIGLA é opcional

#### Caso não passe o parametro SIGLA

**Parâmetros**

Nenhum

**Retorno**

```
{
  "status": "sucess",
  "content": {
    "AL": {
      "name": "AGENTES DE LEITURA"
    },
    "AR": {
      "name": "AVISO DE RECEBIMENTO"
    },
    "AS": {
      "name": "ENCOMENDA PAC – ACAO SOCIAL"
    },

    .
    .
    .
  }
}
```

#### Caso passe o parametro SIGLA

**Parâmetros**

Nenhum

**Retorno**

```
{
  "status": "sucess",
  "content": {
    "AL": {
      "name": "AGENTES DE LEITURA"
    }
  }
}
```


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
