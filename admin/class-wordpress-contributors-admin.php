<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/bhavyeshdhaduk
 * @since      1.0.0
 *
 * @package    Wordpress_Contributors
 * @subpackage Wordpress_Contributors/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Contributors
 * @subpackage Wordpress_Contributors/admin
 * @author     bhavyesh dhaduk <bhavyesh1990@gmail.com>
 */
class WordPress_Contributors_Admin {

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

		// Initialize admin custom style and script.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Add Contributor metabox.
		add_action( 'add_meta_boxes', array( $this, 'add_contributors_metaboxes' ) );
		add_action( 'save_post', array( $this, 'contributors_metabox_save_postdata' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordpress-contributors-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Add contributors meta box.
	 */
	public function add_contributors_metaboxes() {
		add_meta_box( 'contributors-metabox', 'Contributors', array( $this, 'render_contributors_metaox' ), 'post', 'normal', 'high' );
	}

	/**
	 *
	 * Render html for contributors metabox.
	 *
	 * @param post $post The post object.
	 * @param box  $box  The box object.
	 */
	public function render_contributors_metaox( $post, $box ) {
		$post_contributors_list = get_post_meta( $post->ID, 'rtcamp_post_contributors_list', true );
		$post_author_id         = $post->post_author;

		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'rtcamp_contributors_box_nonce', 'contributors_box_nonce' );

		$blog_users = get_users();
		foreach ( $blog_users as $blog_user ) {
			if ( ! $blog_user->has_cap( 'edit_posts' ) ) {
				continue;
			}
			?>
			<label for="contributor_checkbox_<?php echo esc_html( $blog_user->ID ); ?>" class="contributor-chek-lbl"> 
				<input type="checkbox" class="contributor-checkbox" id="contributor_checkbox_<?php echo esc_html( $blog_user->ID ); ?>" name="contributors_checkbox[]" value="<?php echo esc_html( $blog_user->ID ); ?>" 
				<?php
				if ( ! empty( $post_contributors_list ) ) {
					if ( in_array( "$blog_user->ID", $post_contributors_list, true ) ) {
						echo "checked='checked'";
					}
				}

				if ( (string) $post_author_id === (string) $blog_user->ID ) {
					echo "checked='checked'";
					echo "disabled='disabled'";
				}
				?>
				/>
				<?php echo esc_html( $blog_user->first_name . ' ' . $blog_user->last_name . ' (' . $blog_user->user_login . ') ' ); ?>
			</label>
			<?php
		}
	}

	/**
	 * Save contributors metabox data.
	 *
	 * @param int $post_id The Current post id.
	 */
	public function contributors_metabox_save_postdata( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! isset( $_POST['contributors_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['contributors_box_nonce'] ) ), 'rtcamp_contributors_box_nonce' ) ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post' ) ) {
			return $post_id;
		}

		if ( isset( $_POST['contributors_checkbox'] ) && ! empty( $_POST['contributors_checkbox'] ) ) {
			$contributors_checkbox = array_map( 'sanitize_text_field', wp_unslash( $_POST['contributors_checkbox'] ) );
		}

		update_post_meta( $post_id, 'rtcamp_post_contributors_list', $contributors_checkbox );
	}

}
