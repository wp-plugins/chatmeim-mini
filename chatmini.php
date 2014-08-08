<?php
/*
Plugin Name: ChatMe Mini
Plugin URI: http://www.chatme.im/
Description: This plugin add the javascript code for ChatMe Mini a Jabber/XMPP group chat for your WordPress.
Version: 2.2.4
Author: camaran
Author URI: http://www.chatme.im
*/

//Custom Variables (YOU NOT EDIT)
$GLOBALS['jappix_url'] 		= "http://webchat.chatme.im"; 														//jappix installation
$GLOBALS['jappix_url_hosted'] 	= "http://webchat.domains"; 														//jappix installation for hosted domains
$GLOBALS['conference'] 		= "@conference.chatme.im"; 															//server of conference
$GLOBALS['chat'] 		= "chatme.im"; 																		//server of conference
$GLOBALS['anonymous'] 		= "anonymous.chatme.im"; 															//Server for anonymous chat
$GLOBALS['resource'] 		= $_SERVER['SERVER_NAME']; 															//resource for chat
$GLOBALS['default_room'] 	= "piazza"; 																		//default room
$GLOBALS['style'] 		= "<style type=\"text/css\">#jappix_popup { z-index:99999 !important }</style>"; 	//Style theme compatibility 

add_action('wp_head', 'get_chatme_mini');
add_action('admin_menu', 'chatme_mini_menu');
add_action('admin_init', 'register_mysettings' );
add_action( 'init', 'my_plugin_init' );

function my_plugin_init() {
      $plugin_dir = basename(dirname(__FILE__));
      load_plugin_textdomain( 'chatmini', null, $plugin_dir . '/languages/' );
}

function get_chatme_mini() {
	
	$auto_login = get_option('auto_login') ?: 'false';
	$animate = get_option('animate') ?: 'false'; 
	$auto_show = get_option('auto_show') ?: 'false';
	$lng = get_option('language') ?: 'en';
	$admin_site = (get_option('admin_site') == '') ? 'admin@chatme.im' : get_option('admin_site') . "@" . $GLOBALS['chat'];
	$jquery = (get_option('yet_jquery') != 1) ? '&amp;f=jquery.js' : '';
	$url = (filter_var(get_option('custom'),FILTER_VALIDATE_URL)) ? get_option('custom') : ((get_option('hosted') == 1) ? $GLOBALS['jappix_url_hosted'] : $GLOBALS['jappix_url']); 
	$server = (filter_var(get_option('custom'),FILTER_VALIDATE_URL)) ? get_option('custom-server') : "anonymous.chatme.im";
	
	if(get_option('all') == 1 || get_option('all') == '')
		$all = true;
	else
		$all = false;
	
	if ($all || is_user_logged_in()) {

		if(get_option('join_groupchats') == '')
			$join_groupchats = $GLOBALS['default_room'];
		else
			$join_groupchats = get_option('join_groupchats');
			$groups = explode(',', $join_groupchats);
				foreach ($groups as $value) {
					$group .= '"'.trim($value) . $GLOBALS['conference'] .'", '; 
		}
					
	$group = substr ($group, 0, -2);	
	$nickname = get_userdata(get_current_user_id())->user_login;
	echo "\n".$GLOBALS['style'];
	echo "\n".'
<script type="text/javascript">
/* <![CDATA[ */
   var $jappix = jQuery.noConflict();
   $jappix.ajaxSetup({ cache: false });
   $jappix.getScript("' . $url . '/server/get.php?l=it&t=js&g=mini.xml'.$jquery.'", function() {
JappixMini.launch({
        connection: {
           domain: "'.$server.'",
		   resource: "'.$GLOBALS['resource'].'",
        },

        application: {
           network: {
              autoconnect: false,
           },

           interface: {
              showpane: true,
              animate: '.$animate.',
           },

           user: {
              random_nickname: false,
			  nickname: "'.$nickname.'",
           },

           chat: {
              suggest: ["'.$admin_site.'"],
           },

           groupchat: {
              open: ['.$group.'],
              suggest: ["piazza@conference.chatme.im","support@conference.chatme.im"],
           },
        },
     });
  });
/* ]]> */ 
</script>';
}
}

function chatme_mini_menu() {
  add_options_page('ChatMe Mini Options', 'ChatMe Mini', 'manage_options', 'chatme-mini', 'chatme_mini_options');
}

