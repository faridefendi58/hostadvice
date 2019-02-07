jQuery(function($){var latestReviewsSliderTimer=setInterval(function(){var $slider=$('.latest-user-reviews');var index=$slider.data('index')||0;showLatestReviewsSlide(index+1,$slider);},8000);function showLatestReviewsSlide(index,$slider){if(typeof($slider)=='undefined'){$slider=$('.latest-user-reviews');}
if(typeof(index)=='undefined'){index=$slider.data('index')||0;}
var total=$slider.find('.review').length;if(index<0){index=total-1;}
if(index>=total){index=0;}
$slider.data('index',index);$slider.find('.holder').css(Page.isRTL()?'marginRight':'marginLeft',-index*$slider.find('.review:first').width());}
function adjustLatestReviewsSlider(){var $slider=$('.latest-user-reviews');var w=$slider.find('.viewport').width();$slider.find('.review').width(w);showLatestReviewsSlide();}
adjustLatestReviewsSlider();$(window).resize(adjustLatestReviewsSlider);$(document).on('click','.latest-user-reviews .ctrls .next, .latest-user-reviews .ctrls .prev',function(e){e.preventDefault();clearInterval(latestReviewsSliderTimer);var $slider=$(this).closest('.latest-user-reviews');var index=$slider.data('index')||0;showLatestReviewsSlide($(this).hasClass('prev')?index-1:index+1,$slider);});});