<?php /*
===============================================================
Class CommentPressMultiSite Version 1.0
===============================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
---------------------------------------------------------------
NOTES
=====

This class encapsulates all Multisite compatibility

---------------------------------------------------------------
*/






/*
===============================================================
Class Name
===============================================================
*/

class CommentPressMultiSite {






	/*
	===============================================================
	Properties
	===============================================================
	*/
	
	// parent object reference
	var $parent_obj;
	
	// admin object reference
	var $db;
	
	// context array
	var $context;
	
	// buddypress flag
	var $buddypress;
	
	
	



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
	function CommentPressMultiSite( $parent_obj = null ) {
		
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
	
	
	



//#################################################################
	
	
	



	/*
	===============================================================
	PUBLIC METHODS
	===============================================================
	*/
	
	
	



	/** 
	 * @description: add an admin page for this plugin
	 * @todo: 
	 *
	 */
	function add_admin_menu() {
		
		// we must be network admin
		if ( !is_super_admin() ) { return false; }
		
		
	
		// try and update options
		$saved = $this->db->options_update();
		


		// always add the admin page to the Settings menu
		$page = add_submenu_page( 
		
			'settings.php', 
			__( 'Commentpress Network', 'commentpress-plugin' ), 
			__( 'Commentpress Network', 'commentpress-plugin' ), 
			'manage_options', 
			'cpmu_admin_page', 
			array( $this, 'admin_page' )
			
		);
		
		// add styles only on our admin page, see:
		// http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
		add_action( 'admin_print_styles-'.$page, array( $this, 'add_admin_styles' ) );
	
	}
	
	
	



	/**
	 * @description: enqueue any styles and scripts needed by our admin page
	 * @todo: 
	 *
	 */
	function add_admin_styles() {
		
		/*
		// EXAMPLES:
		
		// add css
		wp_enqueue_style('cpmu-admin-style', CP_PLUGIN_URL . 'css/admin.css');
		
		// add javascripts
		wp_enqueue_script( 'cpmu-admin-js', CP_PLUGIN_URL . 'js/admin.js' );
		*/
		
	}
	
	
	



	/**
	 * @description: show our admin page
	 * @todo: 
	 *
	 */
	function admin_page() {
	
		// only allow network admins through
		if( is_super_admin() == false ) {
			
			// disallow
			wp_die( __( 'You do not have permission to access this page.', 'commentpress-plugin' ) );
			
		}
		
		
		
		// sanitise admin page url
		$url = $_SERVER['REQUEST_URI'];
		$url_array = explode( '&', $url );
		if ( is_array( $url_array ) ) { $url = $url_array[0]; }
		
		
		
		// show a message for now
		$msg = '<p>Holding page. No options are set here as yet.</p>';		
		
	
	
		// define admin page - needs translation cap
		$admin_page = '
<div class="icon32" id="icon-options-general"><br/></div>

<h2>Commentpress for Multisite</h2>

<form method="post" action="'.htmlentities($url.'&updated=true').'">

'.wp_nonce_field( 'cpmu_admin_action', 'cpmu_nonce', true, false ).'
'.wp_referer_field( false ).'



<p style="padding-top: 30px;">Checking environment...</p>

'.$msg.'



<p class="submit">
	<input type="submit" name="cpmu_submit" value="Save Changes" class="button-primary" />
</p>

</form>'."\n\n\n\n";

		// done
		echo $admin_page;
	
	}
	
	
	



	/**
	 * @description: enqueue any styles and scripts needed by our public pages
	 * @todo: 
	 *
	 */
	function add_frontend_styles() {
		
		/*
		// EXAMPLES:
		
		// add javascripts
		wp_enqueue_script( 
			
			'cpmu-admin-js', 
			CP_PLUGIN_URL . 'js/admin.js' 
			
		);
		*/
		
		// add css for signup form
		wp_enqueue_style( 
		
			'cpmu-signup-style', 
			CP_PLUGIN_URL . 'commentpress-multisite/css/signup.css'
			
		);
		
	}
	
	
	



	/** 
	 * @description: hook into the blog signup form
	 * @todo: 
	 *
	 */
	function signup_blogform( $errors ) {
	
		// only apply to wordpress signup form (not the BuddyPress one)
		if ( is_object( $this->parent_obj->bp ) ) { return; }


		
		// define title
		$title = __( 'Commentpress:', 'commentpress-plugin' );
		
		// define text
		$text = __( 'Do you want to make the new site a Commentpress document?', 'commentpress-plugin' );
		
		// allow overrides
		$text = apply_filters( 'cp_multisite_options_signup_text', $text );
		
		// define enable label
		$enable_label = __( 'Enable Commentpress', 'commentpress-plugin' );
		
		
		
		// get workflow element
		$workflow_html = $this->_get_workflow();
	
		// get blog type element
		$type_html = $this->_get_blogtype();
	


		// construct form
		$form = '

		<br />
		<div id="cp-multisite-options">

			<h3>'.$title.'</h3>

			<p>'.$text.'</p>

			<div class="checkbox">
				<label for="cpmu-new-blog"><input type="checkbox" value="1" id="cpmu-new-blog" name="cpmu-new-blog" /> '.$enable_label.'</label>
			</div>

			'.$workflow_html.'

			'.$type_html.'

		</div>

		';
		
		echo $form;
		
	}
	
	
	
	
	
	
	/** 
	 * @description: hook into wpmu_new_blog and target plugins to be activated
	 * @todo: 
	 *
	 */
	function wpmu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	
		// test for presence of our checkbox variable in _POST
		if ( isset( $_POST['cpmu-new-blog'] ) AND $_POST['cpmu-new-blog'] == '1' ) {
			
			// hand off to private method
			$this->_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );

		}
		
	}
	

	
	
	
	
//#################################################################
	
	
	



	/*
	===============================================================
	PRIVATE METHODS
	===============================================================
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
		
		// add form elements to signup form
		add_action( 'signup_blogform', array( $this, 'signup_blogform' ) );
		
		// activate blog-specific Commentpress plugin
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ), 12, 6 );
	
		// is this the back end?
		if ( is_admin() ) {
	
			// add menu to Network submenu
			add_action( 'network_admin_menu', array( $this, 'add_admin_menu' ), 30 );
		
		} else {
		
			// register any public styles
			add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_styles' ), 20 );
			
		}
		
	}
	
	
	



	/** 
	 * @description: create a blog
	 * @todo:
	 *
	 */
	function _create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	
		// wpmu_new_blog calls this *after* restore_current_blog, so we need to do it again
		switch_to_blog( $blog_id );
		
		// activate Commentpress core
		$this->db->install_commentpress();
		
		// switch back
		restore_current_blog();
		
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
		$workflow = $this->db->get_workflow_data();
		
		// if we have workflow data...
		if ( !empty( $workflow ) ) {
		
			// show it
			$workflow_html = '
			
			<div class="checkbox">
				<label for="cp_blog_workflow">'.$workflow['element'].' '.$workflow['label'].'</label>
			</div>

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
		$type = $this->db->get_blogtype_data();
		
		// if we have type data...
		if ( !empty( $type ) ) {
		
			// show it
			$type_html = '
			
	<div class="dropdown">
		<label for="cp_blog_type">'.$type['label'].'</label> <select id="cp_blog_type" name="cp_blog_type">
		
		'.$type['element'].'
		
		</select>
	</div>

			';
		
		}
		
		
		
		// --<
		return $type_html;
		
	}
	
	
	



//#################################################################
	
	
	



} // class ends
	
	
	



?>