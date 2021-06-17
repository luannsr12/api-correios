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
        $this->trackCorreios = 'https://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm';
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
        $url = $this->wsCorreios . 'CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=' . $sender . '&sCepDestino=' . $recipient . '&nVlPeso=' . $value['peso'] . '&nCdFormato=' . $value['tipo'] . '&nVlComprimento=' . $value['comprimento'] . '&nVlAltura=' . $value['altura'] . '&nVlLargura=' . $value['largura'] . '&sCdMaoPropria=' . $value['maoPropria'] . '&nVlValorDeclarado=' . $value['valorDeclarado'] . '&sCdAvisoRecebimento=' . $value['avisoRecebimento'] . '&nCdServico=' . $value['servico'] . '&nVlDiametro=' . $value['diametro'] . '&StrRetorno=xml';

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

        //looping para executar a rotina para cada encomenda enviada
        for ($i = 0; $i < count($obj); $i++) {

            $post = array('Objetos' => $obj[$i]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->trackCorreios);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            $output = curl_exec($ch);
            curl_close($ch);

            $out = explode("table class=\"listEvent sro\">", $output);

            if (isset($out[1])) {
                $output = explode("<table class=\"listEvent sro\">", $output);
                $output = explode("</table>", $output[1]);
                $output = str_replace("</td>", "", $output[0]);
                $output = str_replace("</tr>", "", $output);
                $output = str_replace("<strong>", "", $output);
                $output = str_replace("</strong>", "", $output);
                $output = str_replace("<tbody>", "", $output);
                $output = str_replace("</tbody>", "", $output);
                $output = str_replace("<label style=\"text-transform:capitalize;\">", "", $output);
                $output = str_replace("</label>", "", $output);
                $output = str_replace("&nbsp;", "", $output);
                $output = str_replace("<td class=\"sroDtEvent\" valign=\"top\">", "", $output);
                $output = explode("<tr>", $output);


                $novo_array = array();

                foreach ($output as $texto) {
                    $info   = explode("<td class=\"sroLbEvent\">", $texto);
                    $dados  = explode("<br />", $info[0]);

                    $dia   = trim($dados[0]);
                    $hora  = trim(@$dados[1]);
                    $local = trim(@$dados[2]);

                    $dados = explode("<br />", @$info[1]);
                    $acao  = trim($dados[0]);

                    $exAction   = explode($acao . "<br />", @$info[1]);
                    $acrionMsg  = strip_tags(trim(preg_replace('/\s\s+/', ' ', $exAction[0])));

                    if ("" != $dia) {
                        $exploDate = explode('/', $dia);
                        $dia1 = $exploDate[2] . '-' . $exploDate[1] . '-' . $exploDate[0];
                        $dia2 = date('Y-m-d');

                        $diferenca = strtotime($dia2) - strtotime($dia1);
                        $dias = floor($diferenca / (60 * 60 * 24));

                        $change = utf8_encode("há {$dias} dias");

                        $novo_array[] = array("date" => $dia, "hour" => $hora, "location" => $local, "action" => utf8_encode($acao), "message" => utf8_encode($acrionMsg), "change" => utf8_decode($change));
                    }
                }
            } else {
                $jsonObcject = new stdClass();
                $jsonObcject->erro = true;
                $jsonObcject->msg = "Objeto não encontrado";
                $jsonObcject->obj = $obj[$i];
                return $jsonObcject;
            }
            //cria um objeto identificando o código da encomenda e as informações extraídas armazenadas no array $novo_array
            $arrayCompleto[$obj[$i]] = (object)$novo_array;
        }

        $jsonObcject = (object)$arrayCompleto;
        return $jsonObcject;
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
