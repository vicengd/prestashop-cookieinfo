<?php
/**
*  @author Vicente García <v@vicentegarcia.com>
*  @copyright  Copyright (c) 2014, Vicente García
*  @version  1.2
*/

if (!defined('_PS_VERSION_'))
	exit;

class CookieInfo extends Module
{
	private $html = '';
	private $post_errors = array();

	private $cookieinfo_title = 'Uso de cookies';
	private $cookieinfo_text = 'Utilizamos cookies propias y de terceros para mejorar la experiencia de navegación, y ofrecer contenidos
				y publicidad de interés. Al continuar con la navegación entendemos que se acepta nuestra Política de cookies.';
	private $cookieinfo_linkpolicy = 'Ver política';
	private $cookieinfo_public = 0;
	private $cookieinfo_policy = '';

	public function __construct()
	{
		$this->name = 'cookieinfo';
		$this->version = '1.2';
		$this->author = 'Vicente Garcia';
		$this->tab = 'front_office_features';
		$this->displayName = $this->l('Cookie Info');
		$this->description = $this->l('Provides a module information of cookies.');
		$this->confirmUninstall = $this->l('Are you sure you want uninstall?');

		$text_cookie = file_get_contents (_PS_MODULE_DIR_.'cookieinfo/text_default.txt');
		$this->cookieinfo_policy = htmlentities($text_cookie);

		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('header') ||
			!Configuration::updateValue('COOKIEINFO_TITLE', $this->cookieinfo_title) ||
			!Configuration::updateValue('COOKIEINFO_TEXT', $this->cookieinfo_text) ||
			!Configuration::updateValue('COOKIEINFO_LINKPOLICY', $this->cookieinfo_linkpolicy) ||
			!Configuration::updateValue('COOKIEINFO_POLICY', $this->cookieinfo_policy) ||
			!Configuration::updateValue('COOKIEINFO_PUBLIC', $this->cookieinfo_public))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !Configuration::deleteByName('COOKIEINFO_TITLE') ||
			!Configuration::deleteByName('COOKIEINFO_TEXT') ||
			!Configuration::deleteByName('COOKIEINFO_LINKPOLICY') ||
			!Configuration::deleteByName('COOKIEINFO_POLICY') ||
			!Configuration::deleteByName('COOKIEINFO_PUBLIC') )
			return false;
		return true;
	}

	private function postProcess()
	{
		if (Tools::getValue('btnSubmit'))
		{
			if (Tools::getValue('cookieinfo_title') && Tools::getValue('cookieinfo_text'))
			{
				Configuration::updateValue('COOKIEINFO_TITLE', Tools::getValue('cookieinfo_title'));
				Configuration::updateValue('COOKIEINFO_TEXT', htmlentities(Tools::getValue('cookieinfo_text')));
				Configuration::updateValue('COOKIEINFO_LINKPOLICY', Tools::getValue('cookieinfo_linkpolicy'));
				Configuration::updateValue('COOKIEINFO_POLICY', htmlspecialchars(Tools::getValue('cookieinfo_policy')));
				Configuration::updateValue('COOKIEINFO_PUBLIC', Tools::getValue('cookieinfo_public'));
			}
		}
		$this->html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" /> '.$this->l('Settings updated').'</div>';
	}

	private function postValidation()
	{
		if (Tools::getValue('btnSubmit'))
		{
			if (!Tools::getValue('cookieinfo_title') || !Tools::getValue('cookieinfo_text'))
				$this->post_errors[] = $this->l('You need to enter your variables to continue.');
		}
	}

	private function displayForm()
	{
		$this->html .=
		'<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Configuration').'</legend>
				<label>'.$this->l('Title').'</label>
				<div class="margin-form"><input type="text" size="50" name="cookieinfo_title" value="'
				.Tools::getValue('cookieinfo_title', Configuration::get('COOKIEINFO_TITLE')).'" /></div>
				<label>'.$this->l('Text (you can use html)').'</label>
				<div class="margin-form"><textarea name="cookieinfo_text" rows="4" cols="100">'
				.Tools::getValue('cookieinfo_text', Configuration::get('COOKIEINFO_TEXT')).'</textarea></div>
				<label>'.$this->l('Policy\'s link').'</label>
				<div class="margin-form"><input type="text" size="50" name="cookieinfo_linkpolicy" value="'
				.Tools::getValue('cookieinfo_linkpolicy', Configuration::get('COOKIEINFO_LINKPOLICY')).'" /></div>
				<label>'.$this->l('Policy').'</label>
				<div class="margin-form">
					<textarea class="rte" cols="70" rows="160" name="cookieinfo_policy">'
					.Tools::getValue('cookieinfo_policy', Configuration::get('COOKIEINFO_POLICY')).'</textarea>
				</div>
				<label>'.$this->l('Public').'</label>
				<div class="margin-form">
					<input type="radio" name="cookieinfo_public" id="cookieinfo_public_on" value="1" '
					.(Tools::getValue('cookieinfo_public', Configuration::get('COOKIEINFO_PUBLIC')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="cookieinfo_public_on"> <img src="../img/admin/enabled.gif" /></label>
					<input type="radio" name="cookieinfo_public" id="cookieinfo_public_off" value="0" '
					.(!Tools::getValue('cookieinfo_public', Configuration::get('COOKIEINFO_PUBLIC')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="text_list_off"> <img src="../img/admin/disabled.gif" /></label>
				</div>
				<center><input type="submit" name="btnSubmit" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$iso = $this->context->language->iso_code;

		$this->html = '
		<script type="text/javascript">
			var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script language="javascript" type="text/javascript">
			id_language = Number('.$id_lang_default.');
			tinySetup();
		</script>';

		$this->html .= '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			$this->postValidation();

			if (!count($this->post_errors))
				$this->postProcess();
			else
				foreach ($this->post_errors as $err)
					$this->html .= '<div class="alert error">'.$err.'</div>';
		}
		else
			$this->html .= '<br />';

		$this->displayForm();

		return $this->html;
	}

	public function hookDisplayHeader()
	{
		$year = 60 * 60 * 24 * 365 + time();
		$show_alert = true;

		$this->context->controller->addJquery();
		$this->context->controller->addJqueryPlugin('fancybox');
		$this->context->controller->addCSS($this->_path.'css/'.$this->name.'.css');

		if (empty($_COOKIE['cookieinfo_start']))
		{
			//La primera vez que se entra al sitio se graba la cookie 'cookieinfo_start'
			setcookie('cookieinfo_start', $_SERVER['REQUEST_URI'], $year, '/');
		}
		elseif ($_COOKIE['cookieinfo_start'] != $_SERVER['REQUEST_URI'])
		{
			//Si la página a la que llegó es distinta a la que esta viendo actualmente grabamos la cookie 'cookieinfo'
			setcookie('cookieinfo', 1, $year, '/');
			$show_alert = false;
		}

		//Si no existe la cookie y esta en modo publico mostramos el aviso
		if (empty($_COOKIE['cookieinfo']) && $show_alert == true && Configuration::get('COOKIEINFO_PUBLIC') == 1)
		{
			$cookieinfo_token = sha1(_COOKIE_KEY_.'cookieinfo');

			$this->context->controller->addJS($this->_path.'js/jquery.livequery.min.js');
			$this->context->smarty->assign('cookieinfo_token', $cookieinfo_token);
			return $this->display(__FILE__, 'cookieinfo-header.tpl');
		}
	}

}
?>