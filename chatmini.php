<?php
/*
Plugin Name: ChatMe Mini
Plugin URI: http://www.chatme.im/
Description: This plugin add the javascript code for ChatMe Mini a Jabber/XMPP group chat for your WordPress.
Version: 4.3.0
Author: camaran
Author URI: http://www.chatme.im
Text Domain: chatmini
Domain Path: /languages/
*/

class ChatMe_Mini {
    
private $default = array (
			'jappix_url' 		=> 'https://webchat.chatme.im',
			'chat' 			=> '@chatme.im',
			'anonymous'		=> 'anonymous.chatme.im',
			'default_room' 		=> 'piazza@conference.chatme.im',
			'adminjid'		=> 'admin@chatme.im',
			'dlng' 			=> 'en',
			'language_dir'		=> '/languages/',
			'style'			=> '#jappix_popup { z-index:99999 !important }',
			'auto_login' 		=> 'false',
	    		'animate' 		=> 'false',
	    		'auto_show' 		=> 'false',
			'nickname'	    	=> '',
			'loggedonly'		=> false,
			'icon'			=> 'https://webchat.chatme.im/app/images/sprites/animate.png'
			);
    
    public function __construct() {
        add_action('wp_head',       array( $this, 'get_chatme_mini') );
        add_action('admin_menu',    array( $this, 'chatme_mini_menu') );
        add_action('admin_init',    array( $this, 'register_mysettings') );
        add_action('init',          array( $this, 'my_plugin_init') );
        $this->resource             = $_SERVER['SERVER_NAME'];
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_chatme_mini_links') );
    }
    
    function my_plugin_init() {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'chatmini', null, $plugin_dir . $this->default['language_dir'] );
    }

    function add_action_chatme_mini_links ( $links ) {
    	$mylinks = array( '<a href="' . admin_url( 'options-general.php?page=chatme-mini' ) . '">' . __( 'Settings', 'chatmini' ) . '</a>', );
    	return array_merge( $links, $mylinks );
    }

      	function chatme_mini_add_help_tab () {
          	$screen = get_current_screen();

          	$screen->add_help_tab( array(
              	      	'id'		=> 'chatme_mini_help_tab_2',
              	      	'title'		=> __('anonymous server', 'chatmini'),
              	      	'content'	=> '<p>' . __( 'The anonymous server of your XMPP service, default: anonymous.chatme.im', 'chatmini' ) . '</p>',
          	      	) );

          	$screen->set_help_sidebar(
                              __('<p><strong>Other Resources</strong></p><p><a href="https://jappix.org/" target="_blank">Jappix Official Site</a></p><p><a href="https://github.com/jappix/jappix/wiki" target="_blank">Jappix Official Documentation</a></p><p><a href="http://xmpp.net" target="_blank">XMPP.net</a></p><p><a href="http://chatme.im" target="_blank">ChatMe Site</a></p>', 'chatmini')
                             );
      	      	}

    function get_chatme_mini() {

          	$current_user = wp_get_current_user();
		
		$setting = array(
				'jappix_url' 		=> esc_url(get_option('custom')),
				'anonymous'		=> esc_html(get_option('custom-server')),
				'adminjid'		=> esc_html(get_option('admin_site')),
				'dlng' 			=> esc_html(get_option('language')),
				'auto_login' 		=> esc_html(get_option('auto_login')),
	    			'animate' 		=> esc_html(get_option('animate')),
	    			'auto_show' 		=> esc_html(get_option('auto_show')),
				'default_room' 		=> esc_html(get_option('join_groupchats')),
				'nickname'		=> $current_user->display_name,	
				'loggedonly'		=> esc_html(get_option('all')),		
				'style'			=> esc_html(get_option('style')),	
				'icon' 			=> esc_url(get_option('icon')),	
						);
						
		foreach( $setting as $k => $settings )
			if( false == $settings )
				unset( $setting[$k]);
						
		$actual = wp_parse_args( $setting, $this->default );
        
	if (!$actual['loggedonly'] || is_user_logged_in()) {
        
	    printf( '
    <style type="text/css">
    %s
    #jappix_mini .jm_images_animate { background-image: url(\'%s\') !important; background-repeat: no-repeat;}
    </style>
    <link rel="dns-prefetch" href="%s">			
    <script>
    /* <![CDATA[ */
        jQuery.ajaxSetup({cache: true});
        jQuery.getScript("%s/server/get.php?l=%s&t=js&g=mini.xml", function() {

        JappixMini.launch({
            connection: {
                domain: "%s",
		        resource: "",
            },

            application: {
                network: {
                    autoconnect: %s,
                },

                interface: {
                    showpane: true,
                    animate: %s,
                },

                user: {
                    random_nickname: false,
                    nickname: "%s",
                },

                chat: {
                    suggest: ["%s"],
                },

                groupchat: {
                    open: ["%s"],
                    suggest: ["piazza@conference.chatme.im","support@conference.chatme.im"],
                },
            },
        });
    });
/* ]]> */ 
</script>', 
			$actual['style'],
			$actual['icon'],
			$actual['jappix_url'],
			$actual['jappix_url'],	
			$actual['dlng'],
			$actual['anonymous'],
			$actual['auto_login'],
			$actual['animate'],
			$actual['nickname'],
			$actual['adminjid'],
			$actual['default_room']
		);
    }
	}

    function chatme_mini_menu() {
        $my_admin_page = add_options_page('ChatMe Mini Options', 'ChatMe Mini', 'manage_options', 'chatme-mini', array($this, 'chatme_mini_options') );
        add_action('load-'.$my_admin_page, array( $this, 'chatme_mini_add_help_tab') );
    }

    function register_mysettings() {
	//register our settings
	register_setting('mini_chat', 'custom');
	register_setting('mini_chat', 'custom-server');
	register_setting('mini_chat', 'language');
	register_setting('mini_chat', 'auto_login');
	register_setting('mini_chat', 'auto_show');
	register_setting('mini_chat', 'animate');
	register_setting('mini_chat', 'join_groupchats');
	register_setting('mini_chat', 'admin_site');
        register_setting('mini_chat', 'all');
        register_setting('mini_chat', 'style');
        register_setting('mini_chat', 'icon');        
    }

    function chatme_mini_options() {
        if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.', 'chatmini') );
    }
