<?php

/*

Plugin Name: Beckspaced Supersized
Plugin URI: http://beckspaced.com/
Description: Beckspaced easy implementation of Supersized Full Screen Background
Version: 0.2beta
Author: Becki Beckmann
Author URI: http://beckspaced.com
License: GPL2

Copyright 2000-2013  BECKI BECKMANN  (email : becki@beckspaced.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


*/

if ( ! defined( 'BS_SUPERSIZED_LOAD_JS' ) )
	define( 'BS_SUPERSIZED_LOAD_JS', true );

if ( ! defined( 'BS_SUPERSIZED_LOAD_CSS' ) )
	define( 'BS_SUPERSIZED_LOAD_CSS', true );

if ( ! defined( 'BS_SUPERSIZED_FOLDER' ) )
	define('BS_SUPERSIZED_FOLDER', basename( dirname(__FILE__) ) );
	// bs-supersized

if ( ! defined( 'BS_SUPERSIZED_PLUGIN_BASENAME' ) )
	define( 'BS_SUPERSIZED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	// bs-supersized/bs-supersized.php

if ( ! defined( 'BS_SUPERSIZED_PLUGIN_DIR' ) )
	define( 'BS_SUPERSIZED_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
	// /path/to/httpdocs/wp-content/plugins/bs-supersized

if ( ! defined( 'BS_SUPERSIZED_PLUGIN_URL' ) )
	define( 'BS_SUPERSIZED_PLUGIN_URL', untrailingslashit( plugins_url( BS_SUPERSIZED_FOLDER ) ) );
	// http://domain.com/wp-content/plugins/bs-supersized

if ( ! defined( 'BS_SUPERSIZED_PLUGIN_URL_IMAGES' ) )
	define( 'BS_SUPERSIZED_PLUGIN_URL_IMAGES', untrailingslashit( plugins_url('images', __FILE__ ) ) );
	// http://domain.com/wp-content/plugins/bs-supersized/images


if ( !class_exists('bsSupersized') ) {
	
	class bsSupersized {
		
	    public function __construct($options = false) {
			
			//register_activation_hook( __FILE__ , array( $this, 'activate' ) );
			//register_deactivation_hook( __FILE__ , array( $this, 'deactivate' ) );
			
			$this->loadDependencies();
			
			// Start this plugin once all other plugins are fully loaded
			add_action( 'plugins_loaded', array($this, 'startPlugin') );
		}
		
		public function activate() {
			
		}
		
		public function deactivate() {
			
		}
		
		public function startPlugin() {
			
			global $bsSupersizedCore;
			
			$this->loadTextdomain();
			
			// Check if we are in the admin area
			if ( is_admin() ) {
			
			}
			else
			{
				
				add_action( 'wp_head', array( $bsSupersizedCore, 'addSupersized' ) );
				
				// frontend
				if ( BS_SUPERSIZED_LOAD_CSS && $bsSupersizedCore->enqueue_scripts == true )
				    add_action( 'wp_enqueue_scripts', array( $this, 'loadStyles' ) );
				if ( BS_SUPERSIZED_LOAD_JS && $bsSupersizedCore->enqueue_scripts == true )
				    add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts' ) );
			}
		}
		
		public function loadStyles() {
			
			wp_register_style( 'bs-supersized-css', BS_SUPERSIZED_PLUGIN_URL . "/css/supersized.core.css", false, '0.1', 'screen' );
			wp_enqueue_style( 'bs-supersized-css' );
		}
		
		public function loadScripts() {
			
			wp_register_script('bs-supersized-js', BS_SUPERSIZED_PLUGIN_URL .'/js/supersized.core.3.2.1.min.js', array('jquery'));
			wp_enqueue_script('bs-supersized-js');
		}
		
		function loadTextdomain() {
		
			load_plugin_textdomain('bs-supersized', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
		}
		
		public function loadDependencies() {
			
			// loads default config vars
			require_once ( BS_SUPERSIZED_PLUGIN_DIR . '/config/config.php');
			
			// loads and inits config object
			require_once ( BS_SUPERSIZED_PLUGIN_DIR . '/lib/config.php');
			
			// loads and inits core object
			require_once ( BS_SUPERSIZED_PLUGIN_DIR . '/lib/core.php');
			
			if ( is_admin() ) {
			
				require_once ( BS_SUPERSIZED_PLUGIN_DIR . '/admin/admin.php');
				$bsSupersizedAdmin = new bsSupersizedAdmin( $bsSupersizedClassMembers );
			}
		}
	}
	
	global $bsSupersized;
	$bsSupersized = new bsSupersized();
}
?>