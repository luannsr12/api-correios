

# API Correios com PHP v2.0

[![](https://img.shields.io/github/contributors/luannsr12/correios-rastreio.svg?style=flat-square)](https://github.com/luannsr12/correios-rastreio/graphs/contributors)
[![](https://badges.pufler.dev/updated/luannsr12/correios-rastreio)](https://github.com/luannsr12/correios-rastreio)
[![](https://badges.pufler.dev/visits/luannsr12/correios-rastreio)](https://github.com/luannsr12/correios-rastreio)


# Requirements

- Composer
- PHP >= 8.2

# Packages
- [luannsr12/sdkcorreios](https://packagist.org/packages/luannsr12/sdkcorreios)

# Esta API aborda os seguintes serviços:

1. Serviços oferecidos pelos Correios;
2. Cálculo de Frete de um produto;
3. Rastreio de uma encomenda;
4. Retorna os significados das siglas contidas no código de rastreio.

# Como utilizar a API

Segue abaixo as instruções de utilização:
```
 git clone https://github.com/luannsr12/api-correios/
 cd api-correios
 composer install
```
> Após o download e instalação, edite o arquivo "config.php" e adicione o Access Token da sua aplicação.
> Esse Access Token deve ser passado no header das requisições da API.

```php
<?php

 // Usado para comsumir a API
 define("ACCESS_TOKEN", "SEU_ACCESS_TOKEN");

```

# Todos os endpoints
----

# Rastrear encomenda

Endpoint: /correios/tracking/
<br />
Method: POST

Curl:
```
curl --location --request GET '{{base_url}}/correios/tracking' \
--header 'Access-token: SEU_ACCESS_TOKEN' \
--header 'Content-Type: application/json' \
--data '{
  "objects": ["QQ588651634BR"],
  "provider": "Muambator"
}'
```

Body:
> provider é opcional. Por padrão o site de busca é o Melhor Rastreio
> Consule na documentação da SDK para ver os providers: [Sdk Correios](https://github.com/luannsr12/sdkcorreios/)

```json
{
  "objects": ["QQ588651634BR"],
  "provider": "Muambator"
}
```

Response:

```json
{
    "success": true,
    "result": [
        {
            "code": "QQ588651634BR",
            "status": "MOVEMENT",
            "service_provider": "api.melhorrastreio.com.br",
            "data": [
                {
                    "date": "03-09-2024 07:27:10",
                    "to": "Unidade de Distribuição - MARECHAL CANDIDO RONDON/PR",
                    "from": "Unidade de Tratamento - CURITIBA/PR",
                    "location": "Unidade de Tratamento - CURITIBA/PR",
                    "originalTitle": "Objeto em transferência - por favor aguarde",
                    "details": "Objeto em transferência - por favor aguarde"
                },
                {
                    "date": "03-06-2024 12:25:05",
                    "to": "Unidade de Tratamento - CASCAVEL/PR",
                    "from": "Unidade de Tratamento - SOROCABA/SP",
                    "location": "Unidade de Tratamento - SOROCABA/SP",
                    "originalTitle": "Objeto em transferência - por favor aguarde",
                    "details": "Objeto em transferência - por favor aguarde"
                }
            ]
        }
    ]
}
```

# Tipos de serviços

Endpoint: /correios/services/
<br />
Method: GET

Curl:

```
curl --location '{{base_url}}/correios/services' \
--header 'Access-token: SEU_ACCESS_TOKEN'

```
 
Response: 

```json
{
  "status": "sucess",
  "result": {
    "PAC": "04510",
    "SEDEX": "04014",
    "SEDEX 12": "04782",
    "SEDEX 10": "04790",
    "SEDEX Hoje": "04804"
  }
}
```

# Calcular Frete

Endpoint: /correios/calculate/
<br />
Method: GET

1. Para o tipo de encomenda, segue as definições abaixo:
    - Caixa/Pacote (1);
    - Rolo/Prisma (2);
    - Envelope (3);

2. Observações importantes:
    - Caso seja Envelope, informe 0 na altura;
    - Caso seja Envelope, o peso não pode ultrapassar 1kg;
    - Caso seja Rolo/Prisma, informe o diâmetro da embalagem;
    - Caso seja Rolo/Prisma, informe 0 na altura e largura;
    - Caso seja Caixa/Pacote, informe 0 no diâmetro;
    - Para os serviços de "Mão Própria" e/ou "Aviso de Recebimento" informe "S" - sim ou "N" - não;
    - O valor Declarado é opcional, sendo que caso não deseje declarar, informe 0;

Curl:
```
curl --location '{{base_url}}/correios/calculate' \
--header 'Access-token: SEU_ACCESS_TOKEN' \
--header 'Content-Type: application/json' \
--data '{
	"servico": "04510",
	"origem": "85930-000",
	"destinatario": "85960-000",
	"tipo": 1,
	"comprimento": 20,
	"altura": 20,
	"largura": 20,
	"diametro": 0,
	"peso": 0.500,
	"maoPropria": "s",
	"valorDeclarado": 150,
	"avisoRecebimento": "s"
}'

```


Body:

```json
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

Response:

```json
{
  "status": "sucess",
  "result": {
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

# Obter a sigla pelo ID

Endpoint: /correios/flag/
<br />
Method: GET

Curl:
```
curl --location --request GET '{{base_url}}/correios/flag' \
--header 'Access-token: SEU_ACCESS_TOKEN' \
--header 'Content-Type: application/json' \
--data '{
    "Id": "AR"
}'
```

Body:
```json
{
    "Id": "AR"
}
```

Response:

```json
{
    "success": true,
    "result": {
        "Id": "AR",
        "Name": "AVISO DE RECEBIMENTO"
    }
}
```

# Listar todas as siglas

Endpoint: /correios/flags/
<br />
Method: GET

Curl:
```
curl --location '{{base_url}}/correios/flags' \
--header 'Access-token: SEU_ACCESS_TOKEN'

```

Response:

```json
{
    "success": true,
    "result": {
        "AL": {
            "name": "AGENTES DE LEITURA"
        },
        "AR": {
            "name": "AVISO DE RECEBIMENTO"
        },
        "AS": {
            "name": "ENCOMENDA PAC – ACAO SOCIAL"
        },
        "BE": {
            "name": "REMESSA ECONÔMICA S/ AR DIGITAL"
        },
        "BF": {
            "name": "REMESSA EXPRESSA S/ AR DIGITAL"
        }
    }
}
```


## Contribuidores

| [<img alt="luannsr12" src="https://github.com/luannsr12.png?size=115" width="115"><br><sub>@luannsr12</sub>](https://github.com/luannsr12) | [<img alt="germano-rs" src="https://github.com/germano-rs.png?size=115" width="115"><br><sub>@germano-rs</sub>](https://github.com/germano-rs) | [<img alt="pauloalmeidasilva" src="https://github.com/pauloalmeidasilva.png?size=115" width="115"><br><sub>@pauloalmeidasilva</sub>](https://github.com/pauloalmeidasilva) |
| :---: |:---: |:---: 
