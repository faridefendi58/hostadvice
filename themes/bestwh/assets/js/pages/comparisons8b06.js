jQuery(function(e){function o(){var o=a.offset().top,t=e(window).scrollTop(),i=t+a.height(),s=e(".comparison-screen .criteria-item").last(),r=s.offset().top+s.height();return n.css("width",a.css("width")),t>=o&&r>i?(n.addClass("is-visible"),!1):void n.removeClass("is-visible")}CompaniesSearch.init({inputSelector:".comparison-company-search",selectFunction:function(e,o){jQuery(e.target).attr("data-slug",o.item.slug),jQuery(e.target).attr("data-id",o.item.post_id),jQuery(".comparison-company-search").removeClass("invalid-value")},autoSelect:!0,getRequestParams:function(){var e={exclude:[]};return jQuery(".comparison-company-search").each(function(o,n){var a=jQuery(n).attr("data-id");void 0!==a&&a.length&&e.exclude.push(a)}),e}}),e(document).on("input",".comparison-company-search",function(){e(this).attr("data-slug",""),e(this).attr("data-id","")}),e(document).on("click",".compare-btn",function(){var o=e("#comparison-popup").length?e("#cboxLoadedContent").find(".comparison-company-search"):e(".comparison-company-search"),n=!1,a=[],t=[];return jQuery(".comparison-company-search").removeClass("invalid-value"),e.each(o,function(o,i){var s=e(i).attr("data-slug");return-1!==a.indexOf(s)?(n=o,!1):void(""!==s&&"undefined"!=typeof s?a.push(s):t.push(o))}),t.length>1?(e(o[t[0]]).addClass("invalid-value"),!1):n!==!1?(e(o[n]).addClass("invalid-value"),!1):(window.location.assign(window.location.origin+"/tools/web-hosting-comparison/"+a.join("-vs-")+"/"),!1)}),e(document).on("click",".x-vs-y .review-summary .read-more",function(){var o=e(this);o.siblings(".review-content").addClass("full-size"),o.hide()});var n=e(".comparison-screen .header.floating"),a=e(".comparison-screen .header");a.length>0&&(o(),e(window).scroll(function(){o()}),e(window).resize(function(){o()})),e(document).on("click",".x-vs-y .remove-add-company",function(){Template.popup("#comparison-popup"),CompaniesSearch.init({inputSelector:".comparison-company-search",selectFunction:function(e,o){jQuery(e.target).attr("data-slug",o.item.slug),jQuery(e.target).attr("data-id",o.item.post_id),jQuery(".comparison-company-search").removeClass("invalid-value")},autoSelect:!0,getRequestParams:function(){var e={exclude:[]};return jQuery("#cboxLoadedContent").find(".comparison-company-search").each(function(o,n){var a=jQuery(n).attr("data-id");void 0!==a&&a.length&&e.exclude.push(a)}),e}})}),e(document).on("click",".show-more-plans-btn",function(o){o.preventDefault(),e(this).closest("div.hosting-plans").find("tr.hidden").removeClass("hidden"),e(this).closest("tr.show-more-plans-holder").addClass("hidden")}),e(document).on("click","#comparison_show_coupons",function(o){o.preventDefault(),e(".comparison-coupons-holder").addClass("expanded"),e(this).remove()})});