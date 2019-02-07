var GAEvents={parseUrl:function(url){var parser=document.createElement('a');parser.href=url;return parser;},trackClick:function($obj){var href=$obj.attr('href');if(typeof href==='undefined'){href='';}
var action=$obj.data('ga-event');if(typeof action==='undefined'&&href!==null&&href.indexOf('#')!==0){action=this.getPageName(href);}
if(typeof action==='undefined'){return;}
action=this.getPrefix($obj)+action+this.getObjType($obj)+' – '+this.getPageName(window.location.href)+this.getSuffix($obj);var category=$obj.data('ga-category');if(typeof category==='undefined'){category=($obj.attr('target')!=null&&$obj.attr('target')=='_blank')?'click out':'click in';if(category==='click in'&&(href.indexOf('http:')===0||href.indexOf('https:')===0)){var hostname=this.parseUrl(href).hostname;if(hostname.indexOf('hostadvice.')===-1){category='click out';}}}
var label=$obj.data('ga-label');if(typeof label==='undefined'){label=$obj.attr('title');}
if(typeof label==='undefined'){label=$obj.text();}
this.sendEvent(category,action,label);},sendEvent:function(category,action,label){if(category==='click out'){dataLayer.push({'vendor':label,'event':'outbound'});}
ga('send',{'hitType':'event','eventCategory':category,'eventAction':action,'eventLabel':label});},getPageName:function(url){var name,subpage;var aUrl=url.split('/');if(url.indexOf('http:')===0||url.indexOf('https:')===0){var hostname=this.parseUrl(url).hostname;if(hostname.indexOf('hostadvice.')===-1){return '3rd party website';}
name=aUrl[3];subpage=typeof aUrl[4]!=='undefined'?aUrl[4]:'';}else{name=aUrl[1];subpage=typeof aUrl[2]!=='undefined'?aUrl[2]:'';}
if(subpage!==''){switch(name){case 'blog':if(subpage!=='tags'&&subpage!=='page'){name='blog-post';}
break;case 'go':if(subpage==='plan'){name='go-plan';}
break;case 'hosting-company':if(subpage.indexOf('-coupons')!==-1){name='hosting-coupons';}
break;case 'tools':name=subpage;break;case 'hosting-guides':name='hosting-guide';break;case 'how-to':if(subpage!=='category'&&subpage!=='tags'){name='how-to-post';}
break;}}
if(name===''){name='homepage';}
return name;},getPrefix:function($obj){var prefix='';if($obj.hasClass('page-numbers')){prefix='pagination: ';}else if($obj.parent().hasClass('menu-item')){prefix='menu: ';}else if($obj.closest('.location_info').length>0){prefix='breadcrumbs: ';}
return prefix;},getSuffix:function($obj){var suffix='';if($obj.closest('[data-popup-id]').length>0){suffix=' ('+$obj.closest('[data-popup-id]').data('popup-id')+')';}else if($obj.closest('#colorbox').length>0){suffix=' (popup)';}else if($obj.closest('aside').length>0){suffix=' (sidebar)';}
return suffix;},getObjType:function($obj){var type='';if($obj.hasClass('button')){type=' (button)';}else{var $img=$obj.find('img:first');if($img.length>0){if($img.attr('src').indexOf('avatar')!==-1){type=' (avatar)';}else{type=' (image)';}}}
return type;}}
jQuery(function($){setTimeout(function(){$(document).on('click','a,div[data-ga-event]',function(){GAEvents.trackClick($(this));});},200);});