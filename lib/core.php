<?php

if (!defined ('ABSPATH')) die ('No direct access allowed');

if ( !class_exists('bsSupersizedCore') ) :

class bsSupersizedCore {
	
	const plugin_name = 'Beckspaced Supersized';
	const plugin_version = '0.1beta';
	const supersized_core_version = '3.2.1';
	
	public $images_markup = false;
	public $enqueue_scripts = false;
	
	public function __construct($options = false) {
		
		/**
		 * this here actually does nothing in this script
		 * but actually can be used to fill class members with values
		 * when constructing the object
		 */
		
		// extracts config array and fills class members with values
		if ( is_array($options) ) {
			
			$valid = get_class_vars( get_class($this) );
			foreach( $options as $k => $v ) {
				
				if ( array_key_exists( $k, $valid ) ) $this->$k = $v;
			}
		}
		
		$this->init();

	}
	
	public function init() {
	    
	    global $bsSupersizedConfig;
	    
	    if ( $bsSupersizedConfig->isGalleryEnabled() ) {
	    
            if ( $images = self::getImagesNggGallery( $bsSupersizedConfig->getGalleryID() ) ) {
	             
	            $this->enqueue_scripts = true;
	            $this->images_markup = $images;
	            
	        }

	    }
	    elseif ( $bsSupersizedConfig->isDirectoryEnabled() ) {
	    
	        if ( $images = self::getImagesFromDirectory($bsSupersizedConfig->getDirectoryPath()) ) {
	    
	            $this->enqueue_scripts = true;
	            $this->images_markup = $images;
	        }

	    }
	    elseif ( $bsSupersizedConfig->isMediaLibraryEnabled() )
	    {
	        if ( $images = self::get_images_from_media_library( $bsSupersizedConfig->getMediaLibraryPattern() ) ) {
	             
	            $this->enqueue_scripts = true;
	            $this->images_markup = $images;
	        }
	    }
	}
	
	public function addSupersized() {
		
		global $bsSupersizedConfig;
		
        if ( $bsSupersizedConfig->isGalleryEnabled() ) {
            
            if ( $this->images_markup ) {
                	
                self::addHeaderCode($this->images_markup);
            }
            else
            {
                self::addHeaderNotice(array('type'=>'gallery','gallery_id'=> $bsSupersizedConfig->getGalleryID()));
            }
        }
        elseif ( $bsSupersizedConfig->isDirectoryEnabled() ) {
            
            if ( $this->images_markup ) {
                
                self::addHeaderCode($this->images_markup);
            }
            else 
            {
                self::addHeaderNotice(array('type'=>'directory','directory_path'=> $this->getPathURL($bsSupersizedConfig->getDirectoryPath()) ) );
            }

        }
        elseif ( $bsSupersizedConfig->isMediaLibraryEnabled() ) {
            
            if ( $this->images_markup ) {
            
                self::addHeaderCode($this->images_markup);
            }
            else
            {
                self::addHeaderNotice(array('type'=>'medialibrary','medialibrary_pattern'=> $bsSupersizedConfig->getMediaLibraryPattern() ) );
            }
        }

	}
	
