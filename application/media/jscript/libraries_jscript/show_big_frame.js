(function( $ ){

	$.fn.ShowBigFrame = function() {
		
		$(this).click(function () {
			
			//First function click for out of the image preview
			
			//obtain rel html input...
			
			html_id='#'+$(this).attr('rel');
			
			$('body').on('click', '#icon_close_frame', function () {
				
				$(html_id).hide();
				
				$(html_id).appendTo('#container_frame');
				
				$('#show_big_frame').remove();
				$('#center_frame').remove();
				
			/*	//Unbind click for body when is not necessary
				
				$('body').unbind('click');*/
			
				return false;
			
			});
			
			//Now generate html for generate the image
			
			$('body').prepend('<div id="show_big_frame"></div>');
			$('body').prepend('<div id="center_frame"></div>');
			$('#center_frame').prepend('<div id="frame_big"></div>');
			
			$('#center_frame').css({'top': $(document).scrollTop()+'px'});
			
			$('#show_big_frame').fadeTo(550, 0.7);
			$('#frame_big').fadeTo(550, 1);
			
			//Loading image
			
			$('#frame_big').append('<a id="icon_close_frame" href="#"></a>');
			
			
			$(html_id).appendTo('#frame_big');
			
			$(html_id).show();
			
			/*$('#frame_big_showed').load( function () {
			
				//Animate the frame_image
				
				width_css=$('#image_big_showed').css('width');
				height_css=$('#image_big_showed').css('height');
				
				$('#frame_image').animate({'width' : width_css, 'height': height_css}, function () {
				
					$('#image_big_showed').fadeIn('slow');
				
				});
				
			
			});*/
			
			return false;
		
		});
	
	}

})( jQuery );