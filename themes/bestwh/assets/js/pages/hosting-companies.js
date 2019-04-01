jQuery(function(e) {
    function t(e) {
        var t = jQuery.extend([], h);
        t.push({
            name: "sort",
            value: d.find('[name="sort"]').val()
        }), t.push({
            name: "order",
            value: d.find('[name="order"]').val()
        }), t.push({
            name: "action",
            value: "hp_getHostingCompanies"
        }), e ? t.push({
            name: "offset",
            value: e ? c.find("tr").length : 0
        }) : l = !1, c.css("opacity", "0.5")
    }

    function i() {
        e(".user-reviews-holder.active article.active").each(function() {
            var t = e(this).outerHeight(!0) + 62;
            e(this).parent().css("height", t)
        })
    }

    function a() {
        C = !0;
        var t = {
            action: "load-hosting-companies"
        };
        t._page = b, t._ajax = !0, e("[data-filter]").each(function() {
            null != e(this).data("filter-value") && null != e(this).data("filter-value") && ("undefined" == typeof t.filter && (t.filter = {}), t.filter[e(this).data("filter")] = e(this).data("filter-value"))
        }), 0 === e("#infinite-loader").length && Page.startLoad("hosting-companies-load", u), e.get(location.href, t, function(t) {
            var i = e(t).html();
            if (1 >= b) u.html(i), e("body").hide().show(0);
            else {
                var a = e("<div>" + i + "</div>");
                a.find(".actions-bar").remove(), e("#load-more-companies, #infinite-loader").remove(), u.append(a)
            }
            1 >= b && Page.scrollTo("#hosting-companies-holder", 400), e("#hosting_services_top_part_banner").html(f), e("#hosting_services_bottom_part_banner").html(p), Page.finishLoad("hosting-companies-load"), setTimeout(function() {
                e(window).trigger("scroll")
            }, 1), setTimeout(function() {
                C = !1
            }, 250)
        }, "html")
    }

    function n(t) {
        C = !0, e.get(t, {}, function(i) {
            var a = e(i).filter("title").html();
            i = e(i).find("#hosting-companies-template").html(), e("#hosting-companies-template").html(i), e("#hosting_services_top_part_banner").html(f), e("#hosting_services_bottom_part_banner").html(p), e("title").html(a), o(), e(".quick-filters").length > 0 ? Page.scrollTo(".quick-filters", 400) : Page.scrollTo("#hosting-companies-holder", 400), Page.finishLoad("ctrl-hosting-companies-load"), companiesSearchInit(), s(), k.init(), Lazyload.init(), setTimeout(function() {
                e(window).trigger("scroll")
            }, 1), setTimeout(function() {
                C = !1
            }, 250), "undefined" != typeof window.CheckedComparisonPopup && CheckedComparisonPopup.refreshCheckboxes(!0), x();
            var r = e("#canonical_category_url").val();
            "undefined" != typeof r && r.length && r !== t && Modernizr.history && window.history.pushState(null, e("title").html(), r), -1 !== t.indexOf("server") && (e(".hosting-filter-server_location").addClass("selected"), e(document).on("click", "#clean_country_filter", function() {
                e(".hosting-filter-server_location").removeClass("selected"), n(currentPageLink), window.history.pushState(null, e("title").html(), currentPageLink)
            }))
        }, "html")
    }

    function s() {
        var t = "",
            i = "";
        return e("#filter-servers-country").length ? (e("#filter-servers-country").focus(function() {
            t = e(this).val(), e(this).val("")
        }).blur(function() {
            e(this).val(t)
        }).keyup(function(t) {
            13 == t.keyCode && "" === e(this).val() && (n(serverLocationEmptyLink), Modernizr.history && window.history.pushState(null, e("title").html(), serverLocationEmptyLink))
        }), void(e("#filter-servers-country").autocomplete({
            source: function(e) {
                e.action = "servers_location_filter", e.clean_filters = "undefined" != typeof cleanFilters ? cleanFilters : [], e.current_page = "undefined" != typeof currentPageLink ? currentPageLink : window.location.pathname.replace(/filters--(.+)/, "")
            },
            focus: function(t, i) {
                return e("#filter-servers-country").val(i.item.title), !1
            },
            select: function(t, i) {
                return e("#filter-servers-country").val(i.item.title), "undefined" != typeof i.item.link && (Modernizr.history && window.history.pushState(null, e("title").html(), i.item.link), n(i.item.link)), !1
            }
        }).data("ui-autocomplete")._renderItem = function(t, a) {
            return t.addClass("sidebar slf-autocomplete"), i != a.type && (i.length ? (i = a.type, t.find("li").last().addClass("no-border"), e("<li></li>").appendTo(t)) : i = a.type), e("<li></li>").append('<a href="javascript:void(0);">' + a.label + "</a>").data("ui-autocomplete-item", a).appendTo(t)
        })) : !1
    }

    function o() {
        e("[data-visits-tooltip]").each(function() {
            var t = new Date,
                i = Math.round((t.getMinutes() + 60 * t.getHours()) / 5),
                a = e(this).data("tooltip");
            a = a.replace("[visits]", i), e(this).attr("data-tooltip", a)
        })
    }
    var r = e("#hosting-companies-template").hasClass("is-ppc"),
        l = !1,
        c = e("#hosting-companies-table tbody"),
        d = e(".hosting-filtering-form"),
        h = d.serializeArray(),
        u = e("#hosting-companies-holder"),
        f = "",
        p = "",
        v = e("#container"),
        m = e("#secondary");
    if (m.length > 0) {
        var g = e(".ppc-categories").length > 0 ? m.find(".ppc-categories") : m.find(".sticky-content"),
            y = m.outerHeight() - g.outerHeight(!0);
        e("#secondary .toggle-show-more, #secondary .toggle-show-less").on("click", function(e) {
            e.preventDefault(), setTimeout(function() {
                y = m.outerHeight() - g.outerHeight(!0)
            }, 100)
        });
        var w = 0;
        e(window).scroll(function() {
            var t = e(".sticky-filters");
            if (t.length > 0) {
                var i = e("#secondary").get(0).getBoundingClientRect().bottom; - 10 > i ? t.hasClass("fixed-on-top") || (e(".quick-filters-padding").height(t.outerHeight()), t.addClass("fixed-on-top")) : (e(".quick-filters-padding").height(0), t.removeClass("fixed-on-top"))
            } else {
                if (g = e(e(".ppc-categories").length > 0 ? "#secondary .ppc-categories" : "#secondary .sticky-content"), r && !Device.isBigScreen() || !r && Device.isTablet()) return g.css({
                    position: "static"
                }), !1;
                var a = v.get(0).getBoundingClientRect().top,
                    n = Math.abs(a) - y,
                    s = g.outerHeight(),
                    o = n - w;
                w = n;
                var l = "static",
                    c = 0,
                    d = "inherit",
                    h = Math.abs(m.outerHeight(!0) - v.outerHeight(!0)) < 10;
                if (!h && n >= 0 && 0 > a) {
                    l = "fixed";
                    var u = Math.min(0, e(window).outerHeight() - s);
                    c = g.css("top").replace("auto", "0").replace("px", "") - o, c = Math.min(0, Math.max(c, u)), v.outerHeight() - Math.abs(a) < s && (l = "absolute", d = 0, c = "inherit")
                }
                g.css({
                    position: l,
                    top: c,
                    bottom: d,
                    width: g.parent().width()
                })
            }
        }).scroll(), e(window).on("resize", function() {
            e(".sticky-filters").length || g.css({
                width: g.parent().width()
            })
        })
    }
    c.closest("table").find(".sortable-col").click(function(i) {
        i.stopPropagation = !0, i.preventDefault = !0;
        var a = e(this),
            n = "",
            s = "asc";
        return a.closest("table").find(".sortable-col").not(a).removeClass("sorted").removeClass("inverse"), e(".sorted-by-container").text(tr("Sorted by " + a.find(".sorted-by-hidden").text())), a.hasClass("sorted") && !a.hasClass("inverse") ? (a.addClass("inverse"), s = "desc") : a.removeClass("inverse"), a.addClass("sorted"), n = a.data("column"), d.find('[name="sort"]').val(n), d.find('[name="order"]').val(s), t(), !1
    }), e(".hosting-filtering-form").animate({
        opacity: 1
    }, 300), e(document).on("click", ".hosting-filter-name", function(t) {
        t.preventDefault(), t.stopPropagation();
        var i = e(this).parent().find(".hosting-filter-values");
        i.is(":hidden") ? i.slideDown(250).addClass("opened") : i.slideUp(250).removeClass("opened")
    }), e(document).on("click", ".hosting-filter-values a, .hosting-filter .dropdown-menu a, .hosting-filter-btn", function(t) {
        t.preventDefault(), t.stopPropagation();
        var i = e("#hosting-companies-template").data("template-type");
        if (void 0 !== i && "loadmore" === i) {
            var s = e(this).attr("href").substr(1),
                o = e(this).text();
            e(this).closest("[data-filter]").data("filter-value", s).find(".hosting-filter-value").text(o), e(this).closest("[data-filter]").find(".hosting-filter-name").click(), b = 1, a()
        } else {
            var s = e(this).data("value"),
                r = e(this).attr("href");
            Modernizr.history && window.history.pushState(null, e("title").html(), r), e(this).closest(".btn-group").length > 0 ? Page.startLoad("ctrl-hosting-companies-load", e(this).closest(".btn-group").find(".btn")) : e(this).closest("#secondary").length > 0 ? Page.startLoad("ctrl-hosting-companies-load", e(this).closest(".hosting-filter")) : e(this).hasClass("hosting-filter-btn") && Page.startLoad("ctrl-hosting-companies-load", e(this)), n(r)
        }
    }), e(document).on("click", ".load-latest-reviews", function(t) {
        t.preventDefault();
        var i = e(this),
            a = i.data("hosting-id"),
            n = e("#user-reviews-holder-" + a),
            s = n.find(".user-reviews-block");
        return n.hasClass("active") ? (s.css("height", 0), s.html(""), n.removeClass("active"), void i.removeClass("active")) : (e(".user-reviews-holder.active").each(function() {
            e(this).parent().data("hosting-id") !== a && (e(this).removeClass("active"), e(this).find(".user-reviews-block").html(""), e(this).find(".user-reviews-block").css("height", 0))
        }), void Page.startLoad("load-reviews-" + a, i))
    }), e(document).on("click", ".ctrl-prev, .ctrl-next", function(t) {
        t.preventDefault();
        var a = e(this).parent().find(".user-reviews-block"),
            n = a.data("index") || 0,
            s = a.find("article").length;
        e(this).hasClass("ctrl-prev") ? n-- : n++, 0 > n ? n = s - 1 : n >= s && (n = 0), a.data("index", n), a.find("article.active").removeClass("active"), a.find("article:eq(" + n + ")").addClass("active"), i()
    }), e(document).on("click", ".toggle-show-more, .toggle-show-less", function() {
        i()
    }), e(document).on("click", ".user-reviews-slider-filter a", function(t) {
        t.preventDefault();
        var i = e(this).closest("tr").data("hosting-id"),
            a = e("#user-reviews-holder-" + i),
            n = a.find(".user-reviews-block");
        e(this).attr("href").substr(1);
        a.find(".user-reviews-slider-filter a.active").removeClass("active"), e(this).addClass("active"), Page.startLoad("load-reviews-" + i, n)
    }), e(document).on("click", ".score-circle", function() {
        e(this).closest("tr").next().find('.load-latest-reviews:not(".active")').click()
    });
    var b = 1,
        C = !1;
    e("#hosting-companies-template").on("click", "#load-more-companies", function(e) {
        e.preventDefault(), u.find(".ajax-loader:not(:last)").remove(), b++, a()
    }), e("body").on("#show-all-companies", "click", function(t) {
        t.preventDefault(), e(".hosting-company-holder.hidden").removeClass("hidden"), e(this).remove()
    }), e(document).on("click", "#companies-pagination #companies-next-page, #companies-pagination a.page-numbers", function(t) {
        t.preventDefault();
        var i = e(this).attr("href");
        Modernizr.history && window.history.pushState(null, e("title").html(), i), Page.startLoad("ctrl-hosting-companies-load", e(this)), n(i)
    }), window.onpopstate = function(e) {
        "undefined" != typeof e.target && "undefined" != typeof e.target.location && 0 == e.target.location.pathname.indexOf("/hosting-companies") && n(e.target.location.href)
    }, e(document).on("change", ".stars_holder input", function() {
        var t = e(this).closest(".hosting-company-holder").find(".reviews-count"),
            i = e(this).closest(".hosting-company-holder").find(".score-circle div");
        t.hasClass("already-voted") || (e(this).data("initial-reviews-count", t.text()), e(this).data("initial-reviews-score", i.text()), t.text(1 + +t.text()));
        var a = +e(this).val(),
            n = +e(this).data("initial-reviews-count"),
            s = +e(this).data("initial-reviews-score"),
            o = Math.round((n * s + a) / (n + 1) * 10) / 10;
        i.text(o), t.addClass("already-voted")
    }), e(document).on("click", ".editor-take-more", function(t) {
        t.preventDefault();
        var i = e(this).closest("p");
        e(this).hasClass("less") ? (i.css({
            "max-height": "135px",
            "padding-bottom": "0px"
        }), e(this).find("span").html(tr("Read More") + ' <i class="fa fa-chevron-down"></i>'), e(this).removeClass("less")) : (i.css({
            "max-height": "1000px",
            "padding-bottom": "40px"
        }), e(this).find("span").html(tr("Show Less") + ' <i class="fa fa-chevron-up"></i>'), e(this).addClass("less"))
    }), e(document).on("click", ".hosting-coupon-ctrl", function(t) {
        t.preventDefault(), Template.popup(e(this).attr("href"))
    });
    var k = {
            sliderEl: null,
            fromPrice: null,
            toPrice: null,
            maxPrice: null,
            filterLink: null,
            disabled: !0,
            maxStepsCount: 25,
            stepsCount: 0,
            values: [],
            getValueByStep: function(e) {
                var t = this;
                return "undefined" != typeof t.values[e] ? t.values[e] : 0
            },
            getStepByValue: function(e) {
                for (var t = this, i = 0; i < t.values.length; i++)
                    if (t.values[i] >= e) return i;
                return 0
            },
            setValues: function() {
                for (var e = this, t = 0; t <= e.maxStepsCount; t++) {
                    var i = e.getStepValue(t);
                    if (i > e.maxPrice) return e.values[e.values.length - 1] < e.maxPrice && e.values.push(Math.round(e.maxPrice)), !1;
                    e.values.push(i)
                }
            },
            getStepValue: function(e) {
                var t = this;
                return 10 >= e ? e : 13 >= e ? Math.round(5 * (e - 8)) : 16 >= e ? Math.round(25 * (e - 12)) : 17 >= e ? Math.round(100 * (e - 15)) : 19 >= e ? Math.round(500 * (e - 16)) : Math.round(t.maxPrice / (t.maxStepsCount + 1 - e))
            },
            initSlider: function() {
                var t = this;
                t.sliderEl.slider({
                    range: !0,
                    disabled: t.disabled,
                    step: 1,
                    min: 0,
                    max: t.stepsCount,
                    values: [t.getStepByValue(t.fromPrice), t.getStepByValue(t.toPrice)],
                    slide: function(i, a) {
                        0 == a.values[0] && a.values[1] == t.stepsCount ? e(".hosting-filter-price .hosting-filter-value").html(tr("Any")) : e(".hosting-filter-price .hosting-filter-value").html("$" + t.getValueByStep(a.values[0]) + " - $" + t.getValueByStep(a.values[1]))
                    },
                    stop: function(e, i) {
                        if (0 == i.values[0] && i.values[1] == t.stepsCount) var a = t.filterLink.attr("data-clean-href");
                        else var a = t.filterLink.attr("data-href").replace("{from_price}-{to_price}", t.getValueByStep(i.values[0]) + "-" + t.getValueByStep(i.values[1]));
                        t.filterLink.attr("href", a), t.filterLink.trigger("click")
                    }
                })
            },
            init: function() {
                var t = this;
                t.sliderEl = e("#price-range-filter"), t.maxPrice = Math.round(t.sliderEl.attr("data-max-price")), t.fromPrice = parseInt(t.sliderEl.attr("data-from-price")), t.toPrice = parseInt(t.sliderEl.attr("data-to-price")), t.toPrice > t.maxPrice && (t.toPrice = t.maxPrice), t.filterLink = t.sliderEl.parent(".price-slider-holder").find("#price-filter-link"), t.disabled = "undefined" == typeof t.maxPrice || isNaN(t.maxPrice), t.values = [], t.setValues(), t.stepsCount = t.values.length ? t.values.length - 1 : 0, t.initSlider()
            }
        },
        x = function() {
            e(".latest-reviews").each(function() {
                var t = e(this);
                t.find(".reviews-slider").slick({
                    infinite: !0,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: !0,
                    accessibility: !1,
                    autoplaySpeed: 4e3,
                    pauseOnHover: !0,
                    arrows: !0,
                    prevArrow: '<a href="#" class="ctrl-prev"><i class="fa fa-chevron-left"></i></a>',
                    nextArrow: '<a href="#" class="ctrl-next"><i class="fa fa-chevron-right"></i></a>',
                    draggable: !1,
                    speed: 600,
                    rtl: Page.isRTL(),
                    swipeToSlide: !0
                })
            })
        };
    e(document).on("click", ".hosting-company-holder header a", function(t) {
        t.preventDefault();
        var i = e(this),
            a = i.closest("header"),
            n = i.closest(".hosting-company-holder"),
            s = i.data("target");
        if (i.hasClass("active")) {
            if ("main" !== s) return;
            window.location.href = i.attr("href")
        }
        a.find("a").removeClass("active"), i.addClass("active"), n.find(".tab-content").removeClass("active"), n.find('[data-tab="' + s + '"]').addClass("active"), n.find(".slick-slider").length > 0 && n.find(".slick-slider").slick("refresh")
    }), e(document).on("click", ".mobile_tab_holder a", function(t) {
        t.preventDefault();
        var i = e(this),
            a = i.parent(".mobile_tab_holder"),
            n = i.closest(".company_info"),
            s = i.data("target");
        a.find("a").removeClass("active"), i.addClass("active"), n.find(".mobile-tab-content").removeClass("active"), n.find('[data-tab="' + s + '"]').addClass("active")
    }), x(), s(), k.init(), o(), setTimeout(function() {
        e("[data-visits-tooltip]").mouseover()
    }, 3e3), setTimeout(function() {
        e("[data-visits-tooltip]").mouseout()
    }, 7e3), e(document).on("click", ".read-overview-link", function(t) {
        t.preventDefault(), e(this).parent().find(".hosting-company-overview").addClass("expanded"), e(this).remove()
    });
    var _ = 130;
    e("#mobile_filter").length > 0 && (_ = e(".entry-box").get(0).getBoundingClientRect().bottom, e(document).on("click", "#mobile_filter", function() {
        if (0 === e(".mobile_filter_plans").length) {
            var t = e(".filter_plans").html();
            e("#mobile_filter").append('<div class="container_div hosting-categories mobile_filter_plans text-left">' + t + "</div>"), k.init()
        } else e(".mobile_filter_plans").remove()
    }), e(window).bind("scroll", function() {
        e(window).scrollTop() > _ ? e("#mobile_filter").addClass("fixed") : e("#mobile_filter").removeClass("fixed")
    }))
});