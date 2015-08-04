<?php
class ShortCodes extends ChatMe {

	function __construct() {
		self::register_shortcodes( $this->shortcodes_core() );
		add_action('admin_menu',  array( $this, 'chatme_shortcode_menu') );
	}

	private function shortcodes_core() {
		$core = array(
			'userStatus'			=>	array( 'function' => 'userStatus_short' ),
			'chatRoom'			    =>	array( 'function' => 'chatRoom_short' ),
			'chatRoomIframe'		=>	array( 'function' => 'chatRoomIframe_short' ),
			'swatchTime'			=>	array( 'function' => 'swatchTime_short' ),
			);
		return $core;
	}
	
	function chatme_shortcode_menu() {
  		$my_admin_page = add_submenu_page('chatme-page',  __('ChatMe Shortocode Help', 'chatmini'), __('ChatMe Shortcode Help', 'chatmini'), 'manage_options', $this->default['plugin_options_short'], array($this, 'mini_shortcode_help') );
	}
	
function mini_shortcode_help() {
  		if (!current_user_can('manage_options'))  {
    	wp_die( __('You do not have sufficient permissions to access this page.', 'chatmeim-mini-messenger') );
  		} 
	?>
 	<div class="wrap">
	<h1><?php _e('ChatMe Shortocode Help', 'chatmini'); ?></h1>
	<p><?php _e('<b>[userStatus user="users" link=1 hosted=0]</b><br/>This code show user status (online/offline/etc):<ul><li><b>user</b>: insert the user with the domain (example: user@chatme.im)</li><li><b>link</b> (boolean): can be 0 (default) for not link and 1 for link to the user</li><li><b>hosted</b> (boolean): can be 0 (default) for not hosted domain and 1 if you have a custom domain hosted in ChatMe XMPP server</li></ul>', 'chatmini'); ?></p> 
	<p><?php _e('<b>[chatRoom anon=1]</b><br/>This code show a list of default chat room.<ul><li><b>anon</b> (boolean): can be 0 for not anonymous login (require username and password) or 1 (default) for chat only with nickname.</li></ul>', 'chatmini'); ?></p> 
	<p><?php _e('<b>[chatRoomIframe room="room" width="width" height="height" hosted=0]</b><br/>This shortcode show a chat room in your wordpress page:<ul><li><b>room</b>: the name of the chat room (default: piazza@conference.chatme.im)</li><li><b>width</b>: the frame width (default: 100%)</li><li><b>height</b>: the height of frame (default: 100%)</li><li><b>hosted</b> (boolean): can be 0 (default) for not hosted domain and 1 if you have a custom domain hosted in ChatMe XMPP server</li></ul>', 'chatmini'); ?></p> 
	<p><?php _e('<b>[swatchTime]</b><br/>This shortcode show Internet Swatch Time.', 'chatmini'); ?></p> 
	<p><?php _e('For more information visit our <a href="http://chatme.im/forums" target="_blank">forum</a>', 'chatmini'); ?></p> 

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="8CTUY8YDK5SEL">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
	</form>

	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://chatme.im" data-text="Visita chatme.im" data-via="chatmeim" data-lang="it">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

	</div>
<?php 
	}		

    //Stato utente [userStatus user="users" link="1"]
    function userStatus_short($atts)
	    {	
		    $defaults = array(
			    'user'      => $this->default['userStatus_user'],
			    'hosted'    => $this->default['userStatus_hosted'],
			    'link'      => $this->default['userStatus_link'],
			    );
            $atts = shortcode_atts( $defaults, $atts );    
                
            $link = ((bool)$atts['link']) ? ' <a href="xmpp:'. $atts['user'] . '" title="Chatta con ' . $atts['user'] . '">' . $atts['user'] . '</a>' : '';
            
            if ((bool)$atts['hosted']) {
		        return  '<img src="' . $this->default['domains_status'] . $atts['user'] . '" alt="Status">' . $link;
            } else {
		        return '<img src="' . $this->default['status'] . $atts['user'] . '" alt="Status">' . $link;		
            }
	    }	
	
    //Chat Room [chatRoom anon="1"]	
    function chatRoom_short($atts)
	    {
		    $defaults = array(
			    'anon' => $this->default['chatRoom_anon'],
			    );
            $atts = shortcode_atts( $defaults, $atts );    
                
		    if (!(bool)$atts['anon'])  {	
                
		    return '<form method="get" action="' . $this->default['muc_url'] . '" target="_blank" class="form-horizontal">
            	    <select name="room">
					    ' . $this->default['room'] . '
				    </select>
                <button type="submit">Entra nella stanza</button>
            </form> ';
		    } else {
                
		    return '<form method="get" action="' . $this->default['jappix_url'] . '" target="_blank">
            	    <select name="r">
					    ' . $this->default['room'] . '
				    </select>
    			    <input type="text" name="n" placeholder="Nickname" autocomplete="off">
        	    <button type="submit">Entra nella stanza</button>
            </form> ';
		    }
	    }

    //Iframe Chat Room [chatRoomIframe room="room" width="width" height="height"]
    function chatRoomIframe_short($atts)
	    {	
		    $defaults = array(
			    'room' 		=> $this->default['chatRoomIframe_room'],
			    'width' 	=> $this->default['chatRoomIframe_width'],
			    'height' 	=> $this->default['chatRoomIframe_height'],
			    'hosted' 	=> $this->default['chatRoomIframe_hosted'],
				'powered' 	=> $this->default['chatRoomIframe_powered'],
			    );
                $atts = shortcode_atts( $defaults, $atts );
                
				$chat_url = ((bool)$atts['hosted']) ? $this->default['chat_domains'] : $this->default['jappix_url'];
				$powered = ((bool)$atts['powered']) ? $this->default['chat_powered'] : '';
				
				return '<div class="cm-iframe-room"><iframe src="' . $chat_url . '/?r='. $atts['room'] . $this->default['conference_domain'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '" border="0">Il tuo browser non supporta iframe</iframe>' . $powered . '</div>';		
	    }

    //Internet Swatch Time [swatchTime]
    function swatchTime_short()
	    {	
		return "Internet Swatch Time <strong>@" . date('B') . "</strong>";
	    }

    //Registro tutti gli shortcode della classe
	    private function register_shortcodes( $shortcodes ) {
		    foreach ( $shortcodes as $shortcode => $data ) {
			    add_shortcode( $shortcode, array( $this, $data['function']) );
		    }
	    }

}
new ShortCodes;			
?>