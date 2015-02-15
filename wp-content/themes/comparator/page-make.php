<?php
/*
Template Name: Make a Comparator Form

Create a new comporator
*/

if ( !is_user_logged_in() ) {
	// already not logged in? go to desk.
  	wp_redirect ( site_url() . '/desk' );
  	exit;
  	
} elseif ( !current_user_can( 'edit_others_posts' ) ) {
	// okay user, who are you? we know you are not an admin or editor
		
	// if the collector user not found, we send you to the desk
	if ( !comparator_check_user() ) {
		// now go to the desk and check in properly
	  	wp_redirect ( site_url() . '/desk' );
  		exit;
  	}
}


// set af default values
$feedback_msg = 'You are here to build a Comparator of before and after images? Do we have a form for you!';

$cDimensions = 'large-landscape';
$cCats = array( comparator_option('def_cat') ); // preload default category

// status for new items
$my_new_status = comparator_option('new_item_status');
$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';


// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['comparator_form_make_submitted'] ) && wp_verify_nonce( $_POST['comparator_form_make_submitted'], 'comparator_form_make' ) ) {
 
 		// grab the variables from the form
 		$cTitle = 					sanitize_text_field( $_POST['cTitle'] );		
 		$cTags = 					sanitize_text_field( $_POST['cTags'] );	
 		$cDescription = 			esc_textarea( trim($_POST['cDescription']) );
 		$cDimensions =				$_POST['cDimensions'];
 		$cCats = 					( isset ($_POST['cCats'] ) ) ? $_POST['cCats'] : array();
 		
			
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		
 		if ( $cTitle == '' ) $errors[] = '<strong>Title Missing</strong> - please enter a descriptive title.'; 	
 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your entry. Please correct and try again.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul></div>';
 			
 			$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
 			
 		} else {
 			
 			// good enough, let's make a post! Or a custom post type
			$c_information = array(
				'post_title' => $cTitle,
				'post_content' => $cDescription,
				'tags_input'  => $cTags,
				'post_status' => $my_new_status,
				'post_category' => $cCats			
			);

			// insert as a post
			$post_id = wp_insert_post( $c_information );
			
			// check for success
			if ( $post_id ) {
				
				// update the new tags			
				wp_set_post_tags( $post_id, $cTags);
				
				// get the ID for the before image
				$before_id = get_attachment_id_by_src($_POST['cbeforeImageUrl']);
				
				// we will use this one for a thumbnail
				set_post_thumbnail( $post_id, $before_id);
				
				// write the meta data, the images for before, after, and the size 
				add_post_meta($post_id, 'before_img', $_POST['cbeforeImageUrl']);
				add_post_meta($post_id, 'after_img', $_POST['cafterImageUrl']);
				add_post_meta($post_id, 'compsize', $cDimensions);
							
				if  ( $my_new_status == 'publish' ) {				
					// build feedback if new things are automatically published
					
					// grab link to new thing
					$cLink = get_permalink( $post_id );						
					
					// feed back for published item
					$feedback_msg = 'Your new comparator  <strong>' . $cTitle . '</strong>  has been created. You can <a href="'. wp_logout_url( $cLink  )  . '">view it now</a>. Or you can <a href="' . site_url() . '/make">make another</a>.';
					
				} else {
					// feedback if new things are set to draft
					$feedback_msg = 'Your new comparator  <strong>' . $cTitle . '</strong> has been submitted as a draft. You can <a href="'. wp_logout_url( site_url() . '/?p=' . $post_id  )  . '">preview it now</a>. Once it has been approved by a moderator, everyone can see it. Or you can <a href="' . site_url() . '/make">make another</a>.';	
				
				}
 
			} // end if ($post_id)
			
			// set the gate	open, we are done.
			
			$is_published = true;
			$box_style = '<div class="notify notify-green"><span class="symbol icon-tick"></span> ';	

					
		} // end count errors
}
?>

