<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-REGARDLESS-OF-APOCALYPSE-WARE LICENSE" (Revision 42 - 12/12/2012):
 * @Travis_Wimer wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If by some miraculous chance, 
 * the world does not end in 9 days, and we happen meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return. If the world does 
 * end in a Mayan zombie apocalypse, as we all know it will, you are strictly 
 * required to buy me a beer in hell. -Travis Wimer
 * 
 * 
 * ----------------------------------------------------------------------------
 * 
 * For any legal matters, the above license will be superseded by the following:
 * 
 * 
 * Copyright (C) 2012 Travis Wimer
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * ----------------------------------------------------------------------------
 */


class AmazonAPI{
	private static $instance;
	
	private $public_key='YOUR_PUBLIC_KEY';
	private $secret_key='YOUR_PRIVATE_KEY';
	private $associate_tag='YOUR_ASSOCIATE_TAG';//This is only for Amazon affiliates
	
	private $wsdl_url='http://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl';
	private $webservices_url='https://webservices.amazon.com/onca/soap?Service=AWSECommerceService';
	
	private function __construct(){}
	
	private function __clone(){}
	
	public static function getAmazonAPI(){
		if(empty(self::$instance)){
			self::$instance=new AmazonAPI();
		}
		return self::$instance;
	}
	
	
	//Send a request to amazon
	public function sendRequest($request_params){
		$params['Request']=$request_params;
		$operation=$request_params['Operation'];
		
		if(isset($this->associate_tag)){
			$params['AssociateTag']=$this->associate_tag;
		}
		
		$soapy=new SoapClient(
			$this->wsdl_url,
			array('exceptions'=>1)
		);
		
		$soapy->__setLocation($this->webservices_url);

		$current_timestamp=gmdate("Y-m-d\TH:i:s\Z");
		$req_sig=$this->createSignature($operation,$current_timestamp);
		
		$headers_array=array(
			new SoapHeader(
				'http://security.amazonaws.com/doc/2007-01-01/',
				'AWSAccessKeyId',
				$this->public_key
			),
			new SoapHeader(
				'http://security.amazonaws.com/doc/2007-01-01/',
				'Timestamp',
				$current_timestamp
			),
			new SoapHeader(
				'http://security.amazonaws.com/doc/2007-01-01/',
				'Signature',
				$req_sig
			)
		);
		$soapy->__setSoapHeaders($headers_array);

		return $soapy->__soapCall($operation,array($params));
	}
	
	
	//Create signature for request
	protected function createSignature($operation,$timestamp){
		$the_string=$operation.$timestamp;
		return base64_encode(hash_hmac("sha256",$the_string,$this->secret_key,true));
	}
}