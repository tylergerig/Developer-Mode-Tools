<?php
/*
Plugin Name: Developer Mode Tools 02
Author: Tyler Gerig
Version: 1.1
Description: Add developer mode options to your wordpress installation.
License: GNU General Public License v2 or later
*/

function pretty_dump($dump){
	echo '<pre>';
	var_dump($dump);
	echo '</pre>';
}

/**
*Add an options page for the plugin.
*
*@since 1.0.
*
*@return void
*/
function check_admin_page(){
	$screen = get_current_screen();
	//pretty_dump($screen->base);
	if($screen->base == 'settings_page_tgdmt_options_page'){
		if($_POST){
			$menus_to_remove = array();
			$tgdmt_menus = $_POST;
			foreach($tgdmt_menus as $tgdmt_menu){
				if($tgdmt_menu != 'Save Changes'){
					$menus_to_remove[] = $tgdmt_menu;
				}
			}
			update_option( 'tgdmt_menu_settings', array_map('sanitize_text_field', $menus_to_remove) );
			//pretty_dump($menus_to_remove);
		}

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
		<form action=">" method="post">
			<p>
			<?php
			global $menu;
				//pretty_dump($menu);
				$i = 0;	
					foreach($menu as $item){
							
						if($item[0] != ''){
							$menu_name = trim(str_replace(range(0,9),'',$item[0]));
							echo '<input name="menu'.$i.'" type="checkbox" value="'.$item[2].'" ' . checked( 1, '', false ) . ' />'. $menu_name . '<br>';
							$i++;
							
						}
					}
				?>

				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'tgdmt' ); ?>">
			</p>
		</form>
	</div>
	<?php

	

}