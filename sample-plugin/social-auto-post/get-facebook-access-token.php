<?php
require_once( 'classes/class-woo-socio.php' );
global $woosocio;
$woosocio = new Woo_Socio( __FILE__ );
$woosocio->getaccesstoken();
?>