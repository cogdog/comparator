<?php
/* 
This are functions calls specific to our this SPLOT

Developed by: Alan Levine @cogdog
URL: http://cogdog.info/

*/

// Exit if accessed directly outside of WP
if ( !defined('ABSPATH')) exit;


/* ----- Let's set things up ---------------------------------------------------------- */


// get the key, lee
// ---- you will need to edit this for your own version
require get_stylesheet_directory() . '/includes/misc.php';

// run when this theme is activated
add_action('after_switch_theme', 'comparator_setup');

function comparator_setup () {
  // create pages if they do not exist
  
  if (! get_page_by_path( 'make' ) ) {
  
  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Make',
  		'post_content'	=> 'Make Your Compator',
  		'post_name'		=> 'make',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'page_template'	=> 'page-make.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'desk' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Welcome Desk',
  		'post_content'	=> 'Welcome to the place to create a Comparator',
  		'post_name'		=> 'desk',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'page_template'	=> 'page-desk.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'random' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Random',
  		'post_content'	=> '(Place holder for random page)',
  		'post_name'		=> 'random',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'page_template'	=> 'page-random.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'embed' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Embed',
  		'post_content'	=> '(Place holder for embed page)',
  		'post_name'		=> 'embed',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'page_template'	=> 'page-embed.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

   
}

// --------------------------------------------------------------------------------------
// Predefined media sizes (small should still work on mobile screens)

$comp_size_options = array(
					'small-square' => array( 300, 300 ),
					'big-square' => array( 600, 600 ),
					'small-landscape'  => array( 400, 300 ),
					'small-portrait'  => array( 300, 400 ),
					'large-landscape' => array( 800, 600 ),
					'large-portrait' => array( 600, 800 ),
					'jumbo-landscape' => array( 1000, 750 )
				);

// add each size to wp				
foreach ($comp_size_options as $option => $dimensions) {
	add_image_size( $option, $dimensions[0], $dimensions[1], true);
}


// add each size's names as well
add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'small-square' => __('Small Square'),
        'big-square' => __('Big Square'),
        'small-landscape' => __('Small Landscape'),
        'small-portrait' => __('Small Portrait'),
        'large-landscape' => __('Large Landscape'),
        'large-portrait' => __('Large Portrait'),
        'jumbo-landscape' => __('Jumbo Landscape')
    ) );
}


/* ----- add allowable url parameter for urls */
add_filter('query_vars', 'comparator_parameter_queryvars' );

function comparator_parameter_queryvars( $qvars )

// allow  parameters to be passed in wordpress query strings
{
	$qvars[] = 'show'; 	// flag for showing w/o WP chrome
	$qvars[] = 'cid'; 	// post id for embeds
	return $qvars;	
}    


# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------

add_action( 'init', 'comparator_load_theme_options' );

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page

add_action( 'admin_menu', 'comparator_change_post_label' );
add_action( 'init', 'comparator_change_post_object' );

function comparator_change_post_label() {
    global $menu;
    global $submenu;
    
    $thing_name = 'Comparator';
    
    $menu[5][0] = $thing_name . 's';
    $submenu['edit.php'][5][0] = 'All ' . $thing_name . 's';
    $submenu['edit.php'][10][0] = 'Add ' . $thing_name;
    $submenu['edit.php'][15][0] = $thing_name .' Categories';
    $submenu['edit.php'][16][0] = $thing_name .' Tags';
    echo '';
}
function comparator_change_post_object() {

    $thing_name = 'Comparator';

    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name =  $thing_name . 's';;
    $labels->singular_name =  $thing_name;
    $labels->add_new = 'Add ' . $thing_name;
    $labels->add_new_item = 'Add ' . $thing_name;
    $labels->edit_item = 'Edit ' . $thing_name;
    $labels->new_item =  $thing_name;
    $labels->view_item = 'View ' . $thing_name;
    $labels->search_items = 'Search ' . $thing_name;
    $labels->not_found = 'No ' . $thing_name . ' found';
    $labels->not_found_in_trash = 'No ' .  $thing_name . ' found in Trash';
    $labels->all_items = 'All ' . $thing_name;
    $labels->menu_name =  $thing_name;
    $labels->name_admin_bar =  $thing_name;
}


# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'comparator_options_to_admin' );

function comparator_options_to_admin() {
    global $wp_admin_bar;
    
    // we can add a submenu item too
    $wp_admin_bar->add_menu( array(
        'parent' => '',
        'id' => 'comparator-options',
        'title' => __('Comparator Options'),
        'href' => admin_url( 'themes.php?page=comparator-options')
    ) );
}


function comparator_enqueue_options_scripts() {
	// Set up javascript for the theme options interface
	
	// media scripts needed for wordpress media uploaders
	wp_enqueue_media();
	
	// custom jquery for the options admin screen
	wp_register_script( 'comparator_options_js' , get_stylesheet_directory_uri() . '/js/jquery.comparator-options.js', null , '1.0', TRUE );
	wp_enqueue_script( 'comparator_options_js' );
}

function comparator_load_theme_options() {
	// load theme options Settings

	if ( file_exists( get_stylesheet_directory()  . '/class.comparator-theme-options.php' ) ) {
		include_once( get_stylesheet_directory()  . '/class.comparator-theme-options.php' );		
	}
}

# -----------------------------------------------------------------
# login stuff
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }    
	#backtoblog {display:none;}
	#nav {display:none;}
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'login_link' );