function register_mysettings() {
	//register our settings
	register_setting('mini_chat', 'custom');
	register_setting('mini_chat', 'custom-server');
	register_setting('mini_chat', 'hosted');	
	register_setting('mini_chat', 'yet_jquery');
	register_setting('mini_chat', 'language');
	register_setting('mini_chat', 'auto_login');
	register_setting('mini_chat', 'auto_show');
	register_setting('mini_chat', 'animate');
	register_setting('mini_chat', 'join_groupchats');
	register_setting('mini_chat', 'all');
	register_setting('mini_chat', 'admin_site');
}

function chatme_mini_options() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.', 'chatmini') );
  }
 ?>
 <div class="wrap">
<h2>ChatMe Mini</h2>
<p><?php _e("For more information visit <a href='http://www.chatme.im' target='_blank'>www.chatme.im</a>", 'chatmini'); ?> - <a href="https://webchat.chatme.im/?r=support" target="_blank">Support Chat Room</a></p>
<p><?php _e("For subscribe your account visit <a href='http://api.chatme.im/register_web' target='_blank'>http://api.chatme.im/register_web</a>", 'chatmini'); ?></p>

<form method="post" action="options.php">
    <?php settings_fields( 'mini_chat' ); ?>
    <table class="form-table">

		<tr valign="top">
        <th scope="row"><?php _e("Insert a custom Jappix Installation url", 'chatmini'); ?></th>
        <td><input type="text" name="custom" value="<?php echo get_option('custom'); ?>" /> /server/get.php...<br/><?php _e("Insert your Jappix installation URL", 'chatmini'); ?></td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e("Insert your custom anonymous server", 'chatmini'); ?></th>
        <td><input type="text" name="custom-server" value="<?php echo get_option('custom-server'); ?>" /><br/><?php _e("Work only with a custom Jappix installation", 'chatmini'); ?></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e("It is a ChatMe Hosted Domains?", 'chatmini'); ?></th>
        <td><input type="checkbox" name="hosted" value="1" <?php checked('1', get_option('hosted')); ?> /> Yes</td>
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
        <td><input type="checkbox" name="animate" value="true" <?php checked('true', get_option('animate')); ?> /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e("Chat rooms to join (if any)", 'chatmini'); ?></th>
        <td><input type="text" name="join_groupchats" value="<?php echo get_option('join_groupchats'); ?>" /> <?php echo $GLOBALS['conference']; ?><br/><?php _e("For more use comma separator (example: piazza, scuola)", 'chatmini'); ?></td>
        </tr>
        
        <tr valign="top">
	    <th scope="row"><?php _e("Chat with site admin", 'chatmini'); ?></th>
	    <td><input type="text" name="admin_site" value="<?php echo get_option('admin_site'); ?>" /> <?php echo $GLOBALS['chat']; ?></td>
	    </tr>        
		
		<tr valign="top">
        <th scope="row"><?php _e("jQuery is yet included", 'chatmini'); ?></th>
        <td><input type="checkbox" name="yet_jquery" value="1" <?php checked('1', get_option('yet_jquery')); ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e("Available only for logged users", 'chatmini'); ?></th>
        <td><input type="checkbox" name="all" value="0" <?php checked('0', get_option('all')) ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e("Mini Jappix language", 'chatmini'); ?></th>
        <td>
        <select id="language" name="language">
        <option value="de" <?php selected('de', get_option('language')); ?>>Deutsch</option>
        <option value="en" <?php selected('en', get_option('language')); ?>>English</option>
        <option value="eo" <?php selected('eo', get_option('language')); ?>>Esperanto</option>
        <option value="es" <?php selected('es', get_option('language')); ?>>Español</option>
        <option value="fr" <?php selected('fr', get_option('language')); ?>>Français</option>
        <option value="it" <?php selected('it', get_option('language')); ?>>Italiano</option>
        <option value="ja" <?php selected('ja', get_option('language')); ?>>日本語</option>
        <option value="nl" <?php selected('nl', get_option('language')); ?>>Nederlands</option>
        <option value="pl" <?php selected('pl', get_option('language')); ?>>Polski</option>
        <option value="ru" <?php selected('ru', get_option('language')); ?>>� усский</option>
        <option value="sv" <?php selected('sv', get_option('language')); ?>>Svenska</option>
        <option value="hu" <?php selected('hu', get_option('language')); ?>>Hungarian</option>
        </select>
        </td>
        </tr>

    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'chatmini') ?>" />
    </p>
    <p>For Ever request you can use our <a href="http://chatme.im/forums" target="_blank">forum</a></p>

</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8CTUY8YDK5SEL">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
<?php } ?>