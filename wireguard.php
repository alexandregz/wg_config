<?php
/*
 * front-ed to wg_config (https://github.com/alexandregz/wg_config) 
 */





// ini_set('display_errors', true);
// error_reporting(E_ALL);

// my interface
$interface = 'wg0';

// path em local a github.com/alexandregz/wg_config/
$path_wg_config = '/home/pi/wireguard/wg_config/';


$commands = array(
	'show' => 'sudo wg show',
	// 'up' => 'wg-quick up '.$interface,
	// 'down' => 'wg-quick down '.$interface,
	// 'service-start' => 'sudo service wg-quick@wg0 start',
	// 'service-stop' => 'sudo service wg-quick@wg0 stop',
	// 'service-restart' => 'sudo service wg-quick@wg0 restart',

	'adduser' => 'sudo '.$path_wg_config.'user.sh -a ',
	'deluser' => 'sudo '.$path_wg_config.'user.sh -d ',
	'viewuser' => 'sudo '.$path_wg_config.'user.sh -v '
);


$accion = $_REQUEST['accion'];
$usuario = $_REQUEST['usuario'];


if(isset($_REQUEST["user_download_file"])){
    $file = $path_wg_config.'users/'.$_REQUEST["user_download_file"].'/client.conf';

    if(!is_file($file) || !is_readable($file)) die('File does not exists. Check path again.');

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

?>




<html>
<head><title>Wireguard interface</title></head>
<body>

<?php
if(isset($commands[$accion])) {


	// se e viewuser, nom amossamos o output porque queda mal
	// 1) cat de users/USER/client.conf
	// 2) incrustamos qrcode de users/USER/CLIENT.png
	if($accion == 'viewuser') {
		if(file_exists($path_wg_config."users/$usuario/client.conf")) {
			$output_config = shell_exec('/bin/cat '.$path_wg_config."users/$usuario/client.conf");
			$imagedata = file_get_contents($path_wg_config."users/$usuario/$usuario.png");

			echo "config:<hr><pre>\n$output_config\n</pre>";
			echo "<img src='data:image/png;base64,".base64_encode($imagedata)."'>";

			// form to download file
			?>
<form method="POST"">
    <input type="hidden" name="user_download_file" value="<?php echo $usuario;?>" />
    <input type="submit" value="Download config file" />
</form>
			<?php
		}
		else{
			echo "ERRO: Nom existe o usuario $usuario! (OS case-sensitive??) <br />";
		}

	}
	else{
		$output = shell_exec($commands[$accion].$usuario." 2>&1; echo $?");
		//echo $commands[$accion].$usuario."\n";

		echo "executando $accion [". ($usuario != '' ? "usuario: $usuario": "interface: $interface") ."]:<hr><pre>";
		echo "\n$output\n";
		echo "</pre><hr>";
	}

}


?>


<br />

<form method="POST">
Acci&oacuten; 
	<select name="accion">
	<option value="viewuser"> viewuser </option> 
	<option value="adduser"> adduser </option> 
	<option value="deluser"> deluser </option> 
	<option value=""> -- -- -- </option> 
	<option value="show"> show wg0 </option> 
	</select>
<br />
<br />
Usuario: <input type="text" size=20 value="" name="usuario">
<br />
<br />
<hr>
Lista usuarios actuais:
<pre>
<?php
echo shell_exec('ls -1 '.$path_wg_config.'/users/');
?>
</pre>
<hr>

<input type=submit>
</form>

</body>
</html>

