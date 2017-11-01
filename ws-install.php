<?php
		
###########################################################################
###########################################################################
###########################################################################
#	ARQUIVO DE INSTALAÇÃO WEBSHEEP V0.2
###########################################################################
###########################################################################
###########################################################################


	###########################################################################
	#	TEMPO DE EXECUÇÃO ILIMITADO
	###########################################################################
		ini_set('max_execution_time',0);

	###########################################################################
	#	VERIFICA AS DEPENDENCIAS BÁSICAS PARA O FUNCIONAMENTO DO SISTEMA
	###########################################################################
		include_once("./ws-server-verify.php");

	###########################################################################
	#	FUNÇÃO PARA CÓPIA DE DIRETÓRIOS COMPLETOS 
	###########################################################################
	function ws_copy_dir($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					ws_copy_dir($src . '/' . $file,$dst . '/' . $file); 
				} else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	} 

	###########################################################################
	#	FUNÇÃO PARA EXCLUSÃO DE DIRETÓRIOS INTEIROS 
	###########################################################################
	function ws_delete_dir($Dir) {
		if ($dd = opendir($Dir)) {
			while (false !== ($Arq = readdir($dd))) {
				if ($Arq != "." && $Arq != "..") {
					$Path = "$Dir/$Arq";
					if (is_dir($Path)) {
						ws_delete_dir($Path);
					} elseif (is_file($Path)) {
						unlink($Path);
					}
				}
			}
			closedir($dd);
		}
		rmdir($Dir);
	}



	####################################################################################
	#	AQUI ELE DESCOMPACTA O DIRETÓRIO COM TODOS OS ARQUIVOS NA RAIZ E DÁ UM REFRESH  
	####################################################################################

	if((isset($_GET['install']) && @$_GET['install']=="install")){
		echo '<script>console.log("install")</script>'.PHP_EOL;
		if(file_exists("./../admin")){ ws_delete_dir("./../admin"); }
		verifyAdmin:
		if(!file_exists("./../admin")){
			$zip = new ZipArchive();
			if ($zip->open("./../ws-update.zip")) {
				if($zip->extractTo("./../")){
					$folderName = "CMS-".str_replace(".zip","",basename($_GET['githubFile']));
					rename("./../".$folderName.'/admin','./../admin'); 
					ws_delete_dir("./../".$folderName);
					$zip->close();
					unlink("./../ws-update.zip");
					echo "<script>window.top.location='/admin';</script>";
				};
				exit; 
			}
		}else{
			sleep(1);
			goto verifyAdmin;
		}
		exit;
	}
	####################################################################################
	#	PERGUNTA AO USUARIO SE TEM CERTEZA QUE QUER INSTALAR  
	####################################################################################
	if((isset($_GET['install']) && @$_GET['install']=="fire")):?>
		<script>
			window.top.$("#loader,.logo").hide();
			window.top.$("#botao").unbind("click press tap").bind("click press tap",function(){
				window.top.$("#iframe").attr("src","/ws-install-master/ws-install.php?install=install&githubFile=<?=$_GET['githubFile']?>");
				window.top.$("#botao").hide();
				window.top.$(".comboCentral").hide();
				window.top.$(".preloader").show();
			}).html("Instalar WebSheep!").show();
		</script>;

	<? 
	exit;
	endif;

	####################################################################################
	#	FAZ DOWNLOAD DO GITHUB VIA STREAM RETORNANDO PROGRESSBAR  
	####################################################################################
	if((isset($_GET['githubFile']) && @$_GET['githubFile']!="") && (isset($_GET['type']) &&  @$_GET['type']=="progressBar") ):?>
	<?
		$total_size = 0;
		function stream_notification_callback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) {
			global $total_size;
		    switch($notification_code) {
		        case STREAM_NOTIFY_FILE_SIZE_IS:
					$total_size = $bytes_max;
		            echo '<script>window.top.$(".comboCentral .logo").text("Fazendo download...");</script>'.PHP_EOL;
		            break;
		        case STREAM_NOTIFY_PROGRESS:
		        	$pct = @round((100 / $total_size) * $bytes_transferred);
		            echo '<script>document.getElementById("progressBar").style.width="'.$pct.'%";</script>'.PHP_EOL;
		            break;
		    }
		}
		if(file_exists("./../ws-update.zip")){ unlink("./../ws-update.zip"); }
		echo '<div id="progressBar" style="	-webkit-transition: all 1000ms ease-out; -moz-transition: all 1000ms ease-out; -ms-transition: all 1000ms ease-out; -o-transition: all 1000ms ease-out; transition: all 1000ms ease-out; position: fixed; background-color: #4f6cc6; height: 100%; width:0%; margin: 0; padding: 0; top: 0; left: 0; "></div>';
		$ctx = stream_context_create();
		stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));
		file_put_contents("./../ws-update.zip",file_get_contents($_GET['githubFile'], false, $ctx));
		echo "<script>window.location='/ws-install-master/ws-install.php?install=fire&githubFile=".$_GET['githubFile']."';</script>";
		exit;
endif;

	####################################################################################
	#	INICIA A PÁGINA  
	####################################################################################
?>

<head>
	<title><?=(file_exists("./../admin")) ? "WebSheep - Update de sistema" : "WebSheep - instalação do sistema";?> </title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,700" rel="stylesheet">
	<link href="./styles/stylesheet.css" rel="stylesheet">

