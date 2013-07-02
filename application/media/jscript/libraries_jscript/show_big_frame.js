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
			
			//Use escape button for close window
			
			$(document).keyup(function(e) {

				if (e.keyCode == 27) 
				{ 
					
					$('#icon_close_frame').click();
					
				}   // esc
				
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
			
			return false;
		
		});
		
	}

})( jQuery );