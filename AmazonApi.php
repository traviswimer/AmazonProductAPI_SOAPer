<?php
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