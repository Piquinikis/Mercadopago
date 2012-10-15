<?php

class MPrequest
{
	private $client_id = "YOUR_ID_CLIENT";
	private $client_secret = "YOUR_CLIENT_SECRET";

	private $access_token;
	private $url;

	public function __construct()
	{
		$this->access_token = $this->makeRequestToken();
	}

	private function makeRequestToken()
	{
		$appClientValues = "client_id=" . $this->client_id . "&client_secret=" . $this->client_secret . "&grant_type=client_credentials";
		$url = "https://api.mercadolibre.com/oauth/token";	                                                     
	    $handler = curl_init(); 
	    curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'));
	    curl_setopt($handler, CURLOPT_URL, $url);           
	    curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);                  
	    curl_setopt($handler, CURLOPT_POSTFIELDS, $appClientValues);  
	    $response = curl_exec ($handler);  
	    
	    curl_close($handler);  
		$response = json_decode($response, true);		
		$this->access_token = $response['access_token'];
		return $this->access_token;		
	}

	public function getTokenID()
	{	
		return $this->access_token;
	}

	public function getIDpreference( $items )
	{		
		$post_data['items'] = array($items);		
	    $url = "https://api.mercadolibre.com/checkout/preferences?access_token=".$this->access_token;
	    $handler = curl_init();
	    curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
	    curl_setopt($handler, CURLOPT_URL, $url);
	    curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);   
	    curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($post_data));
	    $response = curl_exec($handler);
	    curl_close($handler);
	    $response = json_decode($response, true);
	    return $response['id'];
	}
}
?>