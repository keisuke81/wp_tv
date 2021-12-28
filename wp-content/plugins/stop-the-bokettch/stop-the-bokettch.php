<?php
/*
Plugin Name: Stop the Bokettch
Plugin URI: http://www.warna.info/archives/2649/
Description: This is a plugin for displaying an alert notification to the ToolBar if you have checked "Discourage search engines from indexing this site" of Site Visibility Options. So This is very useful for missed check options.
Author: jim912
Version: 0.6.2
Author URI: http://www.warna.info/
Text Domain: stop-the-bokettch
Domain Path: /languages/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Stop_the_Bokettch {
	
	private $icon_url;
	private $genericons_dir_url;
	private $version;
	private $genericons_version = '3.1';
	private $publish_notice;
	
	public function __construct() {
		$plugin_data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
		$this->version = $plugin_data['version'];
		$this->publish_notice = get_option( 'bokettch-publish-notice', 1 );
		load_plugin_textdomain( 'stop-the-bokettch', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		if ( ! get_option( 'blog_public' ) ) {
			add_action( 'init'             , array( $this, 'init' ) );
			$this->icon_url = plugin_dir_url( __FILE__ ) . 'images/' . __( 'default.png', 'stop-the-bokettch' );
			$this->genericons_dir_url = plugins_url( 'fonts/genericons/', __FILE__ );
		}
		if ( $this->publish_notice ) {
			add_action( 'load-post-new.php', array( $this, 'enqueue_js' ) );
			add_action( 'load-post.php'    , array( $this, 'enqueue_js' ) );
		}
		add_action( 'admin_init'           , array( $this, 'add_publish_notice_setting' ) );
	}
	
	
	public function init() {
		$capability = apply_filters( 'bokettch-notice-capability', 'publish_posts' );
		if ( current_user_can( $capability ) ) {
			add_action( 'admin_bar_menu'    , array( $this, 'bokettch_notice' ), 9999 );
			add_action( 'admin_print_styles', array( $this, 'bokettch_style' ) );
			add_action( 'wp_head'           , array( $this, 'bokettch_style' ) );

			if ( $this->is_admin_responsive() && ( is_admin() || get_user_option( 'show_admin_bar_front', get_current_user_id() ) === 'true' ) ) {
				wp_enqueue_style( 'genericons', $this->genericons_dir_url . 'genericons.css', array(), $this->genericons_version );
			}
		}
	}


	public function enqueue_js() {
		if ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) ) {
			$post_type = $_GET['post_type'];
		} else {
			$post_type = 'post';
		}
		$post_type_obj = get_post_type_object( $post_type );

		if ( $post_type_obj->public == true ) {
			$js_path = plugin_dir_url( __FILE__ ) . 'js/bokettch.js';
			wp_enqueue_script( 'bokettch_publish', $js_path , array(), $this->version, true );
			wp_enqueue_script( 'bokettch_publish', $js_path , array(), $this->version, true );
			wp_localize_script( 'bokettch_publish', 'bokettch', array(
				'confilm' => __( 'Are you sure to publish the post?', 'stop-the-bokettch' ),
			));
		}
	}


	public function bokettch_notice( $wp_admin_bar ) {
		$title = '<span class="ab-icon"></span><span class="ab-label">' . __( 'NO INDEX', 'stop-the-bokettch' ) . '</span>';
		if ( current_user_can( 'manage_options' ) ) {
			$link = admin_url( 'options-reading.php#blog_public' );
		} else {
			$link = false;
		}
		$wp_admin_bar->add_menu(array(
			'id'    => 'bokettch-notice',
			'meta'  => array(),
			'title' => apply_filters( 'bokettch-notice-title', $title ),
			'href'  => apply_filters( 'bokettch-notice-link', $link )
		));
 	}


	public function bokettch_style() {
		if ( ! is_admin() && ! is_user_logged_in() ) { return; }
		$icon_url = apply_filters( 'bokettch-icon-url', $this->icon_url );
?>
	<style type="text/css">
		#wpadminbar #wp-admin-bar-bokettch-notice {
			background: red;
		}
<?php if ( $this->is_admin_responsive() ) : ?>
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon:before {
			font-family: 'Genericons';
			content: '\f446';
			color: white;
			font-size: 1.2em;
			top: 1px;
		}
		@media screen and (max-width: 782px) {
			#wp-toolbar > ul > li#wp-admin-bar-bokettch-notice {
				display:inline;
			}
			#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon:before {
				font-size: 1em;
				top: 4px;
			}
		}
<?php else : ?>
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon {
			background-image: url( "<?php echo esc_url( $icon_url ); ?>" );
			width: 22px;
			height: 22px;
			margin-top: 3px;
		}
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-label{
			color: white;
			text-shadow: none;
		}
<?php endif; ?>
	</style>
<?php
	}
	
	
	private function is_admin_responsive() {
		return version_compare( '3.8.*', get_bloginfo( 'version' ), '<=' );
	}
	
	
	public function add_publish_notice_setting() {
		add_settings_field( 'publish_notice', __( 'Confirmation of publish post', 'stop-the-bokettch' ), array( $this, 'publish_notice_field' ), 'writing' );
		register_setting( 'writing', 'bokettch-publish-notice' );
	}
	
	
	public function publish_notice_field() {
?>
		<input type="hidden" name="bokettch-publish-notice" value="1">
		<label for="bokettch-publish-notice">
			<input type="checkbox" name="bokettch-publish-notice" id="bokettch-publish-notice" value="0"<?php echo $this->publish_notice == 0 ? ' checked="checked"' : ''; ?>>
			<?php _e( 'Disable', 'stop-the-bokettch' ); ?>
		</label>
<?php
	}
}
new Stop_the_Bokettch;