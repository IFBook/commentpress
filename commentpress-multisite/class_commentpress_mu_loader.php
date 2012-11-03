<?php /*
================================================================================
Class CommentPressMultiSiteLoader Version 1.0
================================================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
--------------------------------------------------------------------------------
NOTES
=====

This class loads all Multisite compatibility

--------------------------------------------------------------------------------
*/






/*
================================================================================
Class Name
================================================================================
*/

class CommentPressMultiSiteLoader {






	/*
	============================================================================
	Properties
	============================================================================
	*/
	
	// parent object reference
	var $parent_obj;
	
	// admin object reference
	var $db;
	
	// multisite object reference
	var $mu;
	
	// buddypress object reference
	var $bp;
	
	
	
	
	
	
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
	
		// init
		$this->_init();

		// --<
		return $this;

	}
	
	
	



	/**
	 * PHP 4 constructor
	 */
	function CommentPressMultiSiteLoader( $parent_obj = null ) {
		
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
	 * @todo: 
	 *
	 */
	function initialise() {
	
	}
	
	
	



	/** 
	 * @description: if needed, destroys all items associated with this object
	 * @todo: 
	 *
	 */
	function destroy() {
	
	}
	
	
	



//##############################################################################
	
	
	



	/*
	============================================================================
	PUBLIC METHODS
	============================================================================
	*/
	
	
	




//##############################################################################
	
	
	



	/*
	============================================================================
	PRIVATE METHODS
	============================================================================
	*/
	
	
	



	/** 
	 * @description: object initialisation
	 * @todo:
	 *
	 */
	function _init() {
	
		// check for network deactivation
		add_action( 'deactivated_plugin',  array( $this, '_network_deactivated' ), 10, 2 );
		
		
		
		// ---------------------------------------------------------------------
		// load Database Wrapper object 
		// ---------------------------------------------------------------------
	
		// define filename
		$class_file = 'commentpress-multisite/class_commentpress_mu_admin.php';
	
		// get path
		$class_file_path = cp_file_is_present( $class_file );
		
		// we're fine, include class definition
		require_once( $class_file_path );
	
		// init autoload database object
		$this->db = new CommentPressMultisiteAdmin( $this );
		


		// ---------------------------------------------------------------------
		// load standard Multisite object 
		// ---------------------------------------------------------------------
	
		// define filename
		$class_file = 'commentpress-multisite/class_commentpress_mu_ms.php';
	
		// get path
		$class_file_path = cp_file_is_present( $class_file );
		
		// we're fine, include class definition
		require_once( $class_file_path );
	
		// init multisite object
		$this->mu = new CommentPressMultisite( $this );
		

		
		// ---------------------------------------------------------------------
		// load Post Revisions object (merge this into Core as an option)
		// ---------------------------------------------------------------------
	
		// define filename
		$_class_file = 'commentpress-multisite/class_commentpress_mu_revisions.php';
	
		// get path
		$_class_file_path = cp_file_is_present( $_class_file );
		
		// we're fine, include class definition
		require_once( $_class_file_path );
		
		// instantiate it
		$cpms_revisions = new CommentPressMultisiteRevisions( $this );
		


		// ---------------------------------------------------------------------
		// optionally load BuddyPress object 
		// ---------------------------------------------------------------------
	
		// load when buddypress is loaded
		add_action( 'bp_include', array( $this, '_load_buddypress_object' ) );

	}
	
	
	



	/** 
	 * @description: BuddyPress object initialisation
	 * @todo:
	 *
	 */
	function _load_buddypress_object() {
	
		// ---------------------------------------------------------------------
		// load BuddyPress object 
		// ---------------------------------------------------------------------
	
		// define filename
		$class_file = 'commentpress-multisite/class_commentpress_mu_bp.php';
	
		// get path
		$class_file_path = cp_file_is_present( $class_file );
		
		// we're fine, include class definition
		require_once( $class_file_path );
	
		// init buddypress object
		$this->bp = new CommentPressBuddyPress( $this );



		// ---------------------------------------------------------------------
		// load Groupblog Workshop renaming object 
		// ---------------------------------------------------------------------
	
		// define filename
		$_class_file = 'commentpress-multisite/class_commentpress_mu_workshop.php';
	
		// get path
		$_class_file_path = cp_file_is_present( $_class_file );
		
		// we're fine, include class definition
		require_once( $_class_file_path );
		
		// instantiate it
		$cpms_workshop = new CommentPressGroupBlogWorkshop( $this );
		


		// ---------------------------------------------------------------------
		// load Groupblog Theme selector object 
		// ---------------------------------------------------------------------
	
		// define filename
		$_class_file = 'commentpress-multisite/class_commentpress_mu_theme.php';
	
		// get path
		$_class_file_path = cp_file_is_present( $_class_file );
		
		// we're fine, include class definition
		require_once( $_class_file_path );
		
		// instantiate it
		$cpms_extras = new CommentPressGroupBlogTheme( $this );
		
	}
	
	
	



	/** 
	 * @description: deactivate this plugin
	 * @todo:
	 *
	 */
	function _network_deactivated( $plugin, $network_wide = null ) {
	
		// if it's our plugin...
		if ( $plugin == plugin_basename( CP_PLUGIN_FILE ) ) {
		
			// was it network deactivated?
			if ( $network_wide == true ) {
		
				//print_r( array( $plugin, $network_wide ) ); die();
				
				// do we want to trigger deactivation_hook for all sub-blogs?
				// or do we want to convert each instance into a self-contained
				// Commentpress blog?
				
			}
		
		}
		
	}
	
	
	



//##############################################################################
	
	
	



} // class ends
	
	
	



?>