<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-8 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>
							
							<?php echo $box_style . $feedback_msg . '</div>';?> 
							
						</section> <!-- end article section -->
						
						<footer>
							
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

				<br clear="both">
				</div> <!-- end #main -->
  
  
		<div class="clearfix row">
			<div class="center-block" width="800" id="cpreview">
				<div id="container">
					 <div><img alt="before" src="<?php echo get_stylesheet_directory_uri()?>/images/before-the-default.jpg" width="800" height="600" /></div>
					 <div><img alt="after" src="<?php echo get_stylesheet_directory_uri()?>/images/after-the-default.jpg" width="800" height="600" /></div>
				</div>
			</div>	
			
			<div><p>&nbsp;</p><p>&nbsp;</p></div>
		</div>
    
			
	<?php if ( is_user_logged_in() and !$is_published ) : // show form if logged in and it has not been published ?>
			
	<form  id="comparatorform" class="comparatorform" method="post" action="">
	
	
	<div class="clearfix row">
		<div class="col-md-5 col-md-offset-1 clearfix">
			<fieldset>
 				<label for="beforeImage"><?php _e('Before Image (left side)', 'wpbootstrap') ?></label>
 						
				<div class="uploader">
					<input id="cbeforeImageUrl" name="cbeforeImageUrl" type="hidden" value="<?php echo get_stylesheet_directory_uri()?>/images/before-the-default.jpg" />
					
					<img src="<?php echo get_stylesheet_directory_uri()?>/images/before-the-default-150x150.jpg" alt="before image icon" id="beforethumb" />
					
					<input type="button" id="cbeforeImage_button" class="btn btn-success btn-medium  upload_image_button" name="_beforeImage_button"  data-ctype="before"  data-uploader_title="Set Before Image" data-uploader_button_text="Select Image" value="Set Before Image" tabindex="1" />
				</div>
			</fieldset>
		</div>
		
		<div class="col-md-5">
		
			<fieldset>
				<label for="afterImage"><?php _e('After Image (right side)', 'wpbootstrap') ?></label>

				<div class="uploader">
					<input id="cafterImageUrl" name="cafterImageUrl" type="hidden" value="<?php echo get_stylesheet_directory_uri()?>/images/after-the-default.jpg" />
					
					<img src="<?php echo get_stylesheet_directory_uri()?>/images/after-the-default-150x150.jpg" alt="after image icon" id="afterthumb" />
					
					<input type="button" id="_afterImage_button" class="btn btn-success btn-medium upload_image_button" name="cafterImage_button" data-uploader_title="Set After Image" data-uploader_button_text="Select Image" data-ctype="after" value="Set After Image" tabindex="2"  />
				</div>
			</fieldset>
		
		</div>

	</div>

	<div class="clearfix row">
		<div class="col-md-5 col-md-offset-1 clearfix">
			<fieldset>
						<label for="cDimensions"><?php _e( 'Source Image Dimensions', 'wpbootstrap' )?></label><br />
						<br /> Original images must be equal to or larger than the desired output size. <br />
						<select name="cDimensions" id="cDimensions" class="required" tabindex="4" >
						<option value="--">Select...</option>
						<?php 
									
						foreach ($comp_size_options as $option => $dimensions) {
							$selected = ($option == $cDimensions) ? ' selected' : '';
						
							echo '<option value="' . $option . '"' . $selected . '>' . ucwords( str_replace( '-', ' ', $option ) ) . ' ' . $dimensions[0] . 'x' . $dimensions[1] . '</option>';
						}
					
						?>
					
					</select>				
			</fieldset>
			
			<fieldset>
				<label for="cTitle"><?php _e('Title', 'wpbootstrap' ) ?></label><br />
				<input type="text" name="cTitle" id="cTitle" class="required" value="<?php  echo $cTitle; ?>" tabindex="5" />
			</fieldset>			

			<fieldset>
				<label for="cTags"><?php _e( 'Tags (optional)', 'wpbootstrap' ) ?></label>
				<p><em>Separate tags with commas</em></p>
				<input type="text" name="cTags" id="cTags" value="<?php echo $cTags; ?>" tabindex="6" />
			</fieldset>
		
		</div>
		
		<div class="col-md-5">
				

			<fieldset>
					<label for="cDescription"><?php _e('Description', 'wpbootstrap') ?></label>
					<textarea name="cDescription" id="cDescription" rows="8" cols="30" class="required" tabindex="8"><?php echo stripslashes( $cDescription );?></textarea>
			</fieldset>

			
			<fieldset>
				<?php wp_nonce_field( 'comparator_form_make', 'comparator_form_make_submitted' ); ?>
	
				<input type="submit" class="pretty-button pretty-button-green" value="Make a Comparator" id="makeit" name="makeit" tabindex="15">
			</fieldset>
			
						
		</div> 
			
	</div>

 		
</form>
	<?php endif?>
			
	
<?php get_footer(); ?>