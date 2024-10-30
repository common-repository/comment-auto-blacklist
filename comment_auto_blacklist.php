<?php
/**
 * @package Comment AutoBlacklist
 * @version 0.1
 */
/*
Plugin Name: Comment AutoBlacklist
Plugin URI: http://www.yriase.fr/
Description: Comment AutoBlacklist automatically adds to the comments blacklist the IP of spam comments
Author: Hugo Giraud
Version: 0.1
Author URI: http://www.yriase.fr/
*/
if (!class_exists("CommentAutoBlacklist")) {
	class CommentAutoBlacklist {
	
		public function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_menu',  array('CommentAutoBlacklist', 'addAdminPage') );
				register_activation_hook( __FILE__, array('CommentAutoBlacklist', 'setup') );
			}
			add_filter('pre_comment_approved' , array('CommentAutoBlacklist', 'flood_filter' ), 1, 2);
		}
	
		public function setup() {
			$default = array( 'blockedIPs' => serialize(array()) );
			update_option( 'commentautoblacklist', $default );
		}
		
		public function addAdminPage() {
			$page = add_options_page(__( 'CommentAutoBlacklist', 'commentautoblacklist' ), __( 'CommentAutoBlacklist', 'commentautoblacklist' ), 10, 'commentautoblacklist',
					 array('CommentAutoBlacklist', 'adminPage'));
			add_action( 'admin_head-' . $page, array('CommentAutoBlacklist', 'addAdminHead') );
			add_filter( 'ozh_adminmenu_icon_commentautoblacklist', array('CommentAutoBlacklist', 'addOzhIcon') );
		}
		
		public function addAdminHead() 
		{ 
			?>
				<link rel="stylesheet" href="<?php echo get_bloginfo( 'home' ) . '/' . PLUGINDIR . '/comment-auto-blacklist/style/admin.css' ?>" type="text/css" media="all" />
			<?php
		}

		public function addOzhIcon() 
		{
			return get_bloginfo( 'home' ) . '/' . PLUGINDIR . '/comment-auto-blacklist/style/ozh_icon.png';
		}

		public function adminPage() 
		{
			if(isset($_POST['save'])) 
			{
				$data = $_POST['config'];
				$data['blockedIPs'] = serialize( explode("\n", $data['blockedIPs']) );
				update_option( 'commentautoblacklist', $data );
			} elseif(isset($_POST['reset'])) 
			{
				CommentAutoBlacklist::setup();
			}

			$options = get_option( 'commentautoblacklist' );

			?> 
				<div id="commentautoblacklist" class="wrap" >

					<div id="commentautoblacklist-credits">
						<h3><?php _e('Credits', 'commentautoblacklist') ?> : </h3>
						<?php _e('Developped by', 'commentautoblacklist') ?> <a href="http://www.yriase.fr/" target="_blank">Yriase</a> <br />
						<?php _e('Icon created by', 'commentautoblacklist') ?><a href="http://thenounproject.com/iconify">Scott Lewis</a> <br />
					</div>

					<div id="commentautoblacklist-icon" class="icon32"><br/></div>
					
					<h2><?php _e( 'Comment Auto Blacklist Configuration', 'commentautoblacklist' ) ?></h2>

					<form action="" method="post">
						
						<?php 
							$comments = get_comments(array('status' => 'spam'));
							$spamIPs = array();
							foreach($comments as $comment)
							{
								$spamLine = $comment->comment_author_IP . (strlen($comment->comment_author) ? ' - ' . $comment->comment_author : '') 
												. (strlen($comment->comment_author_email) ? ' - ' . $comment->comment_author_email : '');
								if(!in_array($spamLine, $spamIPs))
									$spamIPs[] = $spamLine;
							}
							$spamIPs = join("\n", $spamIPs);
						?>
						<?php _e( 'Spam comments IP addresses', 'commentautoblacklist' ) ?> <br />
						<textarea name="config[spamIPs]" cols="70" rows="6"><?php echo $spamIPs; ?></textarea> <br />
						
						<?php _e( 'Blocked IPs', 'commentautoblacklist' ) ?> <br />
						<textarea name="config[blockedIPs]" cols="70" rows="6"><?php if($options['blockedIPs']) echo join("\n", unserialize($options['blockedIPs'])); ?></textarea>

						<br /><br />
						<input type="submit" class="button-primary" name="save" value="<?php _e('Save', 'commentautoblacklist') ?>" /><br />
						<input type="submit" class="button-secondary" name="reset" value="<?php _e('Reset', 'commentautoblacklist') ?>" />
					</form>
				</div>
			<?php
		}
	
		function flood_filter( $approved , $commentdata ) 
		{				
			$return_spam = false;
			
			$blacklist = get_option('blacklist_keys');
			$blacklist = explode("\n", $blacklist);
			
			if(!in_array($commentdata['comment_author_IP'], $blacklist))
			{
				$blacklist[] = $commentdata['comment_author_IP'];
				$return_spam = true;
			}
			
			$options = get_option( 'commentautoblacklist' );
			$ips  = unserialize($options['blockedIPs']);
			if(!in_array($commentdata['comment_author_IP'], $ips))
			{
				$ips[] = $commentdata['comment_author_IP'];
				update_option('commentautoblacklist', $ips);
				$return_spam = true;
			}
			
			$blacklist = join("\n", $blacklist);
			update_option('blacklist_keys', $blacklist);
			
			return $return_spam ? "spam" : true;
		}
	}
}



if (class_exists("CommentAutoBlacklist")) {
	$pa = new CommentAutoBlacklist();
}
