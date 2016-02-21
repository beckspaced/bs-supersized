<?php

if (!defined ('ABSPATH')) die ('No direct access allowed');

if ( !class_exists('bsSupersizedConfig') ) :

class bsSupersizedConfig {
	
    private $gallery_settings_key;
    private $directory_settings_key;
    private $medialibrary_settings_key;
    
    public $gallery_settings_default;
    public $directory_settings_default;
    public $medialibrary_settings_default;
    
    private $directory_settings;
    private $gallery_settings;
    private $medialibrary_settings;
    
	public function __construct( $options ) {
		
		// extracts config array and fills class members with values
		if ( is_array( $options ) ) {
			
			$valid = get_class_vars( get_class($this) );
			foreach ( $options as $k => $v ) {
				
				if ( array_key_exists($k,$valid) ) $this->$k = $v;
			}
		}
				
		self::load_settings();
	}
	
	public function load_settings() {
	    
	    $this->gallery_settings = (array) get_option( $this->gallery_settings_key, $this->gallery_settings_default );
	    $this->directory_settings = (array) get_option( $this->directory_settings_key, $this->directory_settings_default );
	    $this->medialibrary_settings = (array) get_option( $this->medialibrary_settings_key, $this->medialibrary_settings_default );
	}
	
	public function getGalleryID() {
		
		//return $this->gallery_id;
		return $this->gallery_settings['gallery_id'];
	}
	
	public function isGalleryEnabled() {
	    
	    return ($this->gallery_settings['gallery_enabled'] == "true") ? true : false;
	}
	
	public function getDirectoryPath() {
	    
	    return $this->directory_settings['directory_path'];
	}
	
	public function isDirectoryEnabled() {
	     
	    return ($this->directory_settings['directory_enabled'] == "true") ? true : false;
	}
	
	public function isMediaLibraryEnabled() {
	
	    return ($this->medialibrary_settings['medialibrary_enabled'] == "true") ? true : false;
	}
	
	public function getMediaLibraryPattern() {
	     
	    return $this->medialibrary_settings['medialibrary_pattern'];
	}
}

// let's start the config object
global $bsSupersizedConfig;
$bsSupersizedConfig = new bsSupersizedConfig($bsSupersizedClassMembers);

endif; // endif class exists