</head>
	<img class="preloader" src="data:image/gif;base64,R0lGODlhUABRAPf/AACd/wCf/8/j/9bt/7Th/wCX/wCv/4XO/3DM/6ve/77r/13G/wCw/0bE//D5/wCZ/wCb/5fU/wC1/2rP/wC3/wCg/5Df/wCh/wCo/wCT/33U//7+/wDF/wCf/wC1/4Xn/+r4/zS//VG9/wCo/7rb/4rd/wCa//L6//D///j5/+v2//L2/+Lt/3LD/9Tx/+78/+H1/wCg/x63/xC+/wC9/z/K/yHD//v//wCn/wCT/wCZ/wCr/6Hl/wCX/wCo/9rx/wCh/wCb/5HO9yLK/wCe/wCb/3HU/wCg/wCV/wCw/wCX/6Ha/43Z/1nS/wCd/wCZ/wCg/wC6/wCP/6bW//r//07K/7Lp/wCW/wCX/83s/wCq/9r7/+P3/8bk/wCj/4nV/8Hm/wC1/6ri/8jq/6vs/4Pb/6na/wCV//z5/wCV/87v///8/9z1/wCY/wCS/wCs/3nb/6vk/2C+/wCU/8To//z8/4Xd/////UrP/wCd/8/y//b+/wCR/8jx/wCc/w+v/wC6/+X7/6zX/+T9/6/m/6fo/+vz/4zW/wCa/3fI/+Hx/2nb/wCr/+fy/wDB/9Po/wLH/6Pe/yjG/wCa/zfB/1TQ/5rg/wCi/wCX///2/9T1/wCn//n8/+v+/wCT/wCV///8+gCU/wCe/2LJ/5DT/4XV/Lzk/wCl/wCw/wCm/x3R/8/1/8Pu/wCg/wCr/QCZ/wCX/4bR/zO2/wCR/wCc/wCp/73y/5rq/8Hg/ACd/wDK/8Xj+gCi/43U++ny+qfa+876/8fm/AC//wCR//X8//v4++vz+uLx/P36+4HJ/wCU/7Hf+/Pw/5zZ/crp/ACl/wC1/ACq/4bZ/f/8+wCg/wCg/wCd/wCa/wCj/4nd/ACe/wCX//X3+gCZ/wCV/wCZ/0C6+U63/wCc/3/e/QCW/6nc+2fC+k7C/B65/ACl/tjq/Y7b/ACg/gCc/+n2/QCe/1zE/ACc/+70+sj3/wCp/gCu/wCu/jPD/q7W96Hd/LDa+vj8/fr9/P///wCg/////yH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTM4IDc5LjE1OTgyNCwgMjAxNi8wOS8xNC0wMTowOTowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjI0RTZCOTZEQkI2RDExRTdBQzcxQTY0RDQ1NEM2RTI2IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjI0RTZCOTZFQkI2RDExRTdBQzcxQTY0RDQ1NEM2RTI2Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MjRFNkI5NkJCQjZEMTFFN0FDNzFBNjRENDU0QzZFMjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MjRFNkI5NkNCQjZEMTFFN0FDNzFBNjRENDU0QzZFMjYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4B//79/Pv6+fj39vX08/Lx8O/u7ezr6uno5+bl5OPi4eDf3t3c29rZ2NfW1dTT0tHQz87NzMvKycjHxsXEw8LBwL++vby7urm4t7a1tLOysbCvrq2sq6qpqKempaSjoqGgn56dnJuamZiXlpWUk5KRkI+OjYyLiomIh4aFhIOCgYB/fn18e3p5eHd2dXRzcnFwb25tbGtqaWhnZmVkY2JhYF9eXVxbWllYV1ZVVFNSUVBPTk1MS0pJSEdGRURDQkFAPz49PDs6OTg3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" >

	<div class="comboCentral">
		<div class="logo"><b><?php if(file_exists("./../admin")){echo "Update"; }else{echo "Install";}?> WebSheep</b><br>
	</div>
	<div class="txt" id="txt">
		<select id="branches" style="display:none">
			<option value="null">Selecione uma versão</option>
		</select>
	</div>
	<!--    -->
	<div class="botao" id="botao" style="display:none">Importar do GitHub</div>
	<div class="loader iframeBack" id="loader" style="display:none">
		
	</div>
</div>

<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript">
	window.setMsn = function(valor){$('#bytes').text(valor);}
	$(document).ready(function(){
		$("#botao").hide();
		$.getJSON("https://api.github.com/repos/websheep/cms/branches",function(a){
			$.each(a,function(b,c){
				var baseLink = "https://github.com/websheep/CMS/archive/__.zip";
				var realLink = baseLink.replace("__",c.name);
				$("#branches").append('<option value="'+realLink+'">'+c.name+'</option>');
			})
			$("#botao,#branches").show();
		})
		$("#botao").bind("click press tap",function(){
			var path 	= $("#branches").val().split('/');
			var version = $("#branches").val();
			if(version=="null"){
				alert("Selecio ne uma versão");
			}else{
				$(".comboCentral .logo").text("Aguarde, conectando GitHub...");
				$("#loader").show().html("<iframe id='iframe' src='./ws-install.php?type=progressBar&githubFile="+version+"'></iframe>");
				$("#txt,#botao").hide();
			}
		})
	})
</script>