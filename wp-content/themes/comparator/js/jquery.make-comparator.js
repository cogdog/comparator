/* Comparator: Maker Page Scripts
   code by Alan Levine @cogdog http://cogdog.info
   
   media uploader scripts somewhat lifted from
   http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
  
*/

jQuery(document).ready(function() { 
	// called for via click of upload button in theme options

	// available sizes, sadly hard coded for now
	var cdims = 
			{
				'small-square' : [300, 300],
				'big-square' : [600, 600],
				'small-landscape' : [400, 300],
				'small-portrait' : [ 300, 400],
				'large-landscape' : [800, 600],
				'large-portrait' : [600, 800 ],
				'jumbo-landscape' : [1000, 750],
			};
	
	jQuery( "#cpreview" ).css( "width", cdims['large-landscape'][0]);

	jQuery(document).on('click', '.upload_image_button', function(e){

		// disable default behavior
		e.preventDefault();

		// Create the media frame
		// use title and label passed from data-items in form button
	
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false  // Set to true to allow multiple files to be selected
		});

		// fetch the type for this option so we can use it, comes from data-ctype value 
		// in form button
	
		ctype = jQuery( this ).data( 'ctype' );
		
		csize = jQuery('#cDimensions').val();
		
		// set up call back from image selection from media uploader
		file_frame.on( 'select', function() {
	
		  // attachment object from upload
		  attachment = file_frame.state().get('selection').first().toJSON();
    		  
		  // insert the base url into the hidden field for the option value
		  jQuery("#c"+ctype+"ImageUrl").val(attachment.url);  
		  
		  // update the thumbnail preview
		  jQuery("#"+ctype+"thumb").attr("src", attachment.sizes.thumbnail.url);  
		  
		  // update the src of the preview image so you can see it
		  jQuery('img[alt="'+ctype+'"]').attr("src", attachment.url);  
		});

		// Finally, open the modal
		file_frame.open();
	
	});
	
	jQuery('#cDimensions').change(function() {
		
		// get the value for size selection
		var newdim = jQuery(this).val();
		
		// get the proper file name for the selected size 
		var imgsizesuffix = '-' + cdims[newdim][0] + 'x' + cdims[newdim][1] + '.jpg';
		
		// reset the width of the div containing the comparator
		jQuery( "#cpreview" ).css( "width", cdims[newdim][0]);	
		
		// get the base file name for the before image
		var bimgurl = jQuery("#cbeforeImageUrl").val();
		var aimgurl = jQuery("#cafterImageUrl").val();
		
		var newhtml = '<div><img alt="before" src="' + bimgurl.substr(0, bimgurl.length-4) + imgsizesuffix + '" width="' + cdims[newdim][0] + '" height="' + cdims[newdim][1] + '"600" /></div><div><img alt="after" src="' + aimgurl.substr(0, aimgurl.length-4) + imgsizesuffix + '" width="' + cdims[newdim][0] + '" height="' + cdims[newdim][1] + '"600" /></div>';
		
		// remove the text links
		jQuery('.balinks').remove();
		
		// update the div that holds the images
		jQuery('#container').html(newhtml);
		
		// retrigger the BeforeAfter pluing
		jQuery('#container').beforeAfter();
    });

});