function login_link( $url ) {
	return get_bloginfo( 'url' );
}
 
 
// Auto Login
// create a link that can automatically log in as a specific user, bypass login screen
// -- h/t  http://www.wpexplorer.com/automatic-wordpress-login-php/

add_action( 'after_setup_theme', 'comparator_autologin');

function comparator_autologin() {
	
	// URL Paramter to check for to trigger login
	if ($_GET['autologin'] == 'splot') {
	
		// change to short auto logout time
		add_filter( 'auth_cookie_expiration', 'comparator_change_cookie_logout', 99, 3 );

		// ACCOUNT USERNAME TO LOGIN TO
		$creds['user_login'] = 'splot';
		
		// ACCOUNT PASSWORD TO USE- lame hard coded... I do not know how to get this
		// any other way since options  are not loaded yet
		$creds['user_password'] = APASS;
			
		$creds['remember'] = true;
		$autologin_user = wp_signon( $creds, false );
		
		
		
		if ( !is_wp_error($autologin_user) ) 
			wp_redirect ( site_url() . '/make' );
	}
}

function comparator_change_cookie_logout( $expiration, $user_id, $remember ) {
    return $remember ? $expiration : 120;
}

// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if ( !current_user_can('edit_others_posts')  ) {
	  show_admin_bar(false);
	}

}


// --------------------------------------------------------------------------------------
// Comparator Stuff

// deterime places to use comparator tools
function show_comparator() {
	return ( is_single() or is_page("make") or is_front_page() );

}


function add_comparator_scripts() {
	// after http://wordpress.stackexchange.com/a/116489/14945
	
	// admins already have this
    if ( is_admin() ) return;
 
 
 	if ( show_comparator() ) { // use just on single posts or our form page
	
	
		//register jquery ui
		wp_register_script( 'jquery-ui.min' , get_stylesheet_directory_uri() . '/js/jquery-ui.min.js', array( 'jquery' ), '1.11.2');
		wp_enqueue_script( 'jquery-ui.min' );

		// register before/after jquery plugin
		wp_register_script( 'jquery.beforeafter' , get_stylesheet_directory_uri() . '/js/jquery.beforeafter-1.4.min.js', array( 'jquery' ), '1.45', false);
		wp_enqueue_script( 'jquery.beforeafter' );
		
		// register before/after jquery plugin
		wp_register_script( 'jquery.touch-punch' , get_stylesheet_directory_uri() . '/js/jquery.ui.touch-punch.min.js', array( 'jquery' ), '0.2.3');
		wp_enqueue_script( 'jquery.touch-punch' );
	}
   
    if ( is_page('make') ) {
    
		 // add media scripts if we are on our maker page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945
    	 
		if (! is_admin() ) wp_enqueue_media();
		
		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );

		// custom jquery for the add thing form
		wp_register_script( 'jquery.make-comparator' , get_stylesheet_directory_uri() . '/js/jquery.make-comparator.js', array( 'jquery' ), '1.33', TRUE );
		wp_enqueue_script( 'jquery.make-comparator' );
	}

}

add_action('wp_enqueue_scripts', 'add_comparator_scripts');

function get_before_after_pairs ($post_id, $size='medium') {	
	$before_url = get_post_meta( $post_id, 'before_img', 1); 								
	$after_url = get_post_meta( $post_id, 'after_img', 1); 
	
	
	
	$before_attach_id = get_attachment_id_by_src ( $before_url );
	$after_attach_id = get_attachment_id_by_src ( $after_url );
	
	return ( 
		wp_get_attachment_image( $before_attach_id, $size ) . 
		wp_get_attachment_image( $after_attach_id, $size ) 
	);
}


# -----------------------------------------------------------------
# Useful spanners and wrenches
# -----------------------------------------------------------------

// Get attachment ID from image URL
// -- h/t  http://themeforest.net/forums/thread/get-attachment-id-by-image-url/36381

function get_attachment_id_by_src ($image_src) {

    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;
}


function comparator_author_user_check() {
// checks for an authoring account set up

	$auser = get_user_by( 'login', 'splot' );
	
	if ( !$auser) {
		return ('Authoring account not set up. You need to <a href="' . admin_url( 'user-new.php') . '">create a user account</a> with login name <strong>sounder</strong> with a role of <strong>Author</strong>. Make a killer strong password; no one uses it. Store it in ');
	} elseif ( $auser->roles[0] != 'author') {
		return ('The user account <strong>splot</strong> is set up but needs to have it\'s role set to <strong>Author</strong>. You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
	} else {
		return ('The authoring account <strong>splot</strong> is correctly set up.');
	}
}

function comparator_check_user( $allowed='splot' ) {
	// checks if the current logged in user is who we expect
	global $current_user;
    get_currentuserinfo();
	
	// return check of match
	return ( $current_user->user_login == $allowed );
}

function splot_the_author() {
	// utility to put in template to show status of special logins
	// nothing is printed if there is not current user, 
	//   echos (1) if logged in user is the special account
	//   echos (0) if logged in user is the another account
	//   in both cases the code is linked to a logout script

	if ( is_user_logged_in() and !current_user_can( 'edit_others_posts' ) ) {
		$user_code = ( comparator_check_user() ) ? 1 : 0;
		echo '<a href="' . wp_logout_url( site_url() ). '">(' . $user_code  .')</a>';
	}

}

?>