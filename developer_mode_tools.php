<?php
/*
Plugin Name: Developer Mode Tools
Author: Tyler Gerig
Version: 1.2
Description: Add developer mode options to your wordpress installation.
License: GNU General Public License v2 or later
*/

// Big props to Brian Ferdinand for sitting down with me in my time of need and helping me with a lot of functionality to my plugin.

/**
*Add an options page for the plugin.
*
*@since 1.0.
*
*@return void
*/
function check_admin_page(){
	$screen = get_current_screen();
	if(isset( $_POST['tgdmt_plugin_noncename']) && wp_verify_nonce( $_POST['tgdmt_plugin_noncename'], plugins_url( __FILE__))){
		if($screen->base == 'settings_page_tgdmt_options_page'){
			if($_POST){
				$tgdmt_menus_to_remove = array();
				$tgdmt_menus = $_POST;
				foreach($tgdmt_menus as $tgdmt_menu){
					if($tgdmt_menu != 'Save Changes'){
						$tgdmt_menus_to_remove[] = $tgdmt_menu;
					}
				}
				update_option( 'tgdmt_menu_settings', array_map('sanitize_text_field', $tgdmt_menus_to_remove) );
			}
		}
	}else{
		return;
	}

}
add_action('admin_head', 'check_admin_page');

function tgdmt_remove_menus(){
	if(!current_user_can('manage_options')){
		if(get_option('tgdmt_menu_settings')){
			$tgdmt_remove = get_option('tgdmt_menu_settings');
			foreach($tgdmt_remove as $remove){
				remove_menu_page($remove);
			}
		}
	}
}
add_action('admin_menu', 'tgdmt_remove_menus', 11);

function tgdmt_add_options_page(){
	//Add new page under the "Settings tab"
	add_options_page(
		__( 'Developer Mode Tools Options' ),
		__( 'Developer Mode Tools Options' ),
		'manage_options',
		'tgdmt_options_page',
		'tgdmt_render_options_page'
	);
}

add_action( 'admin_menu', 'tgdmt_add_options_page' );


function tgdmt_menu_settings() {
	// Register a binary value called ""
	register_setting(
		'tgdmt_menu_settings',
		'tgdmt_menu_settings',
		''
	);
}
add_action('admin_init','tgdmt_menu_settings');


function tgdmt_render_options_page(){
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e( 'Developer Mode Tools Options'); ?></h2>
		<?php _e('<h3>Disable menu options for other users</h3>'); ?>
		<form action="<?php //plugins_url( 'tgdmt_update_menu.php' , dirname(__FILE__) )?>" method="post">
			<p>
				<?php wp_nonce_field(plugins_url(__FILE__), 'tgdmt_plugin_noncename'); ?>
				<?php
					global $menu;
						$i = 0;
							foreach($menu as $item){
									
								if($item[0] != ''){
									$menu_name = trim(str_replace(range(0,9),'',$item[0]));
									$status = get_option('tgdmt_status',0);
									$disable = '<input id="tgdmt_status" name="tgdmt_status_'.$i.'"style="margin-top:2px; margin-bottom:2px; margin-right: 10px;" type="checkbox" value=" '.$item[2].' " ' . checked( 1, $status, false ) . ' />'. $menu_name . '<br>';
									echo $disable;
									$i++;
								}
							}
				?>
			</p>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'tgdmt' ); ?>">
		</form>
	</div>
	<?php
}