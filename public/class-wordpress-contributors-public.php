<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/bhavyeshdhaduk
 * @since      1.0.0
 *
 * @package    Wordpress_Contributors
 * @subpackage Wordpress_Contributors/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wordpress_Contributors
 * @subpackage Wordpress_Contributors/public
 * @author     bhavyesh dhaduk <bhavyesh1990@gmail.com>
 */
class WordPress_Contributors_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name   The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version   The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name  The name of this plugin.
	 * @param    string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Initialize custom style and script.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Add Contributor to post.
		add_filter( 'the_content', array( $this, 'show_post_contributors' ), 10 );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordpress-contributors-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Display Contributor Box
	 *
	 * @param string $content Content of the post.
	 */
	public function show_post_contributors( $content ) {

		global $post;
		$cont_html = '';

		if ( is_singular( 'post' ) ) {
			$post_contributors_list = get_post_meta( $post->ID, 'rtcamp_post_contributors_list', true );

			if ( ! empty( $post_contributors_list ) ) {
				$cont_html .= '<div class="rt-contributors">';
				$cont_html .= '<h4>' . __( 'Contributors' ) . '</h4>';

				foreach ( $post_contributors_list as $contributor ) {
					$user_obj   = get_user_by( 'id', $contributor );
					$cont_html .= '<div class="cb-box">';
					$cont_html .= get_avatar( $user_obj->ID, 75 );
					$cont_html .= '<div class="rt-contributor"><a alt="' . esc_html( $user_obj->display_name ) . '" href="' . get_author_posts_url( $user_obj->ID ) . '">' . esc_html( $user_obj->display_name ) . '</a></div>';
					$cont_html .= '</div>';
				}

				$cont_html .= '</div>';
			}
		}

		return $content . $cont_html;

	}

}
