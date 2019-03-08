jQuery(document).ready(function($){

	//Main menu
	$('#main-menu').smartmenus();
	
	//Mobile menu toggle
	$('.navbar-toggle').click(function(){
		$('.region-primary-menu').slideToggle();
	});

	//Mobile dropdown menu
	if ( $(window).width() < 767) {
		$(".region-primary-menu li a:not(.has-submenu)").click(function () {
			$('.region-primary-menu').hide();
	    });
	}

	//flexslider
	jQuery('.flexslider').flexslider({
    	animation: "slide"	
    });

    
    
});

function expand(node_value) {
   jQuery('article[data-history-node-id='+node_value+'] .node__content').css({"display":"block"});
   jQuery('article[data-history-node-id='+node_value+'] .up').css({"display":"block"});
   jQuery('article[data-history-node-id='+node_value+'] .down').css({"display":"none"});
}

function shrink(node_value) {
   jQuery('article[data-history-node-id='+node_value+'] .node__content').css({"display":"none"});
   jQuery('article[data-history-node-id='+node_value+'] .down').css({"display":"block"});
   jQuery('article[data-history-node-id='+node_value+'] .up').css({"display":"none"});
}

function closeNotification(node_value) {
	//alert(node_value);
   jQuery('article[data-history-node-id='+node_value+']').css({"display":"none"});
}