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
		add_action('init',          array( $this, 'chatme_mini_init') );

	}

    function chatme_mini_init() {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'chatmini', false, $plugin_dir . $this->default['language_dir'] );
    }

    function add_action_chatme_mini_links ( $links ) {
    	$mylinks = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->default['plugin_options_key'] ) . '">' . __( 'Settings', 'chatmini' ) . '</a>', '<a href="' . admin_url( 'options-general.php?page=' . $this->default['plugin_options_short'] ) . '">' . __( 'Shortcode', 'chatmini' ) . '</a>', );
    	return array_merge( $links, $mylinks );
    }
}
spl_autoload_register(function () {
	require_once( 'classes/class.chatmini.php' );
	require_once( 'classes/class.shortcode.php' );
	require_once( 'classes/class.login_widget.php' );
	require_once( 'classes/class.status_widget.php' );
});
new ChatMe;
?>