<?php
function sso_options() {
	$url='<strong>'.get_site_url().'/sso/'.'</strong>';
	
	$sso_options[] = array(  "name" => "Settings",
            "type" => "heading",
			"desc" => "");
	
	$sso_options[] = array(	"name" => "API key",
			"desc" => "The API key links together your different systems. It is an arbitrary string of characters you can generate yourself. Just make sure it is the same for all domains you want to connect.",
			"id" => "sso_key",
			"type" => "text");

	$sso_options[] = array(	"name" => "SSO server URL",
			"desc" => 'The URL to the SSO server. This is a piece of software you will need to install on your web server. You can download the SSO server software <a href="https://github.com/choppedcode/sso-server" target="_blank">here</a>.',
			"id" => "sso_url",
			"type" => "text");
	
	return $sso_options;
}

function sso_add_admin() {
	$sso_options=sso_options();

	if (isset($_GET['page']) && ($_GET['page'] == "sso-cp")) {

		delete_option('sso_bridge_log'); //not used since version 1.0.2
		
		if ( isset($_REQUEST['action']) && 'install' == $_REQUEST['action'] ) {
			delete_option('sso_log');

			foreach ($sso_options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
				}
			}
			header("Location: options-general.php?page=sso-cp&installed=true");
			die;
		}
	}

	add_options_page('Single Sign On', 'Single Sign On', 'administrator', 'sso-cp','sso_admin');
}

function sso_admin() {
	$controlpanelOptions=sso_options();

	if ( isset($_REQUEST['installed']) ) echo '<div id="message" class="updated fade"><p><strong>'.'Single Sign On'.' installed.</strong></p></div>';
	if ( isset($_REQUEST['error']) ) echo '<div id="message" class="updated fade"><p>The following error occured: <strong>'.$_REQUEST['error'].'</strong></p></div>';
	
	?>
<div class="wrap">
<div id="cc-left" style="position:relative;float:left;width:80%">
<h2><b>Single Sign On</b></h2>

	<?php
	$sso_version=get_option("sso_version");
	$submit='Update';
	?>
<form method="post">

<?php require(dirname(__FILE__).'/includes/cpedit.inc.php')?>

<p class="submit"><input name="install" type="submit" value="<?php echo $submit;?>" /> <input
	type="hidden" name="action" value="install"
/></p>
</form>
<hr />
<?php  
	//if (get_option('sso_debug')) {
		$r=get_option('sso_log');
		if ($r) {
			echo '<h2 style="color: green;">Debug log</h2>';
			echo '<table>';
			$v=$r;
			foreach ($v as $m) {
				echo '<tr>';
				echo '<td>'.date('d M H:i:s',$m[0]).'</td>';
				echo '<td>'.$m[1].'</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '<hr />';
		}
	//}
?>

</div> <!-- end cc-left -->
<?php
	//require(dirname(__FILE__).'/includes/support-us.inc.php');
	//zing_support_us('sso','sso','sso',FILEPRESS_VERSION);
}
add_action('admin_menu', 'sso_add_admin'); ?>