<?php

/**
 * http://theme.fm/2011/10/how-to-create-tabs-with-the-settings-api-in-wordpress-2590/
 */

if (!defined ('ABSPATH')) die ('No direct access allowed');

if ( !class_exists('bsSupersizedAdmin') ) :

class bsSupersizedAdmin {
	
	private $gallery_settings_key;
	private $directory_settings_key;
	private $medialibrary_settings_key;
	private $plugin_options_key;
	private $plugin_settings_tabs = array();
	
	public $gallery_settings_default;
	public $directory_settings_default;
	public $medialibrary_settings_default;
	
	function __construct( $options = false ) {
	    
	    // extracts config array and fills class members with values
	    if ( is_array($options) ) {
	        	
	        $valid = get_class_vars( get_class($this) );
	        foreach( $options as $k => $v ) {
	    
	            if ( array_key_exists( $k, $valid ) ) $this->$k = $v;
	        }
	    }
	
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_gallery_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_directory_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_medialibrary_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
	}
	
	public function load_settings() {
	    
	    $this->gallery_settings = (array) get_option( $this->gallery_settings_key, $this->gallery_settings_default );
	    $this->directory_settings = (array) get_option( $this->directory_settings_key, $this->directory_settings_default );
	    $this->medialibrary_settings = (array) get_option( $this->medialibrary_settings_key, $this->medialibrary_settings_default );
	    
	}
	
	function register_medialibrary_settings()
	{
	    $this->plugin_settings_tabs[$this->medialibrary_settings_key] = __( 'Media Library', 'bs-supersized' );
	    
	    register_setting(
	    $this->medialibrary_settings_key, //  A settings group name This must match the group name in settings_fields()
	    $this->medialibrary_settings_key, //  The name of an option to sanitize and save.
	    array( $this, 'sanitize_medialibrary_settings' ) // Sanitize
	    );
	    
	    add_settings_section(
	    'section_medialibrary', // id
	    __( 'Media Library Settings', 'bs-supersized' ), // title
	    array( $this, 'section_medialibrary_desc' ), //callback
	    $this->medialibrary_settings_key ) // page
	    ;
	     
	    add_settings_field(
	    'medialibrary_enabled', // ID
	    __( 'Media Library Enabled', 'bs-supersized' ), // title
	    array( &$this, 'field_medialibrary_enabled' ), //callback
	    $this->medialibrary_settings_key, // page
	    'section_medialibrary' // section
	    );
	     
	    add_settings_field(
	    'medialibrary_pattern', // ID
	    __( 'Media Library Pattern', 'bs-supersized' ), // title
	    array( &$this, 'field_medialibrary_pattern' ), //callback
	    $this->medialibrary_settings_key, // page
	    'section_medialibrary' // section
	    );
	}
	
	function section_medialibrary_desc()
	{
	    ?>
	    <p><?php _e( 'You can also use pictures from your media library as fullscreen background.', 'bs-supersized' ); ?></p>
	    <p><?php _e( 'Just make sure to the picture resolution is a minimum size of <strong>2048 pixel</strong>.', 'bs-supersized' ); ?></p>
	    <p><?php _e( 'The picture file name MUST matches the \'<strong>Media Library Pattern</strong>\' ->  \'<strong>supersized</strong>\'.', 'bs-supersized' ); ?></p>
	    <p><?php _e( 'Meaning the word \'<strong>supersized</strong>\' must be part of your picture file name. See the following examples:', 'bs-supersized' ); ?></p>
	    <ul style="margin-left: 25px; list-style: disc inside none ">
	    <li><?php _e( 'supersized-image-01.jpg', 'bs-supersized' ); ?></li>
	    <li><?php _e( 'image-supersized.jpg', 'bs-supersized' ); ?></li>
	    </ul>
	    <p><strong><?php _e( 'Please select your Media Library settings below:', 'bs-supersized' ); ?></strong></p>
	    <?php
	    
	}
	
	function field_medialibrary_enabled()
	{
	    ?>
			    <input type="radio" id="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled][true]" name="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled]" value="true" <?php checked( 'true', $this->medialibrary_settings['medialibrary_enabled'], true ) ?> />
			    <label for="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled][true]"><?php _e( 'Yes, enable media library', 'bs-supersized' ) ?></label>
			    <br />
			    <input type="radio" id="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled][false]" name="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled]" value="false" <?php checked( 'false', $this->medialibrary_settings['medialibrary_enabled'], true ) ?> />
			    <label for="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_enabled][false]"><?php _e( 'No, disable media library', 'bs-supersized' ) ?></label>
		<?php
	}
	
