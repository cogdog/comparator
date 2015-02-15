<?php
/*
Template Name: Welcome Desk
*/

// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = '';

// the passcode to enter
$wAccessCode = comparator_option('accesscode');


// ------------------------ door check -----------------------


// already logged in? go directly to the tool
if ( is_user_logged_in() ) {
	
	if ( current_user_can( 'edit_others_posts' ) ) {

		// If user has edit/admin role, send them to the tool
		wp_redirect ( site_url() . '/make' );
  		exit;

	} else {
	
		// if the correct user found, go directly to the tool
		if ( comparator_check_user() ) {			
	  		wp_redirect ( site_url() . '/make' );
  			exit;
  			
  		} else {
			// we need to force a click through a logout
			$log_out_warning = true;
			$feedback_msg = 'First, please <a href="' . wp_logout_url( site_url() . '/make' ) . '">activate lasers</a>';
  		}
  	}
  	
} elseif ( $wAccessCode == '')  {
	
	// no code required, log 'em in
	wp_redirect ( site_url() . '/wp-login.php?autologin=splot' );
	exit;

}


// ------------------------ presets ------------------------


// verify that a  form was submitted and it passes the nonce check
if ( 	isset( $_POST['comparator_form_access_submitted'] ) 
		&& wp_verify_nonce( $_POST['comparator_form_access_submitted'], 'comparator_form_access' ) ) {
 
	// grab the variables from the form
	$wAccess = 	stripslashes( $_POST['wAccess'] );
	
	// let's do some validation, store an error message for each problem found
	$errors = array();
	
	if ( $wAccess != $wAccessCode ) $errors[] = '<p><strong>Incorrect Access Code</strong> - try again? Hint: ' . comparator_option('accesshint'); 	
	
	if ( count($errors) > 0 ) {
		// form errors, build feedback string to display the errors
		$feedback_msg = '';
		
		// Hah, each one is an oops, get it? 
		foreach ($errors as $oops) {
			$feedback_msg .= $oops;
		}
		
		$feedback_msg .= '</p>';
		
	} else {

		wp_redirect ( site_url() . '/wp-login.php?autologin=splot' );
		exit;
	}

		
} // end form submmitted check
?><?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-8 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>

					<?php if ($log_out_warning):?>
						<div class="notify notify-green"><span class="symbol icon-tick"></span>
						<?php echo $feedback_msg?>
						</div>

					<?php else:?>
					
	
					
						<?php  
						// set up box code colors CSS

						if ( count( $errors ) ) {
							$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
							echo $box_style . $feedback_msg . '</div>';
						} 
						?>   

	 					<form  id="comparatordesk" class="comparatordesk" method="post" action="">
					
								<fieldset>
									<label for="wAccess"><?php _e('Access Code', 'wpbootstrap' ) ?></label><br />
									<p>Enter the secret code</p>
									<input type="text" name="wAccess" id="wAccess" class="required" value="<?php echo $wAccess; ?>" tabindex="1" />
								</fieldset>	
			
								<fieldset>
									<?php wp_nonce_field( 'comparator_form_access', 'comparator_form_access_submitted' ); ?>
									<input type="submit" class="pretty-button pretty-button-blue" value="Check Code" id="checkit" name="checkit" tabindex="15">
								</fieldset>
				
						</form>
				
					<?php endif?>


					
						</section> <!-- end article section -->
						
						<footer>
			
							<?php the_tags('<p class="tags"><span class="tags-title">' . __("Tags","wpbootstrap") . ':</span> ', ', ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
										
					<?php endwhile; ?>		
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("Not Found", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, but the requested resource was not found on this site.", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
        
			</div> <!-- end #content -->

<?php get_footer(); ?>