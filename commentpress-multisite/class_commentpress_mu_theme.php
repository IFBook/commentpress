<?php /*
================================================================================
Class CommentPressGroupBlogTheme
================================================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
--------------------------------------------------------------------------------
NOTES
=====

This class overrides the Theme that is selected when a Groupblog is created

--------------------------------------------------------------------------------
*/






/*
================================================================================
Class Name
================================================================================
*/

class CommentPressGroupBlogTheme {






	/*
	============================================================================
	Properties
	============================================================================
	*/
	
	// parent object reference
	var $parent_obj;
	
	
	



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
	function CommentPressGroupBlogTheme( $parent_obj = null ) {
		
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
	
	
	



	/*
	----------------------------------------------------------------------------
	Methods to be merged into BuddyPress object
	----------------------------------------------------------------------------
	*/
	
	/** 
	 * @description: WP < 3.4: override the the theme that is made active. This must be the theme NAME
	 * @todo: 
	 *
	 */
	function groupblog_theme_name( $existing ) {
	
		// switch to Demo theme
		return 'Commentpress Child Theme';
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: WP3.4+: override the theme that is made active. This must be the theme SLUG
	 * @todo: 
	 *
	 */
	function groupblog_theme_slug( $existing ) {
	
		// switch to Demo theme
		return 'commentpress-demo';
		
	}
	
	
	
	
	
	
	
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
	
		// register hooks
		$this->_register_hooks();
		
	}
	
	
	



	/** 
	 * @description: register Wordpress hooks
	 * @todo: 
	 *
	 */
	function _register_hooks() {
		
		// override theme that is activated (pre-WP3.4)
		add_filter( 'cp_groupblog_theme_name', array( $this, 'groupblog_theme_name' ), 21 );

		// override theme that is activated (WP3.4+)
		add_filter( 'cp_groupblog_theme_slug', array( $this, 'groupblog_theme_slug' ), 21 );
		
	}
	
	
	



//##############################################################################
	
	
	



} // class ends
	
	
	




