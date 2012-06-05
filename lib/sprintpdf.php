<?php

	/**
	 * @file
	 * @author  Sprint PDF <http://www.sprintpdf.com>
	 * @version 1.2
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 2 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	**/

	/** 
	 * The exception to be thrown in case of errors
	**/
	class sprintPDFException extends Exception {};

	/** 
	 * This class will allow you to generate PDFs in your own PHP application.
	**/
	class sprintPDF {
		private $parameters = array();
		private $gateway = 'http://www.sprintpdf.com/';
		private $apiVersion = 'api';
		private $resourceType = 'pdf';
		private $url = "";
		private $outstream  = NULL;
		private $responseFunction = "defaultResponse";

		/**
		 * sprintPDF constructor
		 *
		 * @param $username Your Sprint PDF username
		 * @param $key Your Sprint PDF password (in plaintext)
		**/
		function __construct($username, $key){
			$this->parameters = array('key' => $key, 'username' => $username);
			$this->url = $this->gateway . $this->apiVersion . '/' . $this->resourceType;
		}

		/**
		 * Convert a given URL into a PDF
		 *
		 * @param $src The publicly accessible URL to convert into PDF; make sure it starts with http:// or https://
		 * @param $outstream (optional) A PHP file pointer in case you want to save the file on your server
		 * @param $parameters (optional) A space-seperarted list of options; for a full set of options that you can use with Sprint PDF, please visit http://www.sprintpdf.com/Documentation/options.
		 *
		 * @return Either outputs the PHP file directly into the browser OR downloads it onto your server based on whether the $outstream parameter is set or not
		**/
		function convertURI($src, $outstream = null, $parameters = null){
			$this->outstream = $outstream;
			$this->parameters += array("resource" => $src, "type" => "url", "parameters" => $parameters);
			return $this->curlHttpRequest('POST', "fileResponse");
		}

		/**
		 * Convert a given raw HTML into a PDF
		 *
		 * @param $src Raw well-formed HTML 
		 * @param $outstream (optional) A PHP file pointer in case you want to save the file on your server
		 * @param $parameters (optional) A space-seperarted list of options; for a full set of options that you can use with Sprint PDF, please visit http://www.sprintpdf.com/Documentation/options.
		 *
		 * @return Either outputs the PHP file directly into the browser OR downloads it onto your server based on whether the $outstream parameter is set or not
		**/
		function convertHTML($src, $outstream = null,$apiParameters = null ){
			$this->outstream =$outstream;
			$this->parameters += array("resource" => $src, "type" => "html","parameters" => $apiParameters);
			return $this->curlHttpRequest('POST', "fileResponse");
		}

		/**
		 * Returns information for the provided user account
		 *
		 * @return An array containing user information, usage, account details, etc.
		**/
		function getInfo() {
			$this->url = $this->gateway . $this->apiVersion . '/me/info?' . 'username=' . $this->parameters["username"] . '&key=' . $this->parameters["key"];
			return $this->curlHttpRequest('GET',"defaultResponse");
		}

		/**
		 * Generic CURL fire-er
		 *
		 * @param $method GET, PUT, POST, or DELETE
		 * @param $responseFunction The callback function
		 *
		 * @return Runs the callback function specified by $responseFunction
		**/
		private function curlHttpRequest($method, $responseFunction){
			$this->parameters = http_build_query($this->parameters, '', '&');

			$curlOpts = array(
				CURLOPT_URL => $this->url,
				CURLOPT_FAILONERROR => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => array('Accept: application/json'),
			);

			$ch = curl_init();			
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
			}

			$ret = new stdClass;
			$ret->response = curl_exec($ch);
			$ret->error = curl_error($ch);
			$ret->errno = curl_errno($ch);
			$ret->info = curl_getinfo($ch);
			curl_close($ch);

			if($ret->errno != 0){
				throw new sprintPDFException($ret->error,  $ret->errno);
			} else if($ret->info['http_code'] == 200){
				$ret->response = json_decode($ret->response);
				return $this->$responseFunction($ret->response);
			} else {
				throw new sprintPDFException($ret->response , $$ret->info['http_code']);
			}
		}

		/**
		 * Returns output unprocessed
		**/
		private function defaultResponse($response){
			return $response;
		}
		
		/**
		 * Write the file to the server
		**/
		private function fileResponse($response){
			if($this->outstream){
				fwrite($this->outstream, base64_decode($response->file) );
				return ;
			} else {
				return base64_decode($response->file) ;
			}
		}
	}	
?>