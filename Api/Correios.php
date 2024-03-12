<?php
  
use Sdkcorreios\Config\Services;
use Sdkcorreios\Methods\Tracking;

class Correios
{
    /**
     * Atributes
     */
    private array  $services;
    private String $wsCorreios;
    private String $trackCorreios;
    private bool $authorization = false;
    private string $access_token;

    /**
     * Construct function
     */
    public function __construct($access_token)
    {
        $this->access_token = $access_token;
        $this->auth();
        
        $this->services = array(
            'PAC'        => '04510',
            'SEDEX'      => '04014',
            'SEDEX 12'   => '04782',
            'SEDEX 10'   => '04790',
            'SEDEX Hoje' => '04804'
        );

        $this->wsCorreios = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
        $this->trackCorreios = 'https://www.linkcorreios.com.br';
    }

    public function auth(){
        
        if($this->access_token == ACCESS_TOKEN){
            $this->authorization = true;
        }else{
           die(json_encode(["success" => false, "message" => "Invalid token - Unauthorized"]));
        }
    }


    /**
     * getServices function
     *
     * Retorna os serviços oferecidos pelos Correios.
     *
     * @return String
     */
    public function services(): String
    {   
        return json_encode(["success" => true, "result" => $this->services]);
    }

    /**
     * getFrete function
     *
     * Calcula o frete dos Correios com base no tipo de serviço, origem e destino, e as dimensões do(s) itens.
     *
     * @return String
     */
    public function calculate($headers, $params): String
    {

        // Recupera os dados JSON no corpo da requisição
        $value = json_decode(file_get_contents('php://input'), TRUE);

        // Trata os CEPs retirando o traço caso haja
        $sender = str_replace('-', '', $params['origem']);
        $recipient = str_replace('-', '', $params['destinatario']);

        $paramsData = [
            'nCdEmpresa'            => "",
            'sDsSenha'              => "",
            'sCepOrigem'            => $sender,
            'sCepDestino'           => $recipient,
            'nVlPeso'               => $params['peso'],
            'nCdFormato'            => 1,
            'nVlComprimento'        => $params['comprimento'],
            'nVlAltura'             => $params['altura'],
            'nVlLargura'            => $params['largura'],
            'sCdMaoPropria'         => $params['maoPropria'],
            'nVlValorDeclarado'     => $params['valorDeclarado'],
            'sCdAvisoRecebimento'   => $params['avisoRecebimento'],
            'nCdServico'            => isset($params['servico']) ? $params['servico']: '04510',
            'nVlDiametro'           => $params['diametro'],
            'StrRetorno'            => 'xml',
            'nIndicaCalculo'        => 3
        ];

        $queryString = http_build_query($paramsData);

        $url = "{$this->wsCorreios}?{$queryString}";

        $cURL = curl_init();
        curl_setopt_array($cURL, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        $respostaWebservice = curl_exec($cURL);
        curl_close($cURL);

        // Trata o XML e retorna um array de dados
        $xml = simplexml_load_string($respostaWebservice);
        $result = json_decode(json_encode($xml, JSON_UNESCAPED_UNICODE), TRUE);
        
        return json_encode(["success" => true, "result" => $result]);

    }


    public function tracking( $headers, $params)
    {

        //aqui eu "quebro" a string para criar um array de encomendas, permitindo o recebimento de mais de uma encomenda
        //a ser pesquisada
 
        if(!isset($params['objects'])){ return json_encode(["success" => false, "message" => 'objects is required']); }
        if($params['objects']  == ""){ return json_encode(["success" => false, "message" => 'objects is required']); }

        $trackCodes = $params['objects'];
        $provider   = isset($params['provider']) ? $params['provider'] : "MelhorRastreio";

        Services::setServiceTracking($provider);
        Services::setDebug(false);

        if(Services::$success){

            try {

                $objetcs  = implode(',', $trackCodes);
                $tracking = new Tracking();
                $tracking->setCode($objetcs);
                $result  = $tracking->get();

                if(Services::$success && $result){
                    return json_encode($result);
                }else{
                    return json_encode(["success" => false, "message" => Services::getMessageError()]);
                }
                
                
            } catch (\Throwable $th) {
               return json_encode(["success" => false, "message" => Services::getMessageError()]);
            }
           
        }else{
            return json_encode(["success" => false, "message" => Services::getMessageError()]);
        }

    }


    public function flags($headers, $params)
    {
        $dados = file_get_contents(__DIR__.'/siglas_rastreio.json');
        $dados = json_decode($dados, TRUE);

        return !empty($dados) ? json_encode(["success" => true, "result" => $dados]) : json_encode(["success" => false, "message" => "Not found"]);

    }

    public function flag($headers, $params)
    {

        if(!isset($params['Id'])){
            return json_encode(["success" => false, "message" => 'Id is required']);
        }

        $sigla = $params['Id'];
        $dados = file_get_contents(__DIR__.'/siglas_rastreio.json');
        $dados = json_decode($dados, TRUE);
        
        if(isset($dados[$sigla])){
            return json_encode(["success" => true, "result" => ["Id" => $sigla, "Name" => $dados[$sigla]['name']]]);
        }       
        
        return json_encode(["success" => false, "message" => 'Flag not found']);

    }

}