?>
 <div class="wrap">
<h2>ChatMe Mini</h2>
<p><?php _e("For more information visit <a href='http://www.chatme.im' target='_blank'>www.chatme.im</a>", 'chatmini'); ?> - <?php _e('<a href="https://webchat.chatme.im/?r=support" target="_blank">Support Chat Room</a>', 'chatmini'); ?></p>
<p><?php _e("For subscribe your account visit <a href='http://chatme.im/servizi/domini-disponibili/' target='_blank'>http://chatme.im/servizi/domini-disponibili/</a>", 'chatmini'); ?></p>

<form method="post" action="options.php">
    <?php settings_fields( 'mini_chat' ); ?>
    <table class="form-table">

		<tr valign="top">
        <th scope="row"><?php _e("Insert a custom Jappix Installation url", 'chatmini'); ?></th>
        <td><input type="url" size="50 name="custom" placeholder="<?php _e("https://webchat.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('custom'); ?>" /> /server/get.php...<br/><?php _e("Insert your Jappix installation URL", 'chatmini'); ?></td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e("Insert your custom anonymous server", 'chatmini'); ?></th>
        <td><input type="text" name="custom-server" placeholder="<?php _e("anonymous.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('custom-server'); ?>" /></td>
        </tr>
            
        <tr valign="top">
        <th scope="row"><?php _e("Auto login to the account", 'chatmini'); ?></th>
        <td><input type="checkbox" name="auto_login" value="true" <?php checked('true', get_option('auto_login')); ?> /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e("Auto show the opened chat", 'chatmini'); ?></th>
        <td><input type="checkbox" name="auto_show" value="true" <?php checked('true', get_option('auto_show')); ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e("Display an animated image when the user is not connected", 'chatmini'); ?></th>
        <td><input type="checkbox" name="animate" value="true" <?php checked('true', get_option('animate')); ?> /><br />
	<input type="url" size="50" name="icon" placeholder="<?php _e("Custom Icon URL", 'chatmini'); ?>" value="<?php echo get_option('icon'); ?>" /><br/><?php _e("Insert your custom icon url, default: https://webchat.chatme.im/app/images/sprites/animate.png size: 80x74 px", 'chatmini'); ?>
	</td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e("Chat rooms to join (if any)", 'chatmini'); ?></th>
        <td><input type="text" name="join_groupchats" placeholder="<?php _e("piazza@conference.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('join_groupchats'); ?>" /></td>
        </tr>
        
        <tr valign="top">
	    <th scope="row"><?php _e("Chat with site admin", 'chatmini'); ?></th>
	    <td><input type="text" name="admin_site" placeholder="<?php _e("admin", 'chatmini'); ?><?php echo $this->default['chat']; ?>" value="<?php echo get_option('admin_site'); ?>" /> </td>
	    </tr>        

		<tr valign="top">
        <th scope="row"><?php _e("Available only for logged users", 'chatmini'); ?></th>
        <td><input type="checkbox" name="all" value="true" <?php checked('true', get_option('all')) ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e("Mini Jappix language", 'chatmini'); ?></th>
        <td>
        <select id="language" name="language">
        <option value="de" <?php selected('de', get_option('language')); ?>>Deutsch</option>
        <option value="en" <?php selected('en', get_option('language')); ?>>English</option>
        <option value="eo" <?php selected('eo', get_option('language')); ?>>Esperanto</option>
        <option value="es" <?php selected('es', get_option('language')); ?>>Espa&ntilde;ol</option>
        <option value="fr" <?php selected('fr', get_option('language')); ?>>Fran&ccedil;ais</option>
        <option value="it" <?php selected('it', get_option('language')); ?>>Italiano</option>
        <option value="ja" <?php selected('ja', get_option('language')); ?>>Ja</option>
        <option value="nl" <?php selected('nl', get_option('language')); ?>>Nederlands</option>
        <option value="pl" <?php selected('pl', get_option('language')); ?>>Polski</option>
        <option value="ru" <?php selected('ru', get_option('language')); ?>>Ru</option>
        <option value="sv" <?php selected('sv', get_option('language')); ?>>Svenska</option>
        <option value="hu" <?php selected('hu', get_option('language')); ?>>Hungarian</option>
        </select>
        </td>
        </tr>

	<tr valign="top">
        	<th scope="row"><?php _e('Custom Style', 'conversejs'); ?></th>
        	<td><textarea name="style" rows="4" cols="50"><?php echo esc_html(get_option('style')); ?></textarea></td>
        </tr>

    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'chatmini') ?>" />
    </p>
    <p><?php _e('For Ever request you can use our <a href="http://chatme.im/forums" target="_blank">forum</a>', 'chatmini') ?></p>

</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8CTUY8YDK5SEL">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal -  The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
<?php 
    }
} 
new ChatMe_Mini;
?>