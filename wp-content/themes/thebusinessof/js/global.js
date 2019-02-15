jQuery(document).ready(function($) {
	
	var nav = jQuery('#menu-main .menu .menu-item');
	
	nav.each(function(index) {    	
		jQuery(this).mouseover(function () {
	    	jQuery(this).addClass("active");
	  	});
		
		jQuery(this).mouseout(function () {
	    	jQuery(this).removeClass("active");
	  	});
		
  	});
	
	/*
	jQuery('#menu-main .menu li').hover(
	  function () {
	    jQuery(this).addClass("hover");
	  },
	  function () {
	    jQuery(this).removeClass("hover");
	  }
	  
	);*/
	
});

