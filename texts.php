<?php
/**
*  @author Vicente García <v@vicentegarcia.com>
*  @copyright  Copyright (c) 2014, Vicente García
*  @version  1.2
*/
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

if (!Tools::getValue('ajax') || Tools::getValue('cookieinfo_token') != sha1(_COOKIE_KEY_.'cookieinfo'))
	die('INVALID TOKEN');

$title = Configuration::get('COOKIEINFO_TITLE');
$text = html_entity_decode(Configuration::get('COOKIEINFO_TEXT'));
$linkpolicy = html_entity_decode(Configuration::get('COOKIEINFO_LINKPOLICY'));
$policy = html_entity_decode(Configuration::get('COOKIEINFO_POLICY'));

echo json_encode( array( 'title'=>$title,'text'=>$text,'linkpolicy'=>$linkpolicy,'policy'=>$policy ) );
?>