	function field_medialibrary_pattern() {
	    ?>
		    <input type="text" name="<?php echo $this->medialibrary_settings_key; ?>[medialibrary_pattern]" value="<?php echo esc_attr( $this->medialibrary_settings['medialibrary_pattern'] ); ?>" readonly />
		    <?php
		}
	
	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	
	public function sanitize_medialibrary_settings( $input )
	{
	    $output = get_option( $this->medialibrary_settings_key );
	    
	    if ( !empty( $input['medialibrary_pattern']) ) {
	    
	        if ( preg_match('/^[a-zA-Z]+$/', $input['medialibrary_pattern']) ) {
	    
	            $output['medialibrary_pattern'] = sanitize_text_field($input['medialibrary_pattern']);
	        }
	        else
	        {
	            add_settings_error( 'bs-supersized-settings', 'bs-supersized-error-medialibrary-pattern', __( 'Media library pattern can only be alphanumeric characters in range of [a-zA-Z].', 'bs-supersized' ) );
	        }
	    }
	    
	    if ( empty( $input['medialibrary_pattern'] ) ) add_settings_error( 'bs-supersized-settings', 'bs-supersized-error-medialibrary-pattern-empty', __( 'Please fill in a media library pattern string!', 'bs-supersized' ) );
	    
	    if ( !empty( $input['medialibrary_enabled']) ) {
	         
	        if ( preg_match('/^(false|true)$/', $input['medialibrary_enabled']) ) {
	             
	            $output['medialibrary_enabled'] = sanitize_text_field($input['medialibrary_enabled']);
	        }
	    }
	    
	    return $output;
	}
	
	function register_gallery_settings() {
	    
	    $this->plugin_settings_tabs[$this->gallery_settings_key] = __( 'Gallery', 'bs-supersized' );
	
	    register_setting(
	       $this->gallery_settings_key, //  A settings group name This must match the group name in settings_fields()
	       $this->gallery_settings_key, //  The name of an option to sanitize and save. 
	       array( $this, 'sanitize_gallery_settings' ) // Sanitize
	    );
	    
	    add_settings_section(
	       'section_gallery', // id
	       __( 'NGG Gallery Settings', 'bs-supersized' ), // title
	       array( &$this, 'section_gallery_desc' ), //callback
	       $this->gallery_settings_key ) // page
	    ;
	    
	    add_settings_field(
	       'gallery_enabled', // ID
	       __( 'NGG Gallery Enabled', 'bs-supersized' ), // title
	       array( &$this, 'field_gallery_enabled' ), //callback
	       $this->gallery_settings_key, // page
	       'section_gallery' // section
	    );
	    
	    add_settings_field(
	       'gallery_id', // ID
	       __( 'NGG Gallery ID', 'bs-supersized' ), // title
	       array( &$this, 'field_gallery_id' ), //callback
	       $this->gallery_settings_key, // page
	       'section_gallery' // section
	    );
	    
	    

	}
	
	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	
	public function sanitize_gallery_settings( $input ) {
	
	    $output = get_option( $this->gallery_settings_key );
	
	    if ( !empty( $input['gallery_id']) ) {
	
	        if ( preg_match('/^[0-9]+$/', $input['gallery_id']) ) {
	
	            $output['gallery_id'] = sanitize_text_field($input['gallery_id']);
	        }
	        else
	        {
	            add_settings_error( 'bs-supersized-settings', 'bs-supersized-error-gallery-id', __( 'NGG Gallery ID can only be digit / number in range of [0-9].', 'bs-supersized' ) );
	        }
	    }
	
	    if ( empty( $input['gallery_id'] ) ) add_settings_error( 'bs-supersized-settings', 'bs-supersized-error-gallery-id-empty', __( 'Please fill in a NGG Gallery ID!', 'bs-supersized' ) );
	    
	    if ( !empty( $input['gallery_enabled']) ) {
	        
	        if ( preg_match('/^(false|true)$/', $input['gallery_enabled']) ) {
	        
	            $output['gallery_enabled'] = sanitize_text_field($input['gallery_enabled']);
	        }
	    }
	
	    return $output;
	}
	
	function section_gallery_desc() {

	    _e( 'Enter NGG gallery settings below:', 'bs-supersized' );
	}
	
	function field_gallery_id() {
	    ?>
	    <input type="text" name="<?php echo $this->gallery_settings_key; ?>[gallery_id]" value="<?php echo esc_attr( $this->gallery_settings['gallery_id'] ); ?>" />
	    <?php
	}
	

	function field_gallery_enabled() {
	    ?>
		    <input type="radio" id="<?php echo $this->gallery_settings_key; ?>[gallery_enabled][true]" name="<?php echo $this->gallery_settings_key; ?>[gallery_enabled]" value="true" <?php checked( 'true', $this->gallery_settings['gallery_enabled'], true ) ?> />
		    <label for="<?php echo $this->gallery_settings_key; ?>[gallery_enabled][true]"><?php _e( 'Yes, enable gallery', 'bs-supersized' ) ?></label>
		    <br />
		    <input type="radio" id="<?php echo $this->gallery_settings_key; ?>[gallery_enabled][false]" name="<?php echo $this->gallery_settings_key; ?>[gallery_enabled]" value="false" <?php checked( 'false', $this->gallery_settings['gallery_enabled'], true ) ?> />
		    <label for="<?php echo $this->gallery_settings_key; ?>[gallery_enabled][false]"><?php _e( 'No, disable gallery', 'bs-supersized' ) ?></label>
		<?php
	}
		