	public function addHeaderNotice($args) {

	    ?>
	    <!--
		Supersized - Fullscreen Slideshow jQuery Plugin
		Adapted for Wordpress <?php echo self::plugin_name . " " . self::plugin_version; ?> by Becki Beckmann Web Design Wiesentheid (http://beckspaced.com/)
		
		<?php 
		
	    switch ($args['type']) {
	        
	        case "gallery":
	            
	           printf( __( 'Error - No pictures found in NGG gallery with ID %d please select a different gallery!', 'bs-supersized' ), $args['gallery_id'] );
	           echo "\n";
	           break;
	        
	        case "directory":
	            
	            printf( __( 'Error - No pictures found in directory with path %s please check your directory path!', 'bs-supersized' ), $args['directory_path'] );
	            echo "\n";
	            break;
	            
            case "medialibrary":
                 
                printf( __( 'Error - No pictures found in media library with pattern %s please check your media library picture upload!', 'bs-supersized' ), $args['medialibrary_pattern'] );
                echo "\n";
                break;
	        
	    }
        ?>
	    <?php _e( 'wp_enqueue_scripts for css and js has been skipped!', 'bs-supersized' ); echo "\n"; ?>
		-->
	    <?php 
	}
	
	public function addHeaderCode( $images ) {
		
		?>
		<!--
			Supersized - Fullscreen Slideshow jQuery Plugin
			Version : Core <?php echo self::supersized_core_version . "\n"; ?>
			Site	: www.buildinternet.com/project/supersized
			
			Author	: Sam Dunn
			Company : One Mighty Roar (www.onemightyroar.com)
			License : MIT License / GPL License
			
			Adapted for Wordpress <?php echo self::plugin_name . " " . self::plugin_version; ?> by Becki Beckmann Web Design Wiesentheid (http://beckspaced.com/)
		-->
		
		<script type="text/javascript"> 
		jQuery(function($){

			$.supersized({

				// Functionality
				start_slide             :   0,			// Start slide (0 is random)
				new_window				:	1,			// Image links open in new window/tab
				image_protect			:	1,			// Disables image dragging and right click with Javascript
														   
				// Size & Position						   
				min_width		        :   0,			// Min width allowed (in pixels)
				min_height		        :   0,			// Min height allowed (in pixels)
				vertical_center         :   1,			// Vertically center background
				horizontal_center       :   1,			// Horizontally center background
				fit_always				:	0,			// Image will never exceed browser width or height (Ignores min. dimensions)
				fit_portrait         	:   0,			// Portrait images will not exceed browser height
				fit_landscape			:   0,			// Landscape images will not exceed browser width

				// Components
				slides 					:  	[			// Slideshow Images
											<?php echo $images; ?>
				       						]

			});
		});
		</script>
		<?php
	}
	
	public function getImagesNggGallery($nggGalleryID) {
	    
	    global $wpdb;
	     
	    $wpdb->nggpictures					= $wpdb->prefix . 'ngg_pictures';
	    $wpdb->nggallery					= $wpdb->prefix . 'ngg_gallery';
	    $wpdb->nggalbum						= $wpdb->prefix . 'ngg_album';
	     
	    $where = ' ';
	    $where = " WHERE gid IN (" . $nggGalleryID . ")";
	    $order = 'gid ASC';
	     
	    // lets check if we find some pics in the gallery
	     
	    if ( $wpdb->get_var("SELECT COUNT(pid) FROM {$wpdb->nggpictures} WHERE galleryid = '" . $nggGalleryID . "'") > 0 ) {
	         
	        // yes, pics are available
	         
	        // lets get the gallery info so we have the path for image url
	        $where = ' ';
	        $where = " WHERE gid IN (" . $nggGalleryID . ")";
	        $order = 'gid ASC';
	         
	        $gallery = $wpdb->get_row("SELECT * FROM {$wpdb->nggallery}" . $where . " ORDER BY " . $order . " LIMIT 0, 1 ");
	         
	        // let's get a random picture
	        $pid = $wpdb->get_var("SELECT pid FROM {$wpdb->nggpictures} WHERE galleryid = '" . $nggGalleryID . "' ORDER BY RAND() LIMIT 1");
	    
	        // lets get the image data
	        $imagerow = $wpdb->get_row("SELECT * FROM {$wpdb->nggpictures} WHERE pid = '" . $pid . "'");
	        // lets combine the image and gallery data
	        foreach ($gallery as $key => $value) {
	            $imagerow->$key = $value;
	        }
	    
	        // build image url
	         
	        $image_url = site_url("/") . $imagerow->path . "/" . $imagerow->filename;
	         
	        // build our image string
	        $full_output = $full_output . "\n{image : '" . $image_url . "', title : '".$imagerow->description."'}";
	        return $full_output;
	    
	         
	    }
	    else
	    {
	        return false;
	    }
	}
	
	public function getImagesFromDirectory( $directory_path ) {
	    
	    $images = glob( $directory_path . "{*.gif,*.jpg,*.png}", GLOB_BRACE);
	    
	    if ( !empty($images) ) {
	        
	        $output = "";
	         
	        foreach ($images as $i) {
	             
	            //$pos = strpos($i, "/wp-content/");
	            //$image = get_bloginfo('url') . substr($i, $pos);
	            
	            $image = $this->getPathURL($i);
	            
	            $image_title = "";
	             
	            $output .= "\n{image : '" . $image . "', title : '".$image_title."'},";
	             
	        }
	         
	        $output = substr( $output, 0, -1 ) . "\n"; // removes the trailing comma to avoid trouble in IE
	        return $output;
	    }
	    else 
	    {
	        return false;
	    }
	    
	}
	
	public function getPathURL($path) {
	    
	    $pos = strpos($path, "/wp-content/");
	    $pathURL = get_bloginfo('url') . substr($path, $pos);
	    return $pathURL;
	}
	
	public function get_images_from_media_library( $pattern )
	{
	    $regex_pattern = "/" . $pattern . "/i";
	    
	    $args = array(
	        'post_type' => 'attachment',
	        'post_mime_type' =>'image',
	        'post_status' => 'inherit',
	        'posts_per_page' => -1,
	        'orderby' => 'rand'
	    );
	    $query_images = new WP_Query( $args );
	    $images = array();
	    foreach ( $query_images->posts as $image) {
	
	        //if (preg_match("/random-employee/i", $image->guid)) {
	        if (preg_match( $regex_pattern, $image->guid)) {
	            	
	            $images[]= $image;
	        }
	    }
	    
	    wp_reset_query();
	
	    //define( 'DEBUG_LOG' , BS_SUPERSIZED_PLUGIN_DIR . "/debug-log.txt" );
	    //$var_str = var_export($images, true);
	    //file_put_contents(DEBUG_LOG, $var_str, FILE_APPEND | LOCK_EX);
	    
	    if ( !empty($images) )
	    {
	        $output = "";
	        $image_title = "";
	        
	        foreach ($images as $i) {
	        
                $output .= "\n{image : '" . $i->guid . "', title : '".$image_title."'},";
	        
	        }
	        
	        $output = substr( $output, 0, -1 ) . "\n"; // removes the trailing comma to avoid trouble in IE
	        return $output;
	    }
	    else 
	    {
	        return false;
	    }

	}
}

// let's start the core object
global $bsSupersizedCore;
$bsSupersizedCore = new bsSupersizedCore();

endif; // endif class exists