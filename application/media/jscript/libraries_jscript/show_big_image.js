function show_big_image(image_showed)
{
	
	$(image_showed).click(function () {
		
			//First function click for out of the image preview
			
			$('body').click(function () {
				
				$('#show_big_images').remove();
				$('#center_frame_image').remove();
				
				//Unbind click for body when is not necessary
				
				$('body').unbind('click');
			
			});
			
			//Now generate html for generate the image
			
			$('body').prepend('<div id="show_big_images"></div>');
			$('body').prepend('<div id="center_frame_image"></div>');
			$('#center_frame_image').prepend('<div id="frame_image"></div>');
			
			$('#center_frame_image').css({'top': $(document).scrollTop()+'px'});
			
			$('#show_big_images').fadeTo(550, 0.7);
			$('#frame_image').fadeTo(550, 1);
			
			//Loading image
			
			$('#frame_image').append('<div id="icon_close_frame_image"></div>');
			$('#frame_image').append('<img id="image_big_showed" src="'+$(image_showed).attr('href')+'" style="display:none;" />');
			
			$('#image_big_showed').load( function () {
			
				//Animate the frame_image
				
				width_css=$('#image_big_showed').css('width');
				height_css=$('#image_big_showed').css('height');
				
				$('#frame_image').animate({'width' : width_css, 'height': height_css}, function () {
				
					$('#image_big_showed').fadeIn('slow');
				
				});
				
			
			});
			
			return false;
		
		});

	
}