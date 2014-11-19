<?php
/* 
This are functions calls specific to our app

Developed by: Alan Levine @cogdog
URL: http://cogdog.info/

*/

// Exit if accessed directly outside of WP
if ( !defined('ABSPATH')) exit;

/* ----- Let's set things up ---------------------------------------------------------- */

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


// --------------------------------------------------------------------------------------
// login stuff

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

add_action( 'after_setup_theme', 'comparator_autologin' );

function comparator_autologin() {
	// URL Paramter to check for to trigger login
	if ($_GET['autologin'] == 'splot') {
		
		// ACCOUNT USERNAME TO LOGIN TO
		$creds['user_login'] = 'splot';
		
		// ACCOUNT PASSWORD TO USE
		$creds['user_password'] = 'beforeafter';
		
		$creds['remember'] = true;
		$autologin_user = wp_signon( $creds, false );
		
		if ( !is_wp_error($autologin_user) ) 
			header('Location:/comparator/make'); // LOCATION TO REDIRECT TO
	}
}

// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
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

// Get attachment ID from image URL
// -- h/t  http://themeforest.net/forums/thread/get-attachment-id-by-image-url/36381

function get_attachment_id_by_src ($image_src) {

    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;

}

?>