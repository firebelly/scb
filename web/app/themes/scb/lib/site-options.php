<?php

namespace Firebelly\SiteOptions;

/**
 * Site Options page in admin with CMB2 fields
 * @version 0.1.0
 */
class FbSiteOptions {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'fb_site_options_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'fb_site_options_option_metabox';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Site Options', 'firebelly' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name' => __( 'Twitter ID', 'firebelly' ),
			'id'   => 'twitter_id',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'Facebook ID', 'firebelly' ),
			'id'   => 'facebook_id',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'LinkedIn ID', 'firebelly' ),
			'id'   => 'linkedin_id',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'Contact Email', 'firebelly' ),
			'id'   => 'contact_email',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Cover Letter PDF', 'firebelly' ),
			'desc'    => __( 'This is attached to the beginning of every Collection PDF', 'firebelly' ),
			'id'      => 'cover_letter_pdf',
			'type'    => 'file',
		) );

	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'firebelly' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the FbSiteOptions object
 * @since  0.1.0
 * @return FbSiteOptions object
 */
function fb_site_options_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new FbSiteOptions();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function get_option( $key='', $default='' ) {
	$option = cmb2_get_option( fb_site_options_admin()->key, $key );
	return (empty($option)) ? $default : $option;
}

// Get it started
fb_site_options_admin();