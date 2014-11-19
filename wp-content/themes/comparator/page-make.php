<?php
/*
Template Name: Make a Comparator Form

Create a new comporator
*/

global $wp_query;

// enqueue jquery for this form
// add_action( 'wp_enqueue_scripts', 'comparator_enqueue_add_scripts' );

// set af default values
$feedback_msg = '';
$cDimensions = 'large-landscape';


// status for new submissions (make option later)
$my_new_status = 'publish';

// a little mojo to get current page ID so we can build a link back here
$post = $wp_query->post;
$current_ID = $post->ID;

// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['comparator_form_make_submitted'] ) && wp_verify_nonce( $_POST['comparator_form_make_submitted'], 'comparator_form_make' ) ) {
 
 		// grab the variables from the form
 		$cTitle = 					sanitize_text_field( $_POST['cTitle'] );		
 		$cTags = 					sanitize_text_field( $_POST['cTags'] );	
 		$cDescription = 			esc_textarea( trim($_POST['cDescription']) );
 		$cDimensions =				$_POST['cDimensions'];
 		
			
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		
 		if ( $cTitle == '' ) $errors[] = '<strong>Title Missing</strong> - please enter a descriptive title.'; 	
 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = '<div class="fade in alert alert-alert-error">Sorry, but there are a few errors in your entry. Please correct and try again.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul></div>';
 			
 		} else {
 			
 			// good enough, let's make a post! Or a custom post type
			$c_information = array(
				'post_title' => $cTitle,
				'post_content' => $cDescription,
				'tags_input'  => $cTags,
				'post_status' => $my_new_status,			
			);

			// insert as a post
			$post_id = wp_insert_post( $c_information );
			
			// check for success
			if ( $post_id ) {
				
				// update the new tags			
				wp_set_post_tags( $post_id, $cTags);
				
				
				$before_id = get_attachment_id_by_src($_POST['cbeforeImageUrl']);
				set_post_thumbnail( $post_id, $before_id);
				
				add_post_meta($post_id, 'before_img', $_POST['cbeforeImageUrl']);
				add_post_meta($post_id, 'after_img', $_POST['cafterImageUrl']);
				add_post_meta($post_id, 'compsize', $cDimensions);
				
				wp_set_post_terms( $post_id, 'splost');
				
				
				if  ( $my_new_status == 'publish' ) {
				
					// build feedback if new things are automatically published
					
					// grab link to new thing
					$cLink = get_permalink( $post_id );
				
					// feedback success
					$feedback_msg = '<div class="fade in alert alert-alert-info">Your new Comparator has been created. Check out <a href="' . get_permalink( $post_id ) . '">' . $cTitle . '</a> or you can <a href="' . get_permalink( $current_ID ) .'">create another one</a>.</div>';  
					
				} else {
					// feedback if new things are set to draft
					$feedback_msg = '<div class="fade in alert alert-alert-info">Your new Comparator, "' . $cTitle . '" has been created. Once it has been approved it will appear on this site. Do you want to <a href="' . get_permalink( $current_ID ) .'">create another one</a>?</div>';  
				
				}
 
			} else {
			
				// generic error of post creation failed
				$feedback_msg = '<div class="fade in alert alert-alert-error">ERROR: the new Comparator could not be created. We are not sure why, but let someone know.</div>';
			} // end if ($post_id)
					
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
							
							<?php 
								if ( !is_user_logged_in() ) {
								
								$text = "Before you start please <a href='" .  get_bloginfo('url') . "/wp-login.php?autologin=splot' class='btn btn-primary'>activate lasers</a>";
								
								$content = '[alert type="warning" close="false" text="' . $text . '"]';
								}
								
								echo do_shortcode( $content );		
							?>

							
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
				<?php echo $feedback_msg?>
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
    
			
<?php if (!$post_id and is_user_logged_in() ) : //hide form if we had success ?>
			
	<form  id="comparatorform" class="comparatorform" method="post" action="" enctype="multipart/form-data">
	
	
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
				<input type="text" name="cTags" id="cTags" value="<?php  echo $cTags; ?>" tabindex="6" />
			</fieldset>
		
		</div>
		
		<div class="col-md-5">
				

			<fieldset>
					<label for="cDescription"><?php _e('Description', 'wpbootstrap') ?></label>
					<textarea name="cDescription" id="cDescription" rows="8" cols="30" class="required" tabindex="8"><?php echo stripslashes( $cDescription );?></textarea>
			</fieldset>

			
			<fieldset>
				<?php wp_nonce_field( 'comparator_form_make', 'comparator_form_make_submitted' ); ?>
	
				<input type="submit" class="btn btn-primary" value="Make a Comparator" id="makeit" name="makeit" tabindex="15">
			</fieldset>
			
						
		</div> 
			
	</div>

 		
</form>
<?php endif?>
			
			
    
</div> <!-- end #formcontent -->
<?php get_footer(); ?>