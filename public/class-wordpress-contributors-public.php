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

		// Modify author post list based on contributor.
		add_filter( 'posts_where', array( $this, 'author_modify_post_query' ), 10, 2 );

		// Add Title based on author.
		add_filter( 'get_the_archive_title', array( $this, 'set_the_archive_title' ), 10 );

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
			$post_contributors_list   = get_post_meta( $post->ID, 'rtcamp_post_contributors_list', true );
			$post_contributors_list[] = $post->post_author;

			if ( ! empty( $post_contributors_list ) ) {
				$cont_html .= '<div class="rt-contributors">';
				$cont_html .= '<h4>' . __( 'Contributors' ) . '</h4>';

				foreach ( $post_contributors_list as $contributor ) {
					$user_obj = get_user_by( 'id', $contributor );
					if ( $user_obj ) {
						if ( $user_obj->first_name || $user_obj->last_name ) {
							$username = $user_obj->first_name . ' ' . $user_obj->last_name;
						} else {
							$username = $user_obj->display_name;
						}

						$cont_html .= '<div class="cb-box">';
						$cont_html .= get_avatar( $user_obj->ID, 75 );
						$cont_html .= '<div class="rt-contributor"><a alt="' . esc_html( $username ) . '" href="' . get_author_posts_url( $user_obj->ID ) . '">' . esc_html( $username ) . '</a></div>';
						$cont_html .= '</div>';
					}
				}

				$cont_html .= '</div>';
			}
		}
		return $content . $cont_html;
	}

	/**
	 * Display Author's Contribution posts
	 *
	 * @param string $where The where clause of the query.
	 * @param object $q main query of author page.
	 */
	public function author_modify_post_query( $where, WP_Query $q ) {

		global $wpdb;
		if ( ! is_admin() && $q->is_main_query() && $q->is_author() ) {
			$author_name = $q->query_vars['author_name'];
			$author      = get_user_by( 'slug', $author_name );
			if ( isset( $author->ID ) ) {
				$author_id = $author->ID;
				$args      = array(
					'post_status' => 'publish',
					'fields'      => 'ids',
					'meta_query'  => array(
						array(
							'key'     => 'rtcamp_post_contributors_list',
							'value'   => serialize( strval( $author_id ) ),
							'compare' => 'LIKE',
						),
					),
				);
				$post_ids  = get_posts( $args );
				if ( ! empty( $post_ids ) ) {
					$post_ids_str = implode( ', ', $post_ids );
					$where       .= " OR ( $wpdb->posts.ID IN ($post_ids_str)  ) ";
				}
			}
		}
		return $where;
	}

	/**
	 * Modify Author's Title
	 *
	 * @param string $title archive title.
	 */
	public function set_the_archive_title( $title ) {

		if ( is_author() ) {
			$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			$title   = '<h1 class="page-title">Author: <span class="vcard">' . $curauth->display_name . '</span></h1>';
		}
		return $title;
	}

}
