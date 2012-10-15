El repositorio contiene una clase para la integración a cualquier tipo de carrito de compras o similar. Además tiene un simple modulo para integrarlo fácilmente a Prestashop.
Para utilizarla se debe incluir el archivo de la clase, y nueva instancia a classMercadopago.

En el archivo classMercadopago.php debe completar el $client_id y $client_secret con los proporcionador por Mercadopago, una vez instanciada la clase se crea un $acces_token para la comunicación con la API.

Agregar item al listado de compra:
Llamar a la función set_data_items() pasando como parametro un array con los datos del item, si se pretende una carga multiple se debe utilizar un foreach en la clase para carga multiples.

Generar datos del cliente:
set_data_payer, con datos del pagador.

Utilizar URLs personalizadas:
set_data_backurls con un arreglo con links de pagos satisfactorios, cancelados, etc.

Ante cualquier duda consultar la documentación de Mercadopago https://developers.mercadopago.com/integracion-checkout