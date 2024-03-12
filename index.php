<?php

  /* 
  *
  * Api correios
  * @Author: Luan Alves
  * @Repo: https://github.com/luannsr12/api-correios
  * @Version: 2.0.0
  *
  */

  require_once 'config.php';
  require_once 'vendor/autoload.php';
    
  header("Content-type: application/json; charset=utf-8");
  date_default_timezone_set(TIME_ZONE);

  if(isset($_REQUEST['url'])){
 
    $url    = explode('/',$_REQUEST['url']);
    $classe = trim(ucfirst($url[0]));
    array_shift($url);
    $metodo = trim($url[0]);
    array_shift($url);

    $params = $_REQUEST;
    $params['rest'] = $url;

    $bd = json_decode(file_get_contents('php://input'), true);

    if($bd){
        if(count($bd) > 0){
            $params = array_merge($bd, $params);
        }
    }
 
    if(is_file("Api/{$classe}.php")){

      require_once "Api/{$classe}.php";

      if(class_exists($classe)){

        if(method_exists($classe,$metodo)){

            try {

              $headers    = getallheaders();

              if(!isset($headers['Access-token'])){
                die(json_encode(array('success' => false, 'message' => 'Access Token required')));
              }

              $class_open = new $classe($headers['Access-token']);
             
              $execute    = $class_open->$metodo($headers,$params);
              
              echo $execute;

            } catch (\Exception $e) {
              echo json_encode(array('success' => false, 'message' => 'Error application'));
            }

        }else{
          echo json_encode(array('success' => false, 'message' => 'Method not exists'));
        }

      }else{
        echo json_encode(array('success' => false, 'message' => 'Method not exists'));
      }

    }else{
      echo json_encode(array('success' => false, 'message' => 'Method not exists'));
    }

  }
