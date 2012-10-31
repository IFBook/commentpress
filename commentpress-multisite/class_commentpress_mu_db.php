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
	
	// options page
	var $options_page;
	






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

		// store options array
		add_option( 'cpmu_options', $this->cpmu_options );
		
		// store Commentpress Multisite version
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
	
	
	
	
	
	

	/** 
	 * @description: Commentpress initialisation
	 * @todo:
	 *
	 */
	function install_commentpress() {
		
		// activate core
		commentpress_activate_core();
		
		// access globals
		global $commentpress_obj, $wpdb;
		
		// run activation hook
		$commentpress_obj->activate();
		
		// activate ajax
		commentpress_activate_ajax();
		


		/*
		------------------------------------------------------------------------
		Configure Commentpress based on admin page settings
		------------------------------------------------------------------------
		*/
		
		// TODO: create admin page settings
		
		// TOC = posts
		//$commentpress_obj->db->option_set( 'cp_show_posts_or_pages_in_toc', 'post' );
	
		// TOC show extended posts
		//$commentpress_obj->db->option_set( 'cp_show_extended_toc', 1 );
	
		

		/*
		------------------------------------------------------------------------
		Further Commentpress plugins may define Blog Workflows and Type and
		enable them to be set in the blog signup form. 
		------------------------------------------------------------------------
		*/
		
		// check for (translation) workflow (checkbox)
		$cp_blog_workflow = 0;
		if ( isset( $_POST['cp_blog_workflow'] ) ) {
			// ensure boolean
			$cp_blog_workflow = ( $_POST['cp_blog_workflow'] == '1' ) ? 1 : 0;
		}

		// set workflow
		$commentpress_obj->db->option_set( 'cp_blog_workflow', $cp_blog_workflow );
	
	
	
		// check for blog type (dropdown)
		$cp_blog_type = 0;
		if ( isset( $_POST['cp_blog_type'] ) ) {
			$cp_blog_type = intval( $_POST['cp_blog_type'] );
		}

		// set blog type
		$commentpress_obj->db->option_set( 'cp_blog_type', $cp_blog_type );
		


		// save
		$commentpress_obj->db->options_save();
	


		/*
		------------------------------------------------------------------------
		Set WordPress Internal Configuration
		------------------------------------------------------------------------
		*/
		
		/*
		// allow anonymous commenting (may be overridden)
		$anon_comments = 0;
	
		// allow plugin overrides
		$anon_comments = apply_filters( 'cp_require_comment_registration', $anon_comments );
	
		// update wp option
		update_option( 'comment_registration', $anon_comments );

		// add Lorem Ipsum to "Sample Page" if the Network setting is empty?
		$first_page = get_site_option( 'first_page' );
		
		// is it empty?
		if ( $first_page == '' ) {
			
			// get it & update content, or perhaps delete?
			
		}
		*/
		
	}
	
	
	




	/** 
	 * @description: Commentpress deactivation
	 * @todo:
	 *
	 */
	function uninstall_commentpress() {
		
		// activate core
		commentpress_activate_core();
		
		// access globals
		global $commentpress_obj, $wpdb;
		
		// run activation hook
		$commentpress_obj->deactivate();
		


		/*
		------------------------------------------------------------------------
		Reset WordPress Internal Configuration
		------------------------------------------------------------------------
		*/
		
		/*
		// allow anonymous commenting (may be overridden)
		$anon_comments = 0;
	
		// allow plugin overrides
		$anon_comments = apply_filters( 'cp_require_comment_registration', $anon_comments );
	
		// update wp option
		update_option( 'comment_registration', $anon_comments );

		// add Lorem Ipsum to "Sample Page" if the Network setting is empty?
		$first_page = get_site_option( 'first_page' );
		
		// is it empty?
		if ( $first_page == '' ) {
			
			// get it & update content, or perhaps delete?
			
		}
		*/
		
	}
	
	
	




	/** 
	 * @description: get workflow form data
	 * @return: keyed array of form data
	 *
	 */
	function get_workflow_data() {
	
		// init
		$return = array();
	
		// off by default
		$has_workflow = false;
	
		// init output
		$workflow_html = '';
	
		// allow overrides
		$has_workflow = apply_filters( 'cp_blog_workflow_exists', $has_workflow );
		
		// if we have workflow enabled, by a plugin, say...
		if ( $has_workflow !== false ) {
		
			// define workflow label
			$workflow_label = __( 'Enable Custom Workflow', 'commentpress-plugin' );
			
			// allow overrides
			$workflow_label = apply_filters( 'cp_blog_workflow_label', $workflow_label );
			
			// add to return
			$return['label'] = $workflow_label;
			
			// define form element
			$workflow_element = '<input type="checkbox" value="1" id="cp_blog_workflow" name="cp_blog_workflow" />';
			
			// add to return
			$return['element'] = $workflow_element;

		}
		
		// --<
		return $return;
		
	}
	
	
	



	/** 
	 * @description: get blog type form elements
	 * @return: keyed array of form data
	 *
	 */
	function get_blogtype_data() {
	
		// init
		$return = array();
	
		// assume no types
		$types = array();
		
		// but allow overrides for plugins to supply some
		$types = apply_filters( 'cp_blog_type_options', $types );
		
		// if we got any, use them
		if ( !empty( $types ) ) {
		
			// define blog type label
			$type_label = __( 'Document Type', 'commentpress-plugin' );
			
			// allow overrides
			$type_label = apply_filters( 'cp_blog_type_label', $type_label );
			
			// add to return
			$return['label'] = $type_label;
			
			// construct options
			$type_option_list = array();
			$n = 0;
			foreach( $types AS $type ) {
				$type_option_list[] = '<option value="'.$n.'">'.$type.'</option>';
				$n++;
			}
			$type_options = implode( "\n", $type_option_list );
			
			// add to return
			$return['element'] = $type_options;

		}
		
		// --<
		return $return;
		
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
		
		
		
		// ----------------------------------------
		// optionally load Commentpress core 
		// ----------------------------------------
		
		// init
		$cp_active = false;
		
		// if we're network-enabled
		if ( CP_PLUGIN_CONTEXT == 'mu_sitewide' ) {
		
			// do we have Commentpress options?
			if ( get_option( 'cp_options', false ) ) {
			
				// get them
				$_cp_options = get_option( 'cp_options' );
			
				// if we have "special pages", then the plugin must be active on this blog
				if ( isset( $_cp_options[ 'cp_special_pages' ] ) ) {
				
					// init
					$cp_active = true;
					
				}
				
			}
			
			// is Core active?
			if ( $cp_active ) {
			
				// activate core
				commentpress_activate_core();
				
				// activate ajax
				commentpress_activate_ajax();
			
				// modify Commentpress settings page
				add_filter( 
					'cpmu_deactivate_commentpress_element', 
					array( $this, '_get_deactivate_element' )
				);
				
				// hook into Commentpress settings page result
				add_action( 
					'cpmu_deactivate_commentpress',
					array( $this, '_disable_core' )
				);
				
			} else {
			
				// modify admin menu
				add_action( 'admin_menu', array( $this, '_admin_menu' ) );
				
			}
		
		}
		
	}







	/** 
	 * @description: appends option to admin menu
	 * @todo: 
	 *
	 */
	function _admin_menu() {
		
		// sanity check function exists
		if ( !function_exists('current_user_can') ) { return; }
	
		// check user permissions
		if ( !current_user_can('manage_options') ) { return; }
		


		// enable Commentpress Core, if applicable
		$this->_enable_core();
		


		// insert item in relevant menu
		$this->options_page = add_options_page(
		
			__( 'Commentpress Settings', 'commentpress-plugin' ), 
			__( 'Commentpress', 'commentpress-plugin' ), 
			'manage_options', 
			'cp_admin_page', 
			array( $this, '_options_page' )
			
		);
		
		//print_r( $this->options_page );die();
		
		
		
		// add scripts and styles
		//add_action( 'admin_print_scripts-'.$this->options_page, array( $this, 'admin_js' ) );
		//add_action( 'admin_print_styles-'.$this->options_page, array( $this, 'admin_css' ) );
		//add_action( 'admin_head-'.$this->options_page, array( $this, 'admin_head' ), 50 );
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: prints plugin options page
	 * @todo: 
	 *
	 */
	function _options_page() {
	
		// sanity check function exists
		if ( !function_exists('current_user_can') ) { return; }
	
		// check user permissions
		if ( !current_user_can('manage_options') ) { return; }
		
		// get our admin options page
		echo $this->_get_admin_page();
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: got the Wordpress admin page
	 * @return string $admin_page
	 * @todo: 
	 *
	 */
	function _get_admin_page() {
	
		// init
		$admin_page = '';
		
		
		
		// open div
		$admin_page .= '<div class="wrap" id="cp_admin_wrapper">'."\n\n";
	
		// get our form
		$admin_page .= $this->_get_admin_form();
		
		// close div
		$admin_page .= '</div>'."\n\n";
		
		
		
		// --<
		return $admin_page;
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: returns the admin form HTML
	 * @return string $admin_page
	 * @todo: translation
	 *
	 */
	function _get_admin_form() {
	
		// sanitise admin page url
		$url = $_SERVER['REQUEST_URI'];
		$url_array = explode( '&', $url );
		if ( $url_array ) { $url = $url_array[0]; }



		// define admin page
		$admin_page = '
<div class="icon32" id="icon-options-general"><br/></div>

<h2>Commentpress Settings</h2>



<form method="post" action="'.htmlentities( $url.'&updated=true' ).'">

'.wp_nonce_field( 'cp_admin_action', 'cp_nonce', true, false ).'
'.wp_referer_field( false ).'



<h4>Activation</h4>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="cp_activate_commentpress">Activate Commentpress</label></th>
		<td><input id="cp_activate_commentpress" name="cp_activate_commentpress" value="1" type="checkbox" /></td>
	</tr>

'.$this->_get_workflow().'
'.$this->_get_blogtype().'

</table>



<input type="hidden" name="action" value="activate" />



<p class="submit">
	<input type="submit" name="cp_submit" value="Save Changes" class="button-primary" />
</p>

</form>'."\n\n\n\n";
		
		
		
		// --<
		return $admin_page;
		
	}
	
	
	
	
	
	

	/** 
	 * @description: get workflow form elements
	 * @return: form html
	 *
	 */
	function _get_workflow() {
	
		// init
		$workflow_html = '';
	
		// get data
		$workflow = $this->get_workflow_data();
		
		// if we have workflow data...
		if ( !empty( $workflow ) ) {
		
			// show it
			$workflow_html = '
			
	<tr valign="top">
		<th scope="row"><label for="cp_blog_workflow">'.$workflow['label'].'</label></th>
		<td>'.$workflow['element'].'</td>
	</tr>
		
			';
		
		}
		
		// --<
		return $workflow_html;
		
	}
	
	
	



	/** 
	 * @description: get blog type form elements
	 *
	 */
	function _get_blogtype() {
	
		// init
		$type_html = '';
	
		// get data
		$type = $this->get_blogtype_data();
		
		// if we have type data...
		if ( !empty( $type ) ) {
		
			// show it
			$type_html = '
			
	<tr valign="top">
		<th scope="row"><label for="cp_blog_type">'.$type['label'].'</label></th>
		<td><select id="cp_blog_type" name="cp_blog_type">
		
		'.$type['element'].'
		
		</select></td>
	</tr>

			';
		
		}
		
		
		
		// --<
		return $type_html;
		
	}
	
	
	
	
	
	
	/** 
	 * @description: enable Commentpress Core
	 * @todo: 
	 *
	 */
	function _enable_core() {
		
		// database object
		global $wpdb;
		
	 	// was the form submitted?
		if( !isset( $_POST['cp_submit'] ) ) { return; }

		// check that we trust the source of the data
		check_admin_referer( 'cp_admin_action', 'cp_nonce' );
		
			
			
		// init var
		$cp_activate_commentpress = 0;
		
		// get vars
		extract( $_POST );
		
		
		
		// did we ask to activate Commentpress?
		if ( $cp_activate_commentpress == '1' ) {
			
			// install core
			$this->install_commentpress();
			
			// redirect
			wp_redirect( $_SERVER[ 'REQUEST_URI' ] );
		
			// --<
			exit();
		
		}
		
	}
	
	
	
	
	
	
	/** 
	 * @description: get deactivation form element
	 * @return: form html
	 *
	 */
	function _get_deactivate_element() {
	
		// define html
		return '
	<tr valign="top">
		<th scope="row"><label for="cp_deactivate_commentpress">Deactivate Commentpress</label></th>
		<td><input id="cp_deactivate_commentpress" name="cp_deactivate_commentpress" value="1" type="checkbox" /></td>
	</tr>
';		
		
	}
	
	
	




	/** 
	 * @description: disable Commentpress Core
	 * @todo: 
	 *
	 */
	function _disable_core() {
		
		// database object
		global $wpdb;
		
	 	// was the form submitted?
		if( !isset( $_POST['cp_submit'] ) ) { return; }

		// check that we trust the source of the data
		check_admin_referer( 'cp_admin_action', 'cp_nonce' );
		
		// init var
		$cp_deactivate_commentpress = 0;
		
		// get vars
		extract( $_POST );
		
		
		
		// did we ask to activate Commentpress?
		if ( $cp_deactivate_commentpress == '1' ) {
			
			// uninstall core
			$this->uninstall_commentpress();
			
			// redirect
			wp_redirect( $_SERVER[ 'REQUEST_URI' ] );
		
			// --<
			exit();
		
		}
		
		
		
		// --<
		return;
		
	}
	
	
	
	
	
	
//#################################################################







} // class ends






?>