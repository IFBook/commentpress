<?php /*
===============================================================
Class CommentPressMultisiteAdmin Version 1.0
===============================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
---------------------------------------------------------------
NOTES
=====

This class is a wrapper for the majority of database operations.

---------------------------------------------------------------
*/






/*
===============================================================
Class Name
===============================================================
*/

class CommentPressMultisiteAdmin {






	/*
	===============================================================
	Properties
	===============================================================
	*/
	
	// parent object reference
	var $parent_obj;
	
	// options
	var $cpmu_options = array();
	
	// default title page content
	var $cpmu_title_page_content = '';
	






	/** 
	 * @description: initialises this object
	 * @param object $parent_obj a reference to the parent object
	 * @return object
	 * @todo: 
	 *
	 */
	function __construct( $parent_obj = null ) {
	
		// store reference to "parent" (calling obj, not OOP parent)
		$this->parent_obj = $parent_obj;
	
		// store reference to database wrapper (child of calling obj)
		$this->db = $this->parent_obj->db;
	
		// init
		$this->_init();

		// --<
		return $this;

	}
	
	
	



	/**
	 * PHP 4 constructor
	 */
	function CommentPressMultisiteAdmin( $parent_obj = null ) {
		
		// is this php5?
		if ( version_compare( PHP_VERSION, "5.0.0", "<" ) ) {
		
			// call php5 constructor
			$this->__construct( $parent_obj );
			
		}
		
		// --<
		return $this;

	}






	/** 
	 * @description: set up all items associated with this object
	 * @param integer $blog_id the ID of the blog - default null
	 * @todo: 
	 *
	 */
	function initialise( $blog_id = null ) {
		
		// test that we aren't reactivating
		if ( !$this->option_wp_get( 'cpmu_version' ) ) {
		
			// add options with default values
			$this->options_create();
			
		}
		
	}







	/** 
	 * @description: upgrade Commentpress plugin from 3.1 options to latest set
	 * @return boolean $result
	 * @todo: 
	 *
	 */
	function upgrade() {
		
		// init return
		$result = false;



		// if we have a commentpress install (or we're forcing)
		if ( $this->check_upgrade() ) {
		

			
			/*
			--------------------------------------------------------------------
			Example of how upgrades work...
			--------------------------------------------------------------------
			
			// database object
			global $wpdb;
			
			// default blog type
			$cp_blog_type = $this->blog_type;
			
			// get variables
			extract( $_POST );
			
			// New in CP3.3.1 - are we missing the cp_blog_type option?
			if ( !$this->option_exists( 'cp_blog_type' ) ) {
			
				// get choice
				$_choice = $wpdb->escape( $cp_blog_type );
			
				// add chosen cp_comment_editor option
				$this->option_set( 'cp_blog_type', $_choice );
				
			}
			*/
			


			// save new options
			$this->options_save();
			


			// store new version
			$this->option_wp_set( 'cpmu_version', CPMU_PLUGIN_VERSION );
			
		}
		
		

		// --<
		return $result;
	}
	
	
	
	
	


	/** 
	 * @description: if needed, destroys all items associated with this object
	 * @todo: 
	 *
	 */
	function destroy() {
	
		// delete options
		$this->options_delete();
		
	}







	/** 
	 * @description: uninstalls database modifications
	 * @todo: 
	 *
	 */
	function uninstall() {
	
		// nothing
		
	}







//#################################################################







	/*
	===============================================================
	PUBLIC METHODS
	===============================================================
	*/
	




	/** 
	 * @description: check for plugin upgrade
	 * @return boolean $result
	 * @todo: 
	 *
	 */
	function check_upgrade() {
	
		// init
		$result = false;
		
		// get installed version
		$_version = $this->option_wp_get( 'cpmu_version' );
		
		// if we have an install and it's lower than this one
		if ( $_version !== false AND version_compare( CPMU_PLUGIN_VERSION, $_version, '>' ) ) {
		
			// override
			$result = true;

		}
		


		// --<
		return $result;
	}
	
	
	
	
	


	/** 
	 * @description: create all basic Commentpress options
	 * @todo: store plugin options in a single array
	 *
	 */
	function options_create() {
	
		// init options array --> TO DO
		$this->cpmu_options = array(
		
			'cpmu_title_page_content' => $this->cpmu_title_page_content
		
		);

		// Paragraph-level comments enabled by default
		add_option( 'cpmu_options', $this->cpmu_options );
		
		// store Commentpress version
		add_option( 'cpmu_version', CPMU_PLUGIN_VERSION );
		
	}
	
	
	
	
	


	/** 
	 * @description: delete all basic Commentpress options
	 * @todo: 
	 *
	 */
	function options_delete() {
		
		// delete Commentpress version
		delete_option( 'cpmu_version' );
		
		// delete Commentpress options
		delete_option( 'cpmu_options' );
		
	}
	
	
	
	
	


