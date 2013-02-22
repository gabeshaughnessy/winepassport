jQuery(document).ready(function($){
	$('.dropdown-menu').prev('a').dropdown();
	$('.dropdown-menu').prev('a').click(function(e){
		$('.navbar li').removeClass('open');
		$(this).parent('li').addClass('open');
		e.preventDefault();
	});
	
	if ($.browser.msie) {
	//dont do anything for explorer
	   }
	   else{
		var imgSrc = jQuery('#bottom').find('img').attr('src');
		jQuery('#bottom').find('.texture').css({'width':'100%', 'height':'100%', 'background-image': 'url('+templateDir+'/images/paper_bg.png), url(' +imgSrc+')'});
		jQuery('#bottom').find('img').css({'opacity':0});
	}
	
	jQuery('.flexslider').flexslider({
	  animation: "slide",
	  selector: ".slides > div",
	  controlNav: false,
	  directionNav: false
	});
	
});


jQuery(window).load(function() {



 });