<?php 
/*
* Esto comprueba la existencia de una constante de PHP, y si no existe, abandona. 
* El único propósito de esta prueba es evitar que los visitantes carguen este archivo directamente.
*/

if( !defined( '_PS_VERSION_') )
	exit;

// Cargando la clase de MercadoPago creada para interctuar con la aplicacion
// Esta clase contiene los metodos para una conexion con el API 
// 
if (file_exists(dirname(__FILE__).'/MPrequest.php'))
	require_once(dirname(__FILE__).'/MPrequest.php');

class MercadoPago extends PaymentModule 
{
	public function __construct()
	{
		$this->name = 'mercadopago';
		$this->tab = 'payment_method';
		$this->version = 1.0;
		$this->author = 'Daniel Russian';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l( 'Mercado Pago' );
		$this->description = $this->l( 'Un modulo para pagar mediante MercadoPago' );
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		parent::uninstall();
	}

	public function hookPayment( $params )
	{
		if (!$this->active)
			return ;

		global $smarty;

		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));	

		$products = $params['cart']->getProducts(true); // Obtengo los productos guardados en el carrito

	// Si la compra es por mas de un articulo debo concatenar los titulos y sumar los totales
	// Para agregarlos a la descripcion de la compra 
		if( count( $products ) > 1 )
		{
			foreach ($products as $key => $value) {			
				$title = $products[$key]['name'] . " + " . $title;				
				$unit_price = $unit_price + $products[$key]['total_wt'];
				$quantity = 1;
			}
			$title = substr( $title, 0, -2 ); // Elimino el ultimo signo + agregado en el foreach
		}	else{ 
		// En caso de ser unico articulo
				$title = $products[0]['name'];
				$unit_price = $products[0]['total_wt'];
				$quantity = $products[0]['cart_quantity'] ;
		}	
	// Organizo un array con todos los datos para ser pasado a MercadoPago
	// Estas variables las organize con el foreach
		$article = array(
			'title' => (string)$title,
	        'unit_price' => $unit_price,
	        'quantity' => intval($quantity) ,
	        'currency_id' => 'ARS',	 
		);
	// Una nueva instancia de la clase para configurar la conexion con el API
		$instance = new MPrequest;
		$id_preference = $instance->getIDpreference( $article );
	// Asigno el id de la preferencia de pago a una variable global de Smarty
		$smarty->assign( 'id_preference', $id_preference );
		return $this->display(__FILE__, 'mercadopago.tpl');
	}

	public function hookPaymentReturn( $params )
	{
		if (!$this->active)
			return ;
		return $this->display(__FILE__, 'confirmation.tpl');
	}
}
?>