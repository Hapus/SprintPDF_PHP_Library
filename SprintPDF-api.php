<?php

 class sprintPDFException extends Exception {};

    class sprintPDF {
       private $parameters = array();
       private $gateway = 'http://www.sprintpdf.com/';
       private $apiVersion = 'api';
       private $resourceType = 'pdf';
       private $url = "";
       private $outstream  = NULL;
       private $responseFunction = "defaultResponse";
       function __construct( $username ,$key ,$gateway = null , $apiVersion = null ) {
          $this->parameters = array(  'key' => $key,'username' => $username);
          if($gateway){
            $this->gateway = $gateway;
          }
          if($apiVersion){
            $this->apiVersion = $apiVersion;
          }
          $this->url = $this->gateway.$this->apiVersion.'/'.$this->resourceType;
       }

       function convertURI( $src, $outstream = null,$apiParameters = null ) {
        $this->outstream =$outstream;
        $this->parameters += array("resource" => $src, "type" => "url","parameters" => $apiParameters );
        return $this->curlHttpRequest('POST', "fileResponse");
       }


       function convertHTML($src, $outstream = null,$apiParameters = null ){
        $this->outstream =$outstream;
        $this->parameters += array("resource" => $src, "type" => "html","parameters" => $apiParameters);
        return $this->curlHttpRequest('POST', "fileResponse");
       }

       function getInfo() {
        $this->url = $this->gateway.$this->apiVersion.'/me/info?'.
        'username='.$this->parameters["username"].'&key='.$this->parameters["key"];
        return $this->curlHttpRequest('GET',"userInfoResponse");
       }
       private function curlHttpRequest($method,$responseFunction ) {

          $this->parameters = http_build_query($this->parameters, '', '&');

          $curlOpts = array(
            CURLOPT_URL => $this->url,
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Accept: application/json'),
            );
          $ch = curl_init();    // create curl resource
          switch ($method) {
            case 'POST':
                $curlOpts[CURLOPT_TIMEOUT] = 90;
                $curlOpts[CURLOPT_POSTFIELDS] = $this->parameters;
                curl_setopt_array($ch, $curlOpts);
              break;

            case 'GET':
                $curlOpts[CURLOPT_TIMEOUT] = 10;
                $curlOpts[CURLOPT_BINARYTRANSFER]= 1 ;
                curl_setopt_array($ch, $curlOpts);
              break;
            default:
              return NULL;
          }


          $ret = new stdClass;
          $ret->response = curl_exec($ch); // execute and get response
          $ret->error    = curl_error($ch);
          $ret->errno    = curl_errno($ch);
          $ret->info     = curl_getinfo($ch);
          curl_close($ch);


          if ($ret->errno  != 0) {
              throw new sprintPDFException($ret->error,  $ret->errno);
          }
          else if ($ret->info['http_code'] == 200) {

                  $ret->response = json_decode($ret->response);

                    return $this->$responseFunction($ret->response);



          } else {
              throw new sprintPDFException($ret->response , $$ret->info['http_code']);
          }

        }
        private function defaultResponse($response){
          return $response;
        }
        private function fileResponse($response){

          if ($this->outstream) {

             fwrite($this->outstream, base64_decode($response->file) );
             return ;
          }else{
            return base64_decode($response->file) ;
          }

        }
        private function userInfoResponse($response) {
          return $response;
        }

      }

?>
