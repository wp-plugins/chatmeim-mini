<?php
/*
Plugin Name: ChatMe Mini
Plugin URI: http://www.chatme.im/
Description: This plugin add the javascript code for ChatMe Mini a Jabber/XMPP group chat for your WordPress. Also add ChatMe Shortcode and Widget for more chat integrations.
Version: 5.2.0
Author: camaran
Author URI: http://www.chatme.im
Text Domain: chatmini
Domain Path: /languages/
*/

class ChatMe {

        protected $default = array(
    		'chatme_cache' 				=> 'true',
    		'jappix_url' 				=> 'https://webchat.chatme.im',
			'chat' 						=> '@chatme.im',
			'anonymous'					=> 'anonymous.chatme.im',
			'default_room' 				=> 'piazza@conference.chatme.im',
			'adminjid'					=> 'admin@chatme.im',
			'dlng' 						=> 'en',
			'language_dir'				=> '/languages/',
			'style'						=> '#jappix_popup { z-index:99999 !important }',
			'auto_login' 				=> 'false',
	    	'animate' 					=> 'false',
	    	'auto_show' 				=> 'false',
			'nickname'	    			=> '',
			'loggedonly'				=> false,
			'icon'						=> 'https://cdn.chatme.im/wp-content/themes/chatme2/images/chat-mini.png',
			'plugin_options_key'    	=> 'chatme-mini',
			'plugin_options_short'		=> 'chatme-shortcode',
			'mini_error_link' 	    	=> 'http://chatme.im/forums/?chatmeim-mini',
			'mini_disable_mobile' 		=> 'false',
			'priority'					=> 1,
			'open_passwords'			=> '',
			'chat_domains' 		        => 'https://webchat.domains',
			'muc_url' 		            => 'https://conference.chatme.im',
			'conference_domain' 	    => '@conference.chatme.im',	
			'room' 					    => '<option value="piazza@conference.chatme.im">Piazza</option>
									        <option value="support@conference.chatme.im">Support</option>
									        <option value="rosolina@conference.chatme.im">Rosolina</option>
									        <option value="politica@conference.chatme.im">Politica</option>',
    		'domains_status'     	    => "http://webchat.domains/status/",
    		'status' 				    => "http://webchat.chatme.im/status/",
    		'chat_powered' 			    => '<div><small>Chat powered by <a href="http://chatme.im" target="_blank">ChatMe</a></small></div>',  
            //Default Variables
            //userStatus
        	'userStatus_user'           => 'admin@chatme.im',
        	'userStatus_hosted'    	    => false,
        	'userStatus_link'      	    => false,   
            //chatRoom    
            'chatRoom_anon' 		    => true,
            //chatRoomIframe
            'chatRoomIframe_room'       => 'piazza',
            'chatRoomIframe_width'	    => '100%',
            'chatRoomIframe_height'     => '100%',
            'chatRoomIframe_hosted' 	=> false,
            'chatRoomIframe_powered'    => true,

			);

    public function __construct() {

		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_chatme_mini_links') );
		add_action('init',          	array( $this, 'chatme_mini_init') );
		add_action('admin_menu', 	array( $this, 'chatme_menu') );
		add_action('admin_init',    array( $this, 'chatme_admin_init') );

	}

    function chatme_mini_init() {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'chatmini', false, $plugin_dir . $this->default['language_dir'] );
    }

    function add_action_chatme_mini_links ( $links ) {
    	$mylinks = array( '<a href="' . admin_url( 'admin.php?page=chatme-page' ) . '">' . __( 'Settings', 'chatmini' ) . '</a>', );
    	return array_merge( $links, $mylinks );
    }

	function chatme_menu() {
  		$my_admin_menu_page = add_menu_page( __('ChatMe', 'chatmini'), __('ChatMe', 'chatmini'), 'manage_options', 'chatme-page', array($this, 'chatme_admin'), 'dashicons-format-chat' );
		}

	function chatme_admin_init() {
	//register our settings
		register_setting('mini_chat', 'mini');
		register_setting('mini_chat', 'shortcode');
		register_setting('mini_chat', 'login');
		register_setting('mini_chat', 'status');

		}

    function chatme_admin(){
	    if (!current_user_can('manage_options'))  {
    		wp_die( __('You do not have sufficient permissions to access this page.', 'chatmini') );
    	}
    ?>
    
        <div class="wrap">
		<h2>ChatMe</h2>
		<p><?php _e('<a href="http://chatme.im" target="_blank">www.chatme.im</a>', 'chatmini'); ?></p>
		<p><?php _e('In this page you can enable plugin modules', 'chatmini'); ?></p>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
    			<?php settings_fields( 'chatme' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="mini"><?php _e('Enable ChatMe Mini', 'chatmini'); ?></label></th>
					<td><input type="checkbox" id="mini" name="mini" value="true" <?php checked('true', get_option('mini')); ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="shortcode"><?php _e('Enable ChatMe Shortcode', 'chatmini'); ?></label></th>
					<td><input type="checkbox" id="shortcode" name="shortcode" value="true" <?php checked('true', get_option('shortcode')); ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="login"><?php _e('Enable Login Widget', 'chatmini'); ?></label></th>
					<td><input type="checkbox" id="login" name="login" value="true" <?php checked('true', get_option('login')); ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="status"><?php _e('Enable Status Widget', 'chatmini'); ?></label></th>
					<td><input type="checkbox" id="status" name="status" value="true" <?php checked('true', get_option('status')); ?> /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
        
    <?php    
    }
    
}
spl_autoload_register(function () {
	if (get_option('mini'))  {
		require_once( 'classes/class.chatmini.php' );
	}
	if (get_option('shortcode'))  {
		require_once( 'classes/class.shortcode.php' );
	}
	if (get_option('login'))  {
		require_once( 'classes/class.login_widget.php' );
	}
	if (get_option('status'))  {
		require_once( 'classes/class.status_widget.php' );
	}
});
new ChatMe;
?>