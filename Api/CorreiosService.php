<?php

class CorreiosService
{
    /**
     * Atributes
     */
    private array  $services;
    private String $wsCorreios;
    private String $trackCorreios;

    /**
     * Construct function
     */
    public function __construct()
    {
        $this->services = array(
            'PAC'        => '04510',
            'SEDEX'      => '04014',
            'SEDEX 12'   => '04782',
            'SEDEX 10'   => '04790',
            'SEDEX Hoje' => '04804'
        );

        $this->wsCorreios = 'http://ws.correios.com.br/calculador/';
        $this->trackCorreios = 'https://www.linkcorreios.com.br';
    }


    /**
     * getServices function
     *
     * Retorna os serviços oferecidos pelos Correios.
     *
     * @return Array
     */
    public function getServices(): Array
    {
        return $this->services;
    }

    /**
     * getFrete function
     *
     * Calcula o frete dos Correios com base no tipo de serviço, origem e destino, e as dimensões do(s) itens.
     *
     * @return Array
     */
    public function getFrete(): Array
    {
        // Recupera os dados JSON no corpo da requisição
        $value = json_decode(file_get_contents('php://input'), TRUE);

        // Trata os CEPs retirando o traço caso haja
        $sender = str_replace('-', '', $value['origem']);
        $recipient = str_replace('-', '', $value['destinatario']);

        // Cria a URL
        $url = $this->wsCorreios . 'CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=' . $sender . '&sCepDestino=' . $recipient . '&nVlPeso=' . $value['peso'] . '&nCdFormato=' . $value['tipo'] . '&nVlComprimento=' . $value['comprimento'] . '&nVlAltura=' .
        $value['altura'] . '&nVlLargura=' . $value['largura'] . '&sCdMaoPropria=' . $value['maoPropria'] . '&nVlValorDeclarado=' . $value['valorDeclarado'] . '&sCdAvisoRecebimento=' . $value['avisoRecebimento'] . '&nCdServico=' . $value['servico'] . '&nVlDiametro=' . $value['diametro'] . '&StrRetorno=xml';

        // Executa a Conexão via Curl
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET'
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        // Trata o XML e retorna um array de dados
        $xml = simplexml_load_string($response);
        return json_decode(json_encode($xml, JSON_UNESCAPED_UNICODE), TRUE);
    }




    /**
     * tracking function
     *
     * Rastreia um objeto enviado pelos Correios. Esta função recebe um ou mais códigos de rastreio desde que estejam separados por ";" (ponto e vírgula).
     *
     * @param String $trackCode
     * @return Object
     */
    public function tracking(String $trackCode): Object
    {
        //aqui eu "quebro" a string para criar um array de encomendas, permitindo o recebimento de mais de uma encomenda
        //a ser pesquisada
        $obj = explode(";", $trackCode);

        $novo_array = array();

        //looping para executar a rotina para cada encomenda enviada
        for ($i = 0; $i < count($obj); $i++) {

            $output = file_get_contents($this->trackCorreios.'/'.$obj[$i]);
            $out    = explode("class=\"singlepost\"", $output);
            $out    = explode("<br>", $out[1]);
            $out    = explode("<ul class=\"linha_status\"", $out[1]);

            $y = 0;

            foreach ($out as $key => $value) {
              if(trim($value) != ""){
                $html  = str_replace(' style="">','<ul>', $value);
                $html  = str_replace('<b>', '', str_replace('</b>', '', $html));
                $array = explode('<li>', $html);

                $action   = str_replace('</ul>','', str_replace('</li>','', explode('-',trim(str_replace('Status:','',$array[1])))[0] ));
                $date     = str_replace('</ul>','', str_replace('</li>','', trim(str_replace(':','',str_replace('Data','',explode('|',$array[2])[0]))) ));
                $hour     = str_replace('</ul>','', str_replace('</li>','', trim(str_replace('Hora:','',explode('|',$array[2])[1])) ));

                if(!isset($array[1])){
                  $message = $action;
                }else{
                  $message  = str_replace('</ul>','', str_replace('</li>','', @explode('-',trim(str_replace('Status:','',$array[1])))[1] ));
                }

                $change   = str_replace('</ul>','', str_replace('</li>','', self::change($date) ));
                $location = str_replace('</ul>','', str_replace('</li>','', trim(str_replace('Origem:','', str_replace('Local:','',$array[3]))) ));

                $array_obj[$y] = array(
                  'date'     => $date,
                  'hour'     => $hour,
                  'location' => $location,
                  'action'   => $action,
                  'message'  => $message,
                  'change'   => $change
                );

                $y++;
              }
            }

            $novo_array[$obj[$i]] = (object)$array_obj;

        }

        return (object)$novo_array;

    }


    public function change($dia){
      $exploDate = explode('/', $dia);
      $dia1 = $exploDate[2] . '-' . $exploDate[1] . '-' . $exploDate[0];
      $dia2 = date('Y-m-d');

      $diferenca = strtotime($dia2) - strtotime($dia1);
      $dias = floor($diferenca / (60 * 60 * 24));

      if($dias>1){
        $change = "há {$dias} dias";
      }else{
        $change = "há {$dias} dia";
      }
      return $change;
    }

    /**
     * getSiglas function
     *
     * Retorna a descrição das Siglas utilizadas nos códigos de rastreio. Esta função retorna todas as siglas ou uma específica
     *
     * @param String $sigla
     * @return Array
     */
    public function getSiglas(String $sigla = ''): Array
    {
        $dados = file_get_contents(__DIR__.'/siglas_rastreio.json');

        $dados = json_decode($dados, TRUE);

        return !empty($sigla) ? array($sigla => $dados[$sigla]) : $dados;
    }
}
