jQuery(document).ready(function ($) {
 
    var sliderObj=jQuery("div[id^=slider]");
	jQuery.each(sliderObj,function(i,v){
		setInterval(function () { moveRight(); }, 5000);
		var slide_id=jQuery(v).attr("id");
		var next=jQuery(v).find("a.control_next").attr('id');
		var prev=jQuery(v).find("a.control_prev").attr('id');
		var slide_id=jQuery(v).attr("id");
		var slideCount = $('#'+slide_id+' ul li').length;
		var slideWidth = $('#'+slide_id+' ul li').width();
		var slideHeight =$('#'+slide_id+' ul li').height();
		var sliderUlWidth = slideCount * slideWidth;
		
		$('#'+slide_id).css({ width: slideWidth, height: slideHeight + 90 });
		
		$('#'+slide_id+' ul').css({ width: sliderUlWidth });
		
	    $('#'+slide_id+' ul li:last-child').prependTo('#'+slide_id+' ul');

	    $('a#'+prev).click(function (event) {
	    	event.preventDefault();
	        moveLeft();
	    });

	    $('a#'+next).click(function (event) {
	    	event.preventDefault();
	        moveRight();
	    });
	    
	    function moveLeft() {
	        $('#'+slide_id+' ul').animate({
	            left: + slideWidth
	        }, 800, function () {
	            $('#'+slide_id+' ul li:last-child').prependTo('#'+slide_id+' ul');
	            $('#'+slide_id+' ul').css('left', '');
	        });
	    };
	    
	    function moveRight() {
	        $('#'+slide_id+' ul').animate({
	            left: - slideWidth
	        }, 800, function () {
	            $('#'+slide_id+' ul li:first-child').appendTo('#'+slide_id+' ul');
	            $('#'+slide_id+' ul').css('left', '');
	        });
	    };

	});
}); 