	/** 
	 * @description: save the settings set by the administrator
	 * @return boolean success or failure
	 * @todo: do more error checking?
	 *
	 */
	function options_update() {
	
		// database object
		global $wpdb;
		
		
	
		// init result
		$result = false;
		


	 	// was the form submitted?
		if( isset( $_POST['cpmu_submit'] ) ) {
			


			// check that we trust the source of the data
			check_admin_referer( 'cpmu_admin_action', 'cpmu_nonce' );
		
			
			
			// init vars
			$cp_upgrade = '0';
			$cp_reset = '0';
			$cpmu_title_page_content = '';
			
			

			// get variables
			extract( $_POST );
			
			
			
			// did we ask to upgrade Commentpress?
			if ( $cp_upgrade == '1' ) {
			
				// do upgrade
				$this->upgrade();
				
				// --<
				return true;
			
			}
			
			
			
			// did we ask to reset?
			if ( $cp_reset == '1' ) {
			
				// reset theme options
				$this->options_reset();
				
				// --<
				return true;
			
			}


			
			// Commentpress Multisite params 
			
			/*
			// default title page content
			$cpmu_title_page_content = $wpdb->escape( $cpmu_title_page_content );
			$this->option_set( 'cpmu_title_page_content', $cpmu_title_page_content );
			*/
			
			
			
			// save
			$this->options_save();
			
			

			// set flag
			$result = true;
	
		}
		
		
		
		// --<
		return $result;
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: upgrade Commentpress options to array
	 * @todo: 
	 *
	 */
	function options_save() {
		
		// set option
		return $this->option_wp_set( 'cpmu_options', $this->cpmu_options );
		
	}
	
	
	
	
	


	/** 
	 * @description: reset Commentpress theme options
	 * @todo: 
	 *
	 */
	function options_reset() {
		
		// default title page content
		$this->option_set( 'cpmu_title_page_content', $this->cpmu_title_page_content );

		// store it
		$this->options_save();
		
	}
	
	
	
	
	


	/** 
	 * @description: return a value for a specified option
	 * @todo: 
	 */
	function option_exists( $option_name = '' ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_exists()', 'commentpress-plugin' ) );
		
		}
	
		// get option with unlikey default
		return array_key_exists( $option_name, $this->cpmu_options );
		
	}
	
	
	
	
	
	
	/** 
	 * @description: return a value for a specified option
	 * @todo: 
	 */
	function option_get( $option_name = '', $default = false ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_get()', 'commentpress-plugin' ) );
		
		}
	
		// get option
		return ( array_key_exists( $option_name, $this->cpmu_options ) ) ? $this->cpmu_options[ $option_name ] : $default;
		
	}
	
	
	
	
	
	
	/** 
	 * @description: sets a value for a specified option
	 * @todo: 
	 */
	function option_set( $option_name = '', $value = '' ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_set()', 'commentpress-plugin' ) );
		
		}
	
		// test for other than string
		if ( !is_string( $option_name ) ) {
		
			// oops
			die( __( 'You must supply the option as a string to option_set()', 'commentpress-plugin' ) );
		
		}
	
		// set option
		$this->cpmu_options[ $option_name ] = $value;
		
	}
	
	
	
	
	
	
	/** 
	 * @description: deletes a specified option
	 * @todo: 
	 */
	function option_delete( $option_name = '' ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_delete()', 'commentpress-plugin' ) );
		
		}
	
		// unset option
		unset( $this->cpmu_options[ $option_name ] );
		
	}
	
	
	
	
	
	
	/** 
	 * @description: return a value for a specified option
	 * @todo: 
	 */
	function option_wp_exists( $option_name = '' ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_wp_exists()', 'commentpress-plugin' ) );
		
		}
	
		// get option with unlikey default
		if ( $this->option_wp_get( $option_name, 'fenfgehgejgrkj' ) == 'fenfgehgejgrkj' ) {
		
			// no
			return false;
		
		} else {
		
			// yes
			return true;
		
		}
		
	}
	
	
	
	
	
	
	/** 
	 * @description: return a value for a specified option
	 * @todo: 
	 */
	function option_wp_get( $option_name = '', $default = false ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_wp_get()', 'commentpress-plugin' ) );
		
		}
	
		// get option
		return get_site_option( $option_name, $default );
		
	}
	
	
	
	
	
	
	/** 
	 * @description: sets a value for a specified option
	 * @todo: 
	 */
	function option_wp_set( $option_name = '', $value = '' ) {
	
		// test for null
		if ( $option_name == '' ) {
		
			// oops
			die( __( 'You must supply an option to option_wp_set()', 'commentpress-plugin' ) );
		
		}
	
		// set option
		return update_site_option( $option_name, $value );
		
	}
	
	
	
	
	
	
//#################################################################







	/*
	===============================================================
	PRIVATE METHODS
	===============================================================
	*/
	
	
	



	/*
	---------------------------------------------------------------
	Object Initialisation
	---------------------------------------------------------------
	*/
	
	/** 
	 * @description: object initialisation
	 * @todo:
	 *
	 */
	function _init() {
		
		// load options array
		$this->cpmu_options = $this->option_wp_get( 'cpmu_options', $this->cpmu_options );
		
		// if we don't have one
		if ( count( $this->cpmu_options ) == 0 ) {
		
			// if not in backend
			if ( !is_admin() ) {
		
				// init upgrade
				//die( 'Commentpress upgrade required.' );
				
			}
		
		}
		
	}







//#################################################################







} // class ends






?>