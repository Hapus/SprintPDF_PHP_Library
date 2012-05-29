<?php

 class sprintPDFException extends Exception {};

    class sprintPDF {
       private $parameters = array();
       private $gateway = 'http://ec2-50-19-62-60.compute-1.amazonaws.com/haapus/';
       private $apiVersion = '?q=api';
       private $resourceType = 'sprintpdf';
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

       function convertURI( $src, $outstream = null) {
        $this->outstream =$outstream;
        $this->parameters += array("resource" => $src, "type" => "url");
        return $this->curlHttpRequest('POST', "fileResponse");
       }
       function convertHTML($src, $outstream = null){
        $this->outstream =$outstream;
        $this->parameters += array("resource" => $src, "type" => "html");
        return $this->curlHttpRequest('POST', "fileResponse");
       }
       function getInfo() {
        $this->url = $this->gateway.$this->apiVersion.'/'.$this->resourceType.
        '/'.$this->parameters["username"].'&key='.$this->parameters["key"];
        return $this->curlHttpRequest('GET',"userInfoResponse");
       }
       private function getCurlGetOptions(){
         $ret = array( CURLOPT_URL => $this->url,
                      CURLOPT_FAILONERROR => true,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_BINARYTRANSFER => 1,
                      CURLOPT_TIMEOUT => 3,
                      CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    );
        return $ret;
       }
       private function getCurlPostOptions(){

            $ret = array( CURLOPT_URL => $this->url,
                      CURLOPT_FAILONERROR => true,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_TIMEOUT => 40,
                      CURLOPT_HTTPHEADER => array('Accept: application/json'),
                      CURLOPT_POST => true,
                      CURLOPT_POSTFIELDS => $this->parameters
                    );
            return $ret;
       }
       private function curlHttpRequest($method,$responseFunction ) {

          $this->parameters = http_build_query($this->parameters, '', '&');
          $ch = curl_init();    // create curl resource
          switch ($method) {
            case 'POST':   curl_setopt_array($ch, $this->getCurlPostOptions());
              break;
            case 'GET':    curl_setopt_array($ch, $this->getCurlGetOptions());
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

    /***
    * Save to file example

    $client = new sprintPDF("admin", "coldcold");
    $out_file = fopen("document.pdf", "wb");
    $client->convertURI("http://www.editage.com", $out_file);
    fclose($out_file);

    // File direct download example

      try
      {
          // create an API client instance
          $client = new sprintPDF("admin", "coldcold");

          // convert a web page and store the generated PDF into a $pdf variable
          $pdf = $client->convertURI('http://google.com/');

          // set HTTP response headers
          header("Content-Type: application/pdf");
          header("Cache-Control: no-cache");
          header("Accept-Ranges: none");
          header("Content-Disposition: attachment; filename=\"google_com.pdf\"");

          // send the generated PDF
          echo $pdf;
      }
      catch(sprintPDFException $e)
      {
          echo "SprintPDF Error: " . $e->getMessage();
      }

  //  Get user information example

      $client = new sprintPDF("admin", "coldcold");
      $info =  $client->getInfo();
      print_r($info);
   */
?>
