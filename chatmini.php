<?php
/*
Plugin Name: ChatMe Mini
Plugin URI: http://www.chatme.im/
Description: This plugin add the javascript code for ChatMe Mini a Jabber/XMPP group chat for your WordPress.
Version: 4.3.9
Author: camaran
Author URI: http://www.chatme.im
Text Domain: chatmini
Domain Path: /languages/
*/

namespace ChatMe;
class Mini {
    
        private $default = array(
    		'chatme_cache' 			=> 'true',
    		'jappix_url' 			=> 'https://webchat.chatme.im',
			'chat' 			        => '@chatme.im',
			'anonymous'		        => 'anonymous.chatme.im',
			'default_room' 		    => 'piazza@conference.chatme.im',
			'adminjid'		        => 'admin@chatme.im',
			'dlng' 			        => 'en',
			'language_dir'		    => '/languages/',
			'style'			        => '#jappix_popup { z-index:99999 !important }',
			'auto_login' 		    => 'false',
	    	'animate' 		        => 'false',
	    	'auto_show' 		    => 'false',
			'nickname'	    	    => '',
			'loggedonly'		    => false,
			'icon'			        => 'https://cdn.chatme.im/wp-content/themes/chatme2/images/chat-mini.png',
			'plugin_options_key'    => 'chatme-mini',
			'mini_error_link' 	    => 'http://chatme.im/forums/?chatmeim-mini',
			'mini_disable_mobile' 	=> 'false',
			'priority'		=> 1,
			'open_passwords'	=> '',
			);
        
    public function __construct() {
        add_action('init',          array( $this, 'chatme_mini_init') );
        add_action('wp_head',       array( $this, 'chatme_mini_wp_head') );
        add_action('admin_menu',    array( $this, 'chatme_mini_admin_menu') );
        add_action('admin_init',    array( $this, 'chatme_mini_admin_init') );
        $this->resource             = $_SERVER['SERVER_NAME'];
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_chatme_mini_links') );
    }
    
    function chatme_mini_init() {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( 'chatmini', false, $plugin_dir . $this->default['language_dir'] );
    }

