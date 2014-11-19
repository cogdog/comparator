<?php

// show wordpress menus and stuff? param show=1 passed to hide
$hide_wp =  ( isset( $wp_query->query_vars['show'] ) ) ? 1 : 0;

// get the display option size
$compsize = get_post_meta( get_the_ID(), 'compsize', $single = true); 

// no size assigned
if ( !$compsize) $compsize = 'small-landscape';
								
?>

<?php 
if ($hide_wp) {
	get_header('noshow'); 
} else {
	get_header(); 
}
?>	
			<div id="content" class="clearfix row">
			
				<div id="main" class="col col-lg-12 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
				
							<div class="page-header"><h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
					
					
						<section class="post_content clearfix" itemprop="articleBody">
						
						
						<div class="center-block" style="width: <?php echo $comp_size_options[$compsize][0]?>px">
							<!-- begin before/after placement -->
							<div id="container">
								<?php
								// get before and after images from post meta
								$before_url = get_post_meta( get_the_ID(), 'before_img', $single = true); 								
								$after_url = get_post_meta( get_the_ID(), 'after_img', $single = true); 
								
								// add the file size name mods
								// create the file name mods for the size chosen, the way wordpress
								// adds -{width}x{width}.jpg
								$file_size_name = '-' . $comp_size_options[$compsize][0] . 'x' . $comp_size_options[$compsize][1];
								$before_url = str_replace('.jpg', $file_size_name . '.jpg', $before_url);
								$after_url = str_replace('.jpg', $file_size_name . '.jpg', $after_url);
								?>
								
				 				<div><img alt="before" src="<?php echo $before_url?>" width="<?php echo $comp_size_options[$compsize][0]?>" height="<?php echo $comp_size_options[$compsize][1]?>" /></div>
				 				<div><img alt="after" src="<?php echo $after_url?>" width="<?php echo $comp_size_options[$compsize][0]?>" height="<?php echo $comp_size_options[$compsize][1]?>" /></div>
							</div>
							<!-- end before/after placement -->

						
							<?php the_content(); ?>
							
							<?php if (!$hide_wp):?>
							<p><strong>Sharable link:</strong> <a href="<?php the_permalink();?>?show=1" target="_blank"><?php the_permalink();?>?show=1</a><br />
							<strong>Embed Code:</strong> For now this will not work in Wordpress (getting jquery conflicts), but will work in stand along HTML. Click to select:</p>
							
							<form>
							<textarea style="width:100%; height: 3em;" onClick="this.select();"><iframe src="<?php echo site_url(); ?>/embed/?cid=<?php echo get_the_ID()?>" width="<?php echo ($comp_size_options[$compsize][0] + 20);?>" height="<?php echo ($comp_size_options[$compsize][1] + 40);?>"  style="border:none;"/></textarea>
							</form>
							
							<?php the_tags('<p class="tags"><span class="tags-title">' . __("Tags","wpbootstrap") . ':</span> ', ' ', '</p>'); ?>
							</div>
							<?php endif?>
						</section> <!-- end article section -->
						
						<footer>
			
							<?php 
							// only show edit button if user has permission to edit posts
							if( $user_level > 0 ) { 
							?>
							<a href="<?php echo get_edit_post_link(); ?>" class="btn btn-success edit-post"><i class="icon-pencil icon-white"></i> <?php _e("Edit post","wpbootstrap"); ?></a>
							<?php } ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					
					<?php endwhile; ?>			
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("Uh oh a comparator was not found!", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, but the comprator you seek seems to have gone missing. Insert sad face.", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
			</div> <!-- end #content -->

<?php 
if ($hide_wp) {
	get_footer('noshow'); 
} else {
	get_footer(); 
}
?>