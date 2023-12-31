<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class RevSliderAddons extends RevSliderFunctions { //before: Rev_addon_Admin
	//private $addon_version_required = '2.0.0'; //this holds the globally needed addon version for the current RS version
	
	private $addon_version_required = array(
		'revslider-whiteboard-addon' => '2.2.0',
		'revslider-backup-addon' => '2.0.0',
		'revslider-gallery-addon' => '2.0.0',
		'revslider-rel-posts-addon' => '2.0.0',
		'revslider-typewriter-addon' => '2.0.0',
		'revslider-sharing-addon' => '2.0.0',
		'revslider-maintenance-addon' => '2.0.0',
		'revslider-snow-addon' => '2.0.0',
		'revslider-particles-addon' => '2.0.0',
		'revslider-polyfold-addon' => '2.0.0',
		'revslider-404-addon' => '2.0.0',
		'revslider-prevnext-posts-addon' => '2.0.0',
		'revslider-filmstrip-addon' => '2.0.0',
		'revslider-login-addon' => '2.0.0',
		'revslider-featured-addon' => '2.0.0',
		'revslider-slicey-addon' => '2.0.0',
		'revslider-beforeafter-addon' => '2.0.0',
		'revslider-weather-addon' => '2.0.0',
		'revslider-panorama-addon' => '2.0.0',
		'revslider-duotonefilters-addon' => '2.0.0',
		'revslider-revealer-addon' => '2.0.0',
		'revslider-refresh-addon' => '2.0.0',
		'revslider-bubblemorph-addon' => '2.0.0',
		'revslider-liquideffect-addon' => '2.0.0',
		'revslider-explodinglayers-addon' => '2.0.0',
		'revslider-paintbrush-addon' => '2.0.0'
	);
	
	public function __construct(){
		//include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	}
	
	/**
	 * get all the addons with information
	 **/
	public function get_addon_list(){

		RevLoader::update_addon_json();

		$addons	= RevLoader::get_option('revslider-addons');
		$addons	= (array)$addons;
		$addons = array_reverse($addons, true);
		//$plugins = get_plugins();
		$plugins = array();
		if(!empty($addons)){
			foreach($addons as $k => $addon){
				if(!is_object($addon)) continue;
				$addon_file_path = RS_PLUGIN_PATH .'addons/'. $addon->slug.'/'.$addon->slug.'.php';
				
				$is_active = RevLoader::get_option($addon->slug);
				if($is_active && $is_active != '0'){
					$is_active = true;
				}else{
					$is_active = false;
				}

				if(file_exists($addon_file_path)){
				//if(array_key_exists($addon->slug.'/'.$addon->slug.'.php', $plugins)){
					//$addons[$k]->full_title	= $plugins[$addon->slug.'/'.$addon->slug.'.php']['Name'];
					$addons[$k]->full_title	= $addon->slug;
					//$addons[$k]->active		= (is_plugin_active($addon->slug.'/'.$addon->slug.'.php')) ? true : false;
					$addons[$k]->active		=  ($is_active) ? true : false;
					//$addons[$k]->installed	= $plugins[$addon->slug.'/'.$addon->slug.'.php']['Version'];
					$addons[$k]->installed	= '6.0';
				}else{
					$addons[$k]->active		= false;
					$addons[$k]->installed	= false;
				}
			}
		}
		return $addons;
	}

	/**
	 * check if any addon is below version x (for RS6.0 this is version 2.0)
	 * if yes give a message that tells to update
	 **/
	public function check_addon_version(){
		$rs_addons	= $this->get_addon_list();
		$update		= array();
		
		if(!empty($rs_addons)){
			foreach($rs_addons as $handle => $addon){
				$installed = $this->get_val($addon, 'installed');
				if(trim($installed) === '') continue;
				if($this->get_val($addon, 'active', false) === false) continue;
				
				$version = $this->get_val($this->addon_version_required, $handle, false);
				if($version !== false && version_compare($installed, $version, '<')){
					$update[$handle] = array(
						'title' => $this->get_val($addon, 'full_title'),
						'old'	=> $installed,
						'new'	=> $this->get_val($addon, 'available'),
						'status'=> '1' //1 is mandatory to use it
					);
				}
			}
		}
		
		return $update;
	}
	
	/**
	 * Install Add-On/Plugin
	 *
	 * @since 6.0
	 */
	public function install_addon($addon, $force = false){

		if(RevLoader::get_option('revslider-valid', 'false') !== 'true') return RevLoader::__('Please activate Slider Revolution', 'revslider');
		//check if downloaded already
		//$plugins	= get_plugins();
		//$addon_path = $addon.'/'.$addon.'.php';
		//if(!array_key_exists($addon_path, $plugins) || $force == true){

		$addon_file_path = RS_PLUGIN_PATH .'addons/'. $addon.'/'.$addon.'.php';
			if(! file_exists($addon_file_path)){
			//download if nessecary
			//RevLoader::update_option($addon, true);
			return $this->download_addon($addon);
		}

		// $addon_path = $addon.'/'.$addon.'.php';
		// $filename = './addons/'.$addon_path;

		// if (! file_exists($filename)) {
		// 		//download if nessecary
		// 	return $this->download_addon($addon);
		// }
		//activate
		$activate = $this->activate_addon($addon);
		
		return $activate;
	}
	
	/**
	 * Download Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function download_addon($addon){
		//global $wp_version, $rslb;
		$rslb	= new RevSliderLoadBalancer();
		if(RevLoader::get_option('revslider-valid', 'false') !== 'true') return RevLoader::__('Please activate Slider Revolution', 'revslider');
		
		$plugin_slug	= basename($addon);
		$plugin_result	= false;
		$plugin_message	= 'UNKNOWN';
		
		$code = RevLoader::get_option('revslider-code', '');
		
		if(0 !== strpos($plugin_slug, 'revslider-')) die( '-1' );
		
		$done	= false;
		$count	= 0;
		
		$download_url = RevLoader::url();
		//track
		//$url = $download_url .'/download.php?code='.$code.'&type='.$plugin_slug;
		$url = 'http://revapi.smartdatasoft.net/v6/call/index.php?code='.$code.'&type='.$plugin_slug.'&ps_base_url_ssl='. _PS_BASE_URL_SSL_;
		$get = RevLoader::wp_remote_post($url, array(
			'user-agent' => 'php/; '. RevLoader::get_bloginfo('url'),
			'body' => '',
			'timeout' => 400
		));

		if($get["info"]["http_code"] != "200" ){
			die("FAILED TO DOWNLOAD");
		}else{
															
			$upload_dir	= RevLoader::wp_upload_dir();
			$file		= $upload_dir['basedir']. '/revslider/templates/' . $plugin_slug . '.zip';
			@mkdir(dirname($file), 0777, true);
			$ret	= @file_put_contents($file, $get['body']);

			//$dis_path = './addons';
			$dis_path = RS_PLUGIN_PATH  . '/addons/';
			$zip = new ZipArchive;
			$res = $zip->open($file);
			if ($res === TRUE) {
				$zip->extractTo($dis_path);
				$zip->close();
				return true;
			}else{
				die("There was a problem. Please try again!");
			}

			@unlink($file);
			return true;
		}
		//$result = activate_plugin( $plugin_slug.'/'.$plugin_slug.'.php' );

		// $rattr	= array(
		// 	'code'		=> urlencode($code),
		// 	'version'	=> urlencode(RS_REVISION),
		// 	'product'	=> urlencode(RS_PLUGIN_SLUG),
		// 	'type'		=> urlencode($plugin_slug)
		// );
		
		// do{
		// 	$url = 'addons/'.$plugin_slug.'/download.php';
		// 	$get = $rslb->call_url($url, $rattr, 'updates');
			
		// 	if(RevLoader::wp_remote_retrieve_response_code($get) == 200){
		// 		$done = true;
		// 	}else{
		// 		$rslb->move_server_list();
		// 	}
			
		// 	$count++;
		// }while($done == false && $count < 5);


		// if(!$get || RevLoader::wp_remote_retrieve_response_code($get) != 200){
		// }else{
		// 	$upload_dir	= RevLoader::wp_upload_dir();
		// 	$file		= $upload_dir['basedir']. '/revslider/templates/' . $plugin_slug . '.zip';
		// 	@mkdir(dirname($file), 0777, true);
		// 	$ret		= @file_put_contents($file, $get['body']);
		// 	WP_Filesystem();

		// 	global $wp_filesystem;

		// 	$upload_dir	= RevLoader::wp_upload_dir();
		// 	$d_path		= WP_PLUGIN_DIR;
		// 	$unzipfile	= unzip_file($file, $d_path);

		// 	if(is_wp_error($unzipfile)){
		// 		define('FS_METHOD', 'direct'); //lets try direct. 

		// 		WP_Filesystem();  //WP_Filesystem() needs to be called again since now we use direct !

		// 		//@chmod($file, 0775);
		// 		$unzipfile = unzip_file($file, $d_path);
		// 		if(is_wp_error($unzipfile)){
		// 			$d_path = WP_PLUGIN_DIR;
		// 			$unzipfile = unzip_file($file, $d_path);

		// 			if(is_wp_error($unzipfile)){
		// 				$f = basename($file);
		// 				$d_path = str_replace($f, '', $file);

		// 				$unzipfile = unzip_file($file, $d_path);
		// 			}
		// 		}
		// 	}
			
		// 	@unlink($file);
		// 	return true;
		// }
		
		//$result = activate_plugin( $plugin_slug.'/'.$plugin_slug.'.php' );
		return false;
	}
	
	/**
	 * Activates Installed Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function activate_addon($addon){
		// Verify that the incoming request is coming with the security nonce
		if(isset($addon)){
			RevLoader::update_option($addon, true);
		}else{
			return false;
		}

		return true;
	}

	/**
	 * Deactivates Installed Add-On/Plugin
	 *
	 * @since    1.0.0
	 */
	public function deactivate_addon($addon){

		RevLoader::update_option($addon, false);
		return true;

		// Verify that the incoming request is coming with the security nonce
		// $result = deactivate_plugins($addon);
		// return (is_wp_error($result)) ? false : true;
	}
}

class Rev_addon_Admin extends RevSliderAddons {}
?>