    function add_action_chatme_mini_links ( $links ) {
    	$mylinks = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->default['plugin_options_key'] ) . '">' . __( 'Settings', 'chatmini' ) . '</a>', );
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

    function chatme_mini_wp_head() {

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
				'style'			=> wp_kses(get_option('style'), ''),	
				'icon' 			=> esc_url(get_option('icon')),	
				'mini_disable_mobile' 	=> esc_html(get_option('mini_disable_mobile')),	
				'priority'		=> esc_html(get_option('priority')),
				'open_passwords'	=> wp_kses(get_option('open_passwords'),''),
						);
						
		foreach( $setting as $k => $settings )
			if( false == $settings )
				unset( $setting[$k]);
						
		$actual = apply_filters( 'chat_actual', wp_parse_args( $setting, $this->default ) );
		//$actual = wp_parse_args( $setting, $this->default );
        
	if (!$actual['loggedonly'] || is_user_logged_in()) {
        
	$chat = printf( '
    <style type="text/css">
    %s
    #jappix_mini .jm_images_animate { background-image: url(\'%s\') !important; background-repeat: no-repeat;}
    </style>
    <link rel="dns-prefetch" href="%s">			
    <script>
    /* <![CDATA[ */
        jQuery.ajaxSetup({cache: %s});
        jQuery.getScript("%s/server/get.php?l=%s&t=js&g=mini.xml", function() {

        JappixMini.launch({
            connection: {
                domain: "%s",
		resource: "%s",
		priority: %s,
            },

            application: {
                network: {
                    autoconnect: %s,
                },

                interface: {
                    showpane: true,
                    animate: %s,
                    no_mobile: %s,
                    error_link: "%s",
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
		    open_passwords: ["%s"],
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
			$actual['chatme_cache'],
			$actual['jappix_url'],	
			$actual['dlng'],
			$actual['anonymous'],
            		$this->resource,
			$actual['priority'],
			$actual['auto_login'],
			$actual['animate'],
			$actual['mini_disable_mobile'],
			$actual['mini_error_link'],
			$actual['nickname'],
			$actual['adminjid'],
			$actual['default_room'],
			$actual['open_passwords']
		);
    }
    return apply_filters( 'chat_html', $chat );
	}

    function chatme_mini_admin_menu() {
        $my_admin_page = add_options_page( __('ChatMe Mini Options', 'chatmini'), __('ChatMe Mini', 'chatmini'), 'manage_options', $this->default['plugin_options_key'], array($this, 'chatme_mini_options') );
        add_action('load-'.$my_admin_page, array( $this, 'chatme_mini_add_help_tab') );
    }

    function chatme_mini_admin_init() {
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
        register_setting('mini_chat', 'mini_disable_mobile');     
        register_setting('mini_chat', 'priority');   
        register_setting('mini_chat', 'open_passwords');   
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
        <th scope="row"><label for="custom"><?php _e("Insert a custom Jappix Installation url", 'chatmini'); ?></label></th>
        <td><input class="regular-text" aria-describedby="custom-description" type="url" size="50" id="custom" name="custom" placeholder="<?php _e("https://webchat.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('custom'); ?>" /> /server/get.php...<p class="description" id="custom-description"><?php _e("Insert your Jappix installation URL", 'chatmini'); ?></p></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="custom-server"><?php _e("Insert your custom anonymous server", 'chatmini'); ?></label></th>
        <td><input class="regular-text" type="text" id="custom-server" name="custom-server" placeholder="<?php _e("anonymous.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('custom-server'); ?>" /></td>
        </tr>
            
        <tr valign="top">
        <th scope="row"><label for="auto_login"><?php _e("Auto login to the account", 'chatmini'); ?></label></th>
        <td><input type="checkbox" id="auto_login" name="auto_login" value="true" <?php checked('true', get_option('auto_login')); ?> /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><label for="auto_show"><?php _e("Auto show the opened chat", 'chatmini'); ?></label></th>
        <td><input type="checkbox" id="auto_show" name="auto_show" value="true" <?php checked('true', get_option('auto_show')); ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="animate"><?php _e("Display an animated image when the user is not connected", 'chatmini'); ?></label></th>
        <td><input type="checkbox" id="animate" name="animate" value="true" <?php checked('true', get_option('animate')); ?> /><br />
	<input class="regular-text" aria-describedby="animate-description" type="url" size="50" name="icon" placeholder="<?php _e("Custom Icon URL", 'chatmini'); ?>" value="<?php echo get_option('icon'); ?>" /><p class="description" id="animate-description"><?php _e("Insert your custom icon url, default: https://webchat.chatme.im/app/images/sprites/animate.png size: 80x74 px", 'chatmini'); ?></p>
	</td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><label for="join_groupchats"><?php _e("Chat rooms to join (if any)", 'chatmini'); ?></label></th>
        <td><input aria-describedby="join_groupchats-description" class="regular-text" type="text" id="join_groupchats" name="join_groupchats" placeholder="<?php _e("piazza@conference.chatme.im", 'chatmini'); ?>" value="<?php echo get_option('join_groupchats'); ?>" /><p class="description" id="join_groupchats-description"><?php _e('For create a Chat Room use Desktop <a href="http://chatme.im/elenco-client/" target="_blank">Client</a> or go to <a href="https://conference.chatme.im" target="_blank">Here.</a>', 'chatmini'); ?></p></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="open_passwords"><?php _e("Chat rooms password", 'chatmini'); ?></label></th>
        <td><input aria-describedby="open_passwords-description" class="regular-text" type="password" id="open_passwords" name="open_passwords" placeholder="<?php _e("Chat Room Password", 'chatmini'); ?>" value="<?php echo wp_kses(get_option('open_passwords'),''); ?>" /><p class="description" id="open_passwords-description"><?php _e("The password of Chat Room, please attention the password is visible in HTML code ", 'chatmini'); ?></p></td>
        </tr>
        
        <tr valign="top">
	    <th scope="row"><label for="admin_site"><?php _e("Chat with site admin", 'chatmini'); ?></label></th>
	    <td><input class="regular-text" type="text" id="admin_site" name="admin_site" placeholder="<?php _e("admin", 'chatmini'); ?><?php echo $this->default['chat']; ?>" value="<?php echo get_option('admin_site'); ?>" /> </td>
	    </tr>        

		<tr valign="top">
        <th scope="row"><label for="all"><?php _e("Available only for logged users", 'chatmini'); ?></label></th>
        <td><input type="checkbox" id="all" name="all" value="true" <?php checked('true', get_option('all')) ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="mini_disable_mobile"><?php _e("Hide for mobile user", 'chatmini'); ?></label></th>
        <td><input type="checkbox" id="mini_disable_mobile" name="mini_disable_mobile" value="true" <?php checked('true', get_option('mini_disable_mobile')) ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><label for="priority"><?php _e("Priority", 'chatmini'); ?></label></th>
        <td>
        	<select id="priority" name="priority">
        		<option value="1" <?php selected('1', get_option('priority')); ?>><?php _e("Low", 'chatmini'); ?></option>
        		<option value="10" <?php selected('10', get_option('priority')); ?>><?php _e("Medium", 'chatmini'); ?></option>
        		<option value="100" <?php selected('100', get_option('priority')); ?>><?php _e("Height", 'chatmini'); ?></option>
       		</select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><label for="language"><?php _e("Mini Jappix language", 'chatmini'); ?></label></th>
        <td>
        <select id="language" name="language">
        <option value="de" <?php selected('de', get_option('language')); ?>><?php _e("Deutsch", 'chatmini'); ?></option>
        <option value="en" <?php selected('en', get_option('language')); ?>><?php _e("English", 'chatmini'); ?></option>
        <option value="eo" <?php selected('eo', get_option('language')); ?>><?php _e("Esperanto", 'chatmini'); ?></option>
        <option value="es" <?php selected('es', get_option('language')); ?>><?php _e("Espa&ntilde;ol", 'chatmini'); ?></option>
        <option value="fr" <?php selected('fr', get_option('language')); ?>><?php _e("Fran&ccedil;ais", 'chatmini'); ?></option>
        <option value="it" <?php selected('it', get_option('language')); ?>><?php _e("Italiano", 'chatmini'); ?></option>
        <option value="ja" <?php selected('ja', get_option('language')); ?>><?php _e("Japan", 'chatmini'); ?></option>
        <option value="nl" <?php selected('nl', get_option('language')); ?>><?php _e("Nederlands", 'chatmini'); ?></option>
        <option value="pl" <?php selected('pl', get_option('language')); ?>><?php _e("Polski", 'chatmini'); ?></option>
        <option value="ru" <?php selected('ru', get_option('language')); ?>><?php _e("Russian", 'chatmini'); ?></option>
        <option value="sv" <?php selected('sv', get_option('language')); ?>><?php _e("Svenska", 'chatmini'); ?></option>
        <option value="hu" <?php selected('hu', get_option('language')); ?>><?php _e("Hungarian", 'chatmini'); ?></option>
        </select>
        </td>
        </tr>

	<tr valign="top">
        	<th scope="row"><label for="style"><?php _e('Custom Style', 'chatmini'); ?></label></th>
        	<td><textarea class="large-text code" aria-describedby="style-description" id="style" name="style" rows="4" cols="50"><?php echo wp_kses(get_option('style'),''); ?></textarea><br /> <p class="description" id="style-description"><?php _e('For Advance use try chat_html hook', 'chatmini') ?></p></td>
        </tr>

    </table>
    <?php submit_button(); ?>
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
new \ChatMe\Mini;
?>