//New carousel write on jquery

// See the lenght of <a> elements innerOuter

// Is <div id="carousel"><a href="">Images</a></div>

(function( $ ){

	$.fn.CarouselImages = function(num_images_showed, button_left, button_right, num_move_elements, width_item, size_default, top_position) {
		
		// Colocate the images
		// Convert on absolute...
		
		//Chrome need define the width, cannot obtain from a or img..., :(
		
		if(num_move_elements==undefined)
		{
			
			var num_move_elements=1;
			
		}
		
		if(size_default==undefined)
		{
			
			var size_default=1;
			
		}
		
		if(top_position==undefined)
		{
			
			top_position=new Array();
			
		}
		
		var num_element=0;
		
		var ph_img_carousel=$(this).children('a');
		
		var c_images=ph_img_carousel.length;
		
		if(c_images<=num_images_showed)
		{
			
			$(button_left).hide();
			$(button_right).hide();

		}
		else
		{
			
			$(button_left).show();
			$(button_right).show();
			
		}
		
		ph_img_carousel.css('position', 'absolute');
		ph_img_carousel.css('top', 0);
		ph_img_carousel.css('display', 'block');
		
		var border_width_margin=0;
		
		var width_cont=$(this).width();
		var height_cont=$(this).height();
		
		var size_item_avaliable=Math.ceil(width_cont/num_images_showed);
		
		var overload_item=Math.floor( (size_item_avaliable-width_item)/4 );
		
		var sum_pos_x=size_item_avaliable+overload_item;
		
		var last_item_pos=sum_pos_x*(c_images-1);
		
		var pos_initial_x=0;
		
		var first_item=0;
		
		var last_item=c_images-1;
		
		var count_moves=0;
		
		if(ph_img_carousel.css('border-left-width')!=undefined)
		{
			border_width_margin=Number(ph_img_carousel.css('border-left-width').replace('px', ''))*2;
		}
		
		if(size_default==1)
		{
			ph_img_carousel.children('img').height(height_item-border_width_margin);
		}
		
		//Set initial position
		
		for(x=0;x<c_images;x++)
		{
			
			ph_img_carousel.eq(x).css('left', pos_initial_x+"px");
			
			pos_initial_x+=sum_pos_x;
			
			if(top_position[x]==undefined)
			{
				
				top_position[x]='0px';
				
			}
			
			ph_img_carousel.eq(x).children('img').css('top', top_position[x]+"px");
			
		}
		
		x=0;
		
		//Set initial effecto for showed
		
		//set_image_fadein();
		
		function set_image_fadein()
		{
			
			ph_img_carousel.eq(x).fadeIn('fast', function () {
			
				x++;
				
				if(x>c_images)
				{
					
					x=0;
					
				}
				
				if(x<c_images)
				{
					
					setTimeout(set_image_fadein, 0);
					
				}
				
			});
			
		}
		
		$(button_right).click(function(){
			
			//Put the last element in first place where you cannot see.
			
			count_moves=0;
			
			if (ph_img_carousel.is(":animated")) 
			{ 
				return false; 
			}
			//Move all 
			
			//Move prev first item.
			
			prev_first_item=first_item-1;
			
			if(prev_first_item<0)
			{
				
				//Move prev to last position
				
				prev_first_item=c_images-1;
				
			}
			
			ph_img_carousel.eq(prev_first_item).css({'left':last_item_pos});
			
			for(x=0;x<num_move_elements;x++)
			{ 
			
				ph_img_carousel.animate({"left": "-="+sum_pos_x+"px"}, {duration: 'fast', easing: 'linear', complete: function () {
						
						count_moves++;
						
						ph_img_carousel.eq(first_item).css({'left':last_item_pos});
						
						if(c_images==count_moves)
						{
							
							first_item++;
							
							if(first_item==c_images)
							{
								
								first_item=0;
								
							}
							
							
							last_item++;
							
							if(last_item==c_images)
							{
								
								last_item=0;
								
							}
													
							
							count_moves=0;
						}
						
					}
					
				});
				
			}
			
			return false;
			
		});
		
		$(button_left).click(function(){
			
			//Put the last element in first place where you cannot see.
			
			if (ph_img_carousel.is(":animated")) 
			{ 
				return false; 
			}
			
			count_moves=0;
			
			ph_img_carousel.eq(last_item).css('left', '-'+sum_pos_x+'px');
			
			//Move all 
			
			for(x=0;x<num_move_elements;x++)
			{ 
				ph_img_carousel.animate({"left": "+="+sum_pos_x+"px"}, {duration: 'fast', easing: 'linear', complete: function () {
						
						if(count_moves==0)
						{	
							
							count_moves=c_images;
							
							first_item--;
							
							if(first_item<0)
							{
								
								first_item=c_images-1;
								
							}
							
							last_item--;
							
							if(last_item<0)
							{
							
								last_item=c_images-1;
								
							}
							
							//alert(last_item);
							
						}
						
						ph_img_carousel.eq(last_item).css({'left':'-'+sum_pos_x+'px'});

						count_moves--;
						
						
					},
				
				});
				
			}
			
			return false;
			
		});
			
		
	}

})( jQuery );

