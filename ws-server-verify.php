<?


################################################################################
# 1° INICIAMOS AS VERIFICAÇÕES BÁSICAS DO SERVIDOR ANTES DE INICIAR
################################################################################
	$bug 					= 0;
	$errors 				= '';
	$config 				= (object) Array();
	$config->php_ini 		= (object) ini_get_all();
	$config->extensions 	= (object) Array();
	$config->apache 		= (object) Array();
	$_GETURL 				= (empty($_SERVER['REQUEST_URI'])) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_URI'];
	$_GETURL 				= explode('/',$_GETURL);

	if (function_exists('apache_get_modules')) {
	  $modules = apache_get_modules();
	  $mod_rewrite = in_array('mod_rewrite', $modules);
	} else {
	  $mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
	}
	foreach (get_loaded_extensions() as $value) {
		$config->extensions->{$value} = true;
	}
	function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
	        case 'g':
	            $val *= (1024 * 1024 * 1024); //1073741824
	            break;
	        case 'm':
	            $val *= (1024 * 1024); //1048576
	            break;
	        case 'k':
	            $val *= 1024;
	            break;
	    }
	    return $val;
	}

	###########################################################################
	#	VERIFICA AS DEPENDENCIAS BÁSICAS PARA O FUNCIONAMENTO DO SISTEMA
	###########################################################################

	if(basename(dirname(__FILE__))!="ws-install-master"){
		$bug = 1;
		$errors.=  "<div>• Para instalar/atualizar o WebSheep, é necessário colocar os arquivos do instalador dentro do diretório <b>/ws-install-master/</b>, e em seguida dê continuidade a instalação.</div> ";
	}
	if (version_compare(PHP_VERSION, '5.6.4') < 0) {
		$bug = 1;
		$errors.="<div>• A versão do PHP é ".PHP_VERSION.". Por favor, instale o php 5.6.4 ou superior para que o sistema funcione corretamente.</div>";
	}
	if (!date_default_timezone_get()) {
	    $bug = 1;
		$errors.="<div>• date_default_timezone_set</div>";
	}

	if(@$config->php_ini->file_uploads["global_value"] != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a função file_uploads</div>";
	}
	if(@$config->php_ini->max_file_uploads["global_value"] < 10){
		$bug = 1;
		$errors.="<div>• Aumente a opção 'max_file_uploads' em seu php.ini, sugerimos no mínimo 30</div>";
	}
	if(@$config->php_ini->short_open_tag["global_value"]!=1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a função short_open_tag</div>";
	}
	if(return_bytes(@$config->php_ini->upload_max_filesize["global_value"]) <= 1048576){	
		$bug = 1;
		$errors.="<div>• Aumente a opção 'upload_max_filesize' em seu php.ini, sugerimos no mínimo 2M</div>";
	}
	if(@$config->extensions->mbstring !=1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a extansão php_mbstring</div>";
	}
	if(@$config->extensions->gettext !=1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a extansão php_gettext</div>";
	}
	if(@$config->extensions->openssl !=1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a extansão php_openssl</div>";
	}
	if(@$config->extensions->zip != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca ZIP</div>";
	}
	if(@$config->extensions->curl != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a extansão php_curl</div>";
	}
	if(@$config->extensions->mysqli != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar em seu php.ini a extansão php_mysqli</div>";
	}
	if(@$config->extensions->mcrypt != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca mcrypt</div>";
	}
	if(@$config->extensions->hash != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca hash</div>";
	}
	if(@$config->extensions->session != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca session</div>";
	}
	if(@$config->extensions->dom != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca DOM</div>";
	}
	if(@$config->extensions->SimpleXML != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca SimpleXML</div>";
	}
	if(@$config->extensions->gd != 1){
		$bug = 1;
		$errors.="<div>• É necessário habilitar a biblioteca GD</div> ";
	}

################################################################################
# CASO TENHA ALGUM ERRO PRINTA NA TELA
################################################################################
if($bug) {
	header('Content-Type: text/html; charset=utf-8');
	echo '<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
		<style>
			div{
			    position: relative;
			    float: none;
			    font-family: "Titillium Web", sans-serif;
			    height: 20px;
			    background-color: #bf1200;
			    padding: 10px;
			    color: #FFF;
			    border-bottom: dotted 1px;
			}
		</style>';
		echo $errors;
		exit;
}

