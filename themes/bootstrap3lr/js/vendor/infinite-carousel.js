/**
 * @author Stéphane Roucheray 
 * @extends jquery
 */

		


jQuery.fn.carousel = function(time){
	var sliderList = jQuery(this).children()[0];
	
	
	if (sliderList) {
		var increment = jQuery(sliderList).children().outerWidth(true),
		elmnts = jQuery(sliderList).children(),
		numElmts = elmnts.length,
		sizeFirstElmnt = increment,
		shownInViewport = Math.round(jQuery(this).width() / sizeFirstElmnt),
		firstElementOnViewPort = 1,
		isAnimating = false;
		
		for (i = 0; i < shownInViewport; i++) {
			jQuery(sliderList).css('width',(numElmts+shownInViewport)*increment + increment + "px");
			jQuery(sliderList).css('position','relative')
			jQuery(sliderList).append(jQuery(elmnts[i]).clone());
		}
		
		
		
		
		function slide(){
			if (!isAnimating) {
				if (firstElementOnViewPort > numElmts) {
					firstElementOnViewPort = 2;
					jQuery(sliderList).css('left', "0px");
				}
				else {
					firstElementOnViewPort++;
				}
				jQuery(sliderList).animate({
					left: "-=" + increment,
					y: 0,
					queue: true
				}, "swing", function(){isAnimating = false;});
				isAnimating = true;
			}
		};
		
		var intervalId = window.setInterval(slide, parseInt(time));		
		

	}
};
