jQuery(document).ready(function(){
	var can=jQuery("div[id^=myCanvas]");
	jQuery.each(can,function(i,v){	
		
		var cancontainer=jQuery(v).attr("id");
		var canvasdiv=jQuery(v).find("canvas");
		var can_id=canvasdiv.attr("id");
		var tag_id=canvasdiv.data("id");
		
		if(!jQuery("#"+can_id).tagcanvas({
			textColour: "#00f",
			outlineColour: "#19365E",
			reverse: true,
			depth: 0.8,
			maxSpeed: 0.05,
			textFont: 'Impact,"Arial Black",sans-serif',
			weight: true,
		},"tags"+tag_id)) {
			// something went wrong, hide the canvas container
			jQuery("#"+cancontainer).hide();
		}
	});
	
});
