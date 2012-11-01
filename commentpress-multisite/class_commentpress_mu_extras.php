<?php /*
================================================================================
Class CommentPressMultisiteExtras Version 1.0
================================================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
--------------------------------------------------------------------------------
NOTES
=====

This class overrides and customises some Multisite functionality

TODO
====

Merge this into the existing plugin...

--------------------------------------------------------------------------------
*/






/*
================================================================================
Class Name
================================================================================
*/

class CommentPressMultisiteExtras {






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
	function CommentPressMultisiteExtras( $parent_obj = null ) {
		
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
	BuddyPress Compatibility
	----------------------------------------------------------------------------
	*/
	
	/**
	 * @description: override the name of the filter item
	 * @todo: 
	 *
	 */
	function groupblog_comment_name() { 
	
		// default name
		return __( 'Workshop Comments', 'commentpress-plugin' );
		
	}
	
	
	



	/** 
	 * @description: override the name of the filter item
	 * @todo: 
	 *
	 */
	function groupblog_post_name() {
	
		// default name
		return __( 'Workshop Posts', 'commentpress-plugin' );
	
	}
	
	
	



	/** 
	 * @description: override the name of the filter item
	 * @todo: 
	 *
	 */
	function activity_post_name() {
	
		// default name
		return __( 'workshop post', 'commentpress-plugin' );
	
	}
	
	
	



	/** 
	 * @description: override the name of the sub-nav item
	 * @todo: 
	 *
	 */
	function filter_blog_name( $name ) {
	
		return __( 'Workshop', 'commentpress-plugin' );
		
	}
	
	
	
	
	
	
	/** 
	 * @description: override the slug of the sub-nav item
	 * @todo: 
	 *
	 */
	function filter_blog_slug( $slug ) {
	
		return 'workshop';
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: override the name of the theme that is made active in WP < 3.4
	 * @todo: 
	 *
	 */
	function groupblog_theme_name( $existing ) {
	
		// switch to Demo theme
		return 'Commentpress Child Theme';
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: override the slug of the theme that is made active in WP3.4+
	 * @todo: 
	 *
	 */
	function groupblog_theme_slug( $existing ) {
	
		// switch to Demo theme
		return 'commentpress-demo';
		
	}
	
	
	
	
	
	
	
	/** 
	 * @description: override the title of the "Create a new document" link
	 * @todo: 
	 *
	 */
	function user_links_new_site_title() {
	
		// override default link name
		return apply_filters(
			'cpmsextras_user_links_new_site_title', 
			__( 'Create a new site', 'commentpress-plugin' )
		);
	
	}
	
	
	
	
	
	
	
	/** 
	 * @description: override the title of the "Recent Comments in..." link
	 * @todo: 
	 *
	 */
	function activity_tab_recent_title_blog( $title ) {
	
		// if groupblog...
		global $commentpress_obj;
		if ( 
		
			!is_null( $commentpress_obj ) 
			AND is_object( $commentpress_obj ) 
			AND $commentpress_obj->is_groupblog() 
			
		) { 
		
			// override default link name
			return apply_filters(
				'cpmsextras_user_links_new_site_title', 
				__( 'Recent Comments in this Workshop', 'commentpress-plugin' )
			);
			
		}
		
		// if main site...
		if ( is_multisite() AND is_main_site() ) { 
		
			// override default link name
			return apply_filters(
				'cpmsextras_user_links_new_site_title', 
				__( 'Recent Comments in Site Blog', 'commentpress-plugin' )
			);
			
		}
		
		return $title;
	
	}
	
	
	
	
	
	
	
	/** 
	 * @description: override the slug of the sub-nav item
	 * @todo: 
	 *
	 */
	function get_blogs_visit_blog_button( $button ) {
		
		//print_r( $button ); die();
		
		global $blogs_template;
		if( !get_groupblog_group_id( $blogs_template->blog->blog_id ) ) {
		
			// check site_options to see if site is a Commentpress-enabled one
			
			// otherwise, leave the button untouched
			
		} else {
			
			// update link for groupblogs
			$label = __( 'Visit Workshop', 'commentpress-plugin' );
			$button['link_text'] = apply_filters( 'cpmsextras_visit_blog_button', $label );
			$button['link_title'] = apply_filters( 'cpmsextras_visit_blog_button', $label );
		
		}
		
		return $button;
	
	}
	
	
	
	
	
	
	/** 
	 * @description: add a css class depending on the workshop type
	 * @todo: 
	 *
	 */
	function get_activity_css_class( $component_type_class ) {
		
		// do we need to add a class?
		//print_r( array( 'css' => $component_type_class ) ); die();
		
		return $component_type_class;
	
	}
	
	
	
	
	
	/** 
	 * @description: amend the post title prefix
	 * @todo: 
	 *
	 */
	function new_post_title_prefix( $prefix ) {
		
		// don't use a prefix
		return '';
	
	}
	
	
	
	
	
	/** 
	 * @description: add suffix " - Draft N", where N is the latest version number
	 * @todo: 
	 *
	 */
	function new_post_title( $title, $post ) {
	
		// get incremental version number of source post
		$key = '_cp_version_count';
		
		// if the custom field of our current post has a value...
		if ( get_post_meta( $post->ID, $key, true ) != '' ) {
		
			// get current value
			$value = get_post_meta( $post->ID, $key, true );
			
			// increment
			$value++;
			
		} else {
		
			// this must be the first new version (Draft 2)
			$value = 2;
		
		}
		
		
		
		// do we already have our suffix in the title?
		if ( stristr( $title, ' - Draft ' ) === false ) {
		
			// no, append " - Draft N"
			$title = $title.' - Draft '.$value;
			
		} else {
		
			// yes, split
			$title_array = explode( ' - Draft ', $title );
			
			// append to first part
			$title = $title_array[0].' - Draft '.$value;
			
		}
		
		
		
		// --<
		return $title;
	
	}
	
	
	
	
	

	/** 
	 * @description: override title on All Comments page
	 * @todo: 
	 *
	 */
	function page_all_comments_blog_title( $title ) {
	
		// --<
		return __( 'Comments on Workshop Posts', 'commentpress-plugin' );
	
	}
	
	
	
	
	

	/** 
	 * @description: override title on All Comments page
	 * @todo: 
	 *
	 */
	function page_all_comments_book_title( $title ) {
	
		// --<
		return __( 'Comments on Workshop Pages', 'commentpress-plugin' );
	
	}
	
	
	
	
	

	/** 
	 * @description: override title on Activity tab
	 * @todo: 
	 *
	 */
	function filter_activity_title_all_yours( $title ) {
	
		// --<
		return __( 'Recent Activity in your Workshops', 'commentpress-plugin' );
	
	}
	
	
	
	
	

	/** 
	 * @description: override title on Activity tab
	 * @todo: 
	 *
	 */
	function filter_activity_title_all_public( $title ) {
	
		// --<
		return __( 'Recent Activity in Public Workshops', 'commentpress-plugin' );
	
	}
	
	
	
	
	

	/** 
	 * @description: override Commentpress "Title Page"
	 * @todo: 
	 *
	 */
	function filter_nav_title_page_title( $title ) {
		
		// access globals
		global $commentpress_obj;

		// if plugin active...
		if ( 
		
			!is_null( $commentpress_obj ) 
			AND is_object( $commentpress_obj )
			AND $commentpress_obj->is_groupblog()
			
		) {
		
			// --<
			return __( 'Workshop Home Page', 'commentpress-plugin' );
			
		}
		
		// --<
		return $title;
	
	}
	
	
	
	
	

	/** 
	 * @description: override default setting for comment registration
	 * @todo: 
	 *
	 */
	function require_comment_registration( $comment_registration ) {
	
		// --<
		return 1;
	
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
		
		// filter bp-groupblog defaults
		add_filter( 'bp_groupblog_subnav_item_name', array( $this, 'filter_blog_name' ), 21 );
		add_filter( 'bp_groupblog_subnav_item_slug', array( $this, 'filter_blog_slug' ), 21 );
		
		// change name of activity sidebar headings
		add_filter( 'cp_activity_tab_recent_title_all_yours', array( $this, 'filter_activity_title_all_yours' ), 21 );
		add_filter( 'cp_activity_tab_recent_title_all_public', array( $this, 'filter_activity_title_all_public' ), 21 );
		
		// override Commentpress "new doc" text
		add_filter( 'cp_user_links_new_site_title', array( $this, 'user_links_new_site_title' ), 21 );
		add_filter( 'cp_register_new_site_page_title', array( $this, 'user_links_new_site_title' ), 21 );
		
		// override Commentpress "Title Page"
		add_filter( 'cp_nav_title_page_title', array( $this, 'filter_nav_title_page_title' ), 21 );
		
		// override with 'workshop'
		add_filter( 'cp_activity_tab_recent_title_blog', array( $this, 'activity_tab_recent_title_blog' ), 21, 1 );
		
		// override CP title of "view document" button in blog lists
		add_filter( 'bp_get_blogs_visit_blog_button', array( $this, 'get_blogs_visit_blog_button' ), 21 );
		
		// override theme that is activated (pre-WP3.4)
		add_filter( 'cp_groupblog_theme_name', array( $this, 'groupblog_theme_name' ), 21 );

		// override theme that is activated (WP3.4+)
		add_filter( 'cp_groupblog_theme_slug', array( $this, 'groupblog_theme_slug' ), 21 );
		
		// override titles of BP activity filters
		add_filter( 'cp_groupblog_comment_name', array( $this, 'groupblog_comment_name' ), 21 );
		add_filter( 'cp_groupblog_post_name', array( $this, 'groupblog_post_name' ), 21 );
		
		// cp_activity_post_name_filter
		add_filter( 'cp_activity_post_name', array( $this, 'activity_post_name' ), 21 );
		
		// add filter for new post title prefix
		add_filter( 'cp_new_post_title_prefix', array( $this, 'new_post_title_prefix' ), 21, 1 );

		// add filter for new post title
		add_filter( 'cp_new_post_title', array( $this, 'new_post_title' ), 21, 2 );

		// cp_get_blogs_visit_blog_button
		//add_filter( 'cp_get_blogs_visit_blog_button', array( $this, 'get_blogs_visit_blog_button' ), 21 );
		
		// add class to activity items
		//add_filter( 'bp_get_activity_css_class', array( $this, 'get_activity_css_class' ), 21, 1 );

		// override label on All Comments page
		add_filter( 'cp_page_all_comments_book_title', array( $this, 'page_all_comments_book_title' ), 21, 1 );
		add_filter( 'cp_page_all_comments_blog_title', array( $this, 'page_all_comments_blog_title' ), 21, 1 );
		
		// disallow anonymous commenting
		add_filter( 'cp_require_comment_registration', array( $this, 'require_comment_registration' ), 21, 1 );

		// is this the back end?
		if ( is_admin() ) {
	
			// add options to network settings form
			//add_filter( 'cpmu_network_multisite_options_form', array( $this, '_network_admin_form' ), 20 );
				
			// add options to buddypress settings form
			//add_filter( 'cpmu_network_buddypress_options_form', array( $this, '_buddypress_admin_form' ), 20 );
				
		}
		
	}
	
	
	



	/** 
	 * @description: add our options to the network admin form
	 * @todo: 
	 *
	 */
	function _network_admin_form() {
	
		// init
		$element = '';
	
		// label
		$label = __( 'Disable Translation Workflow (Recommended because it is still very experimental)', 'commentpress-plugin' );
		
		// define element
		$element .= '
	<tr valign="top">
		<th scope="row"><label for="cpmu_allow_translation_workflow">'.$label.'</label></th>
		<td><input id="cpmu_allow_translation_workflow" name="cpmu_allow_translation_workflow" value="1" type="checkbox" /></td>
	</tr>
';
		
		// --<
		return $element;

	}
	
	
	
	
	
	
	/** 
	 * @description: add our options to the buddypress admin form
	 * @todo: 
	 *
	 */
	function _buddypress_admin_form() {
	
		// label
		$label = __( 'Reset BuddyPress settings', 'commentpress-plugin' );
		
		// define admin page
		$element = '
	<tr valign="top">
		<th scope="row"><label for="cpmu_bp_reset">'.$label.'</label></th>
		<td><input id="cpmu_bp_reset" name="cpmu_bp_reset" value="1" type="checkbox" /></td>
	</tr>

';
		
		// --<
		return $element;

	}
	
	
	
	
	
	
//##############################################################################
	
	
	



} // class ends
	
	
	