	function register_directory_settings() {
	    
	    $this->plugin_settings_tabs[$this->directory_settings_key] = __( 'Directory', 'bs-supersized' );
	
	    register_setting(
	       $this->directory_settings_key,
	       $this->directory_settings_key,
	       array( $this, 'sanitize_directory_settings' ) // Sanitize
	    );
	    
	    add_settings_section(
	       'section_directory',
	       __( 'Directory Settings', 'bs-supersized' ),
	       array( &$this, 'section_directory_desc' ),
	       $this->directory_settings_key )
	    ;
	    
	    add_settings_field(
	       'directory_enabled', // ID
	       __( 'Directory Enabled', 'bs-supersized' ), // title
	       array( &$this, 'field_directory_enabled' ), //callback
	       $this->directory_settings_key, // page
	       'section_directory' // section
	    );
	    
	    add_settings_field(
	       'directory_path',
	        __( 'Directory Path', 'bs-supersized' ),
	       array( &$this, 'field_directory_option' ),
	       $this->directory_settings_key,
	       'section_directory'
	    );
	}
	
	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	
	public function sanitize_directory_settings( $input ) {
	
	    $output = get_option( $this->directory_settings_key );

	    if ( !empty( $input['directory_path']) ) {
	
	        //$output['directory_path'] = sanitize_file_name($input['directory_path']);
	        $output['directory_path'] = $input['directory_path'];
	    }
	
	    if ( empty( $input['directory_path'] ) ) add_settings_error( 'bs-supersized-settings', 'bs-supersized-error-directory-path-empty', __( 'Please fill in a directory path!', 'bs-supersized' ) );
	
	    if ( !empty( $input['directory_enabled']) ) {
	         
	        if ( preg_match('/^(false|true)$/', $input['directory_enabled']) ) {
	             
	            $output['directory_enabled'] = sanitize_text_field($input['directory_enabled']);
	        }
	    }
	    
	    return $output;
	}
	
	function section_directory_desc() {

	    _e( 'Enter Directory Path below:', 'bs-supersized' );
	}
	
	function field_directory_option() {
	    
	    /**
	    printf(
	    '<input type="text" id="gallery_id" name="bs_supersized_option[gallery_id]" value="%s" />',
	    esc_attr( $this->options['gallery_id'])
	    );
	    **/
	    
	    ?>
	    <input type="text" size="75" name="<?php echo $this->directory_settings_key; ?>[directory_path]" value="<?php echo esc_attr( $this->directory_settings['directory_path'] ); ?>" />
	    <?php
	}
	
	function field_directory_enabled() {
	    ?>
	    <input type="radio" id="<?php echo $this->directory_settings_key; ?>[directory_enabled][true]" name="<?php echo $this->directory_settings_key; ?>[directory_enabled]" value="true" <?php checked( 'true', $this->directory_settings['directory_enabled'], true ) ?> />
	    <label for="<?php echo $this->directory_settings_key; ?>[directory_enabled][true]"><?php _e( 'Yes, enable directory', 'bs-supersized' ) ?></label>
	    <br />
	    <input type="radio" id="<?php echo $this->directory_settings_key; ?>[directory_enabled][false]" name="<?php echo $this->directory_settings_key; ?>[directory_enabled]" value="false" <?php checked( 'false', $this->directory_settings['directory_enabled'], true ) ?> />
	    <label for="<?php echo $this->directory_settings_key; ?>[directory_enabled][false]"><?php _e( 'No, disable directory', 'bs-supersized' ) ?></label>
		<?php
		}
	
	function add_admin_menus() {
	    
	    // create options page hook, so we can add admin style to only that page
	    $options_page_hook = add_options_page( __( 'Supersized Settings', 'bs-supersized' ), __( 'Supersized Settings', 'bs-supersized' ), 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	    
	    add_action( 'admin_print_styles-' . $options_page_hook, array( $this, 'admin_print_styles' ) );
	}
	
	function admin_print_styles() {
	    
	    wp_register_style( 'bs-supersized-admin-css', BS_SUPERSIZED_PLUGIN_URL . "/admin/css/style.css", false, '0.1', 'screen' );
	    wp_enqueue_style( 'bs-supersized-admin-css' );
	}
	
	function plugin_options_page() {
	    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->gallery_settings_key;
	    ?>
	    <div class="wrap">
	        <?php $this->plugin_options_tabs(); ?>
	        <form method="post" action="options.php">
	            <?php wp_nonce_field( 'update-options' ); ?>
	            <?php settings_fields( $tab ); ?>
	            <?php do_settings_sections( $tab ); ?>
	            <?php submit_button(); ?>
	        </form>
	    </div>
	    <?php
	}
	
	function plugin_options_tabs() {
	    
	    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->gallery_settings_key;
	
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
	        $active = ($current_tab == $tab_key) ? 'nav-tab-active' : '';
	        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
	    }
	    echo '</h2>';
	}
	
}

endif; // endif class exists

?>