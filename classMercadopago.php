<?php
/**
* Clase para la integración del boton de pagos de mercadopago
* Desarrollado por Daniel Russian
* client_id es en identificador unico de cliente
* client_secret codigo secreto unico por cliente
*/
class classMercadopago {
	const $client_id = 'ID DE CLIENTE';
	const $client_secret = 'CODIGO SECRETO DE CLIENTE';
	private $access_token;

	function __construct()
	{
		$this->access_token = get_access_token();
	}

	/**
	* ============================== Autentícate ==============================
	* Obtener el acces token para interactuar con el API
	* La funcion devuelve el acces_token si se obtuvo con exito o NULL si fracaso la conexion
	* $url = es la direccion donde enviamos el json 
	* $post_data = los datos que enviamos al APi para permitir el token de acceso 
	*/
	private function get_access_token()
	{
		$url = "https://api.mercadolibre.com/oauth/token";
	    $post_data ="client_id=$client_id&client_secret=$client_secret&grant_type=client_credentials";                                                 
	    $handler = curl_init();
	    // Configuramos los datos para la conexion json 
	    curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'));
	    curl_setopt($handler, CURLOPT_URL, $url);                           
	    curl_setopt($handler, CURLOPT_POSTFIELDS, $post_data);  
	    $response = curl_exec ($handler); 
	    curl_close($handler);  
	    // Guardamos la respuesta si es ok guardamos access_token sino NULL 
	    $response = json_decode($response, true);
	    $access_token = $response['status'] == 200 ? $response['access_token'] : null;
	    return $access_token; 
	}

	/**
	* ============================== Configura la API de Checkout ==============================
	* @param $data (array)
	* Seteo los datos del articulo
	*/
	private function set_data( $data )
	{
		if( !isset( $data['quantity'] ) || !isset( $data['currency_id']) || !isset( $data['unit_price'] ) )
		{
			trigger_error("Hay escases de datos");
		}
		$attributesArray = array(
			'id' => $data['id'],
			'title' => $data['title'],
			'description' => $data['description'],
			'quantity' => $data['quantity'],
			'unit_price' => $data['unit_price'],
			'currency_id' => $data['currency_id'],
			'picture_url' => $data['picture_url'], 
			);  
		return $attributesArray;
	}

	/**
	
	*/
	public function config_checkout($array)
	{
		$token = getTokenID();
		$items = array(
			'title' => $title,
	        'unit_price' => intval($money),
	        'quantity' => 1,
	        'currency_id' => 'ARS',	 
		);
		$back_urls = array(
			'success' => 'http://www.fiestadedescuentos.com/order/pay.php?state=pay',
		);
		$post_data['items'] = array($items);
		$post_data['back_urls'] = $back_urls;    
	    $url = "https://api.mercadolibre.com/checkout/preferences?access_token=".$token;
	    $handler = curl_init();
	    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
	    curl_setopt($handler, CURLOPT_URL, $url);
	    curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($post_data));
	    $response = curl_exec($handler);
	    curl_close($handler);
	    $response = json_decode($response, true);
	    return $response['id'];
	}

	/*GET a mercadopago para verificar estado de pago */
	function getback($id){	
		$token = getTokenID();
		/* realizar un GET a mercadopago*/
		$url = "https://api.mercadolibre.com/collections/notifications/$id?access_token=$token";
		$handler = curl_init();
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept: application/json'));
	  	curl_setopt($handler, CURLOPT_URL, $url);
		$response = curl_exec($handler);
		curl_close($handler);
		$response = json_decode($response, true);
		$dataArray = array();
		$dataArray['status'] = $response['collection']['status'];
		$dataArray['email'] = $response['collection']['payer']['email'];
		$dataArray['monto'] = $response['collection']['transaction_amount'];
		return $dataArray;
	}
}/*final de la clase*/
?>