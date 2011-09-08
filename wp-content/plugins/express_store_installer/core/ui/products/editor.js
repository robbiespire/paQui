/*
 editor scripts
 **/
var Pricelines = new Pricelines(),
	productOptions = new Array(),
	productAddons = new Array(),
	optionMenus = new Array(),
	addonGroups = new Array(),
	addonOptionsGroup = new Array(),
	selectedMenuOption = false,
	detailsidx = 1,
	variationsidx = 1,
	addon_group_idx = 1,
	addonsidx = 1,
	optionsidx = 1,
	pricingidx = 1,
	fileUploader = false,
	changes = false,
	saving = false,
	flashUploader = false,
	template = false,
	fileUploads = false;
jQuery(document).ready(function () {
	var b = jqnc(),
		d = b("#title"),
		c = b("#title-prompt-text"),
		a = b(".publishdate");
	d.bind("focus keydown", function () {
		c.hide()
	}).blur(function () {
		if (d.val() == "") {
			c.show()
		}
		else {
			c.hide()
		}
	});
	if (!product) {
		d.focus();
		c.show()
	}
	postboxes.add_postbox_toggles("ecart_page_ecart-products");
	b(".if-js-closed").removeClass("if-js-closed").addClass("closed");
	b(".postbox a.help").click(function () {
		b(this).colorbox({
			iframe: true,
			open: true,
			innerWidth: 768,
			innerHeight: 480,
			scrolling: false
		});
		return false
	});
	b("#publish-calendar").PopupCalendar({
		m_input: b("#publish-month"),
		d_input: b("#publish-date"),
		y_input: b("#publish-year"),
		autoinit: true,
		title: calendarTitle,
		startWeek: startWeekday
	});
	b("#schedule-toggle").click(function () {
		b("#scheduling").slideToggle("fast", function () {
			if (b(this).is(":visible")) {
				a.removeAttr("disabled")
			}
			else {
				a.attr("disabled", true)
			}
		})
	});
	b("#scheduling").hide();
	a.attr("disabled", true);
	b("#published").change(function () {
		if (b(this).attr("checked")) {
			b("#publish-status,#schedule-toggling").show()
		}
		else {
			b("#publish-status,#schedule-toggling,#scheduling").hide()
		}
	}).change();
	editslug = new SlugEditor(product, "product");
	if (specs) {
		b.each(specs, function () {
			addDetail(this)
		})
	}
	b("#addDetail").click(function () {
		addDetail()
	});
	fileUploads = new FileUploader("flash-upload-file", b("#ajax-upload-file"));
	basePrice = b(prices).get(0);
	if (basePrice && basePrice.context == "product") {
		Pricelines.add(false, basePrice, "#product-pricing")
	}
	else {
		Pricelines.add(false, false, "#product-pricing")
	}
	b("#variations-setting").bind("toggleui", variationsToggle).click(function () {
		b(this).trigger("toggleui")
	}).trigger("toggleui");
	loadVariations((!options.v && !options.a) ? options : options.v, prices);
	b("#addVariationMenu").click(function () {
		addVariationOptionsMenu()
	});
	b("#linkOptionVariations").click(linkVariationsButton).change(linkVariationsButtonLabel);
	b("#addons-setting").bind("toggleui", addonsToggle).click(function () {
		b(this).trigger("toggleui")
	}).trigger("toggleui");
	b("#newAddonGroup").click(function () {
		newAddonGroup()
	});
	if (options.a) {
		loadAddons(options.a, prices)
	}
	imageUploads = new ImageUploads(b("#image-product-id").val(), "product");
	categories();
	tags();
	quickSelects();
	updateWorkflow();
	window.onbeforeunload = unsavedChanges;
	b("#product").change(function () {
		changes = true
	}).unbind("submit").submit(function (h) {
		h.stopPropagation();
		var f = b("#product").attr("action").split("?"),
			g = f[0] + "?" + b.param(request);
		b("#product")[0].setAttribute("action", g);
		saving = true;
		return true
	});
	b("#prices-loading").remove()
});

function updateWorkflow() {
	var a = jqnc();
	a("#workflow").change(function () {
		setting = a(this).val();
		request.page = adminpage;
		request.id = product;
		if (!request.id) {
			request.id = "new"
		}
		if (setting == "new") {
			request.id = "new";
			request.next = setting
		}
		if (setting == "close") {
			delete request.id
		}
		if (setting == "previous") {
			a.each(worklist, function (b, c) {
				if (c.id != product) {
					return
				}
				if (worklist[b - 1]) {
					request.next = worklist[b - 1].id
				}
				else {
					delete request.id
				}
			})
		}
		if (setting == "next") {
			a.each(worklist, function (b, c) {
				if (c.id != product) {
					return
				}
				if (worklist[b + 1]) {
					request.next = worklist[b + 1].id
				}
				else {
					delete request.id
				}
			})
		}
	}).change()
}

function categories() {
	var b = jqnc();
	b("#new-category").hide();
	b("#new-category-button").click(function () {
		b("#new-category").toggle();
		b("#new-category input").focus();
		b(this).toggle()
	});
	b("#add-new-category").click(function () {
		var c = b("#new-category input").val(),
			d = b("#new-category select").val();
		if (c != "") {
			b("#new-category").hide();
			b("#new-category-button").show();
			b(this).addClass("updating");
			b.getJSON(addcategory_url + "&action=ecart_add_category&name=" + c + "&parent=" + d, function (e) {
				b("#add-new-category").removeClass("updating");
				a(e);
				b.get(catmenu_url + "&action=ecart_category_menu", false, function (g) {
					var f = b("#new-category select option").eq(0).clone();
					b("#new-category select").empty().html(g);
					f.prependTo("#new-category select");
					b("#new-category select").attr("selectedIndex", 0)
				}, "html");
				b("#new-category input").val("")
			})
		}
	});
	b("#category-menu input.category-toggle").change(function () {
		if (!this.checked) {
			return true
		}
		var d, c = new Array();
		b("#details-menu").children().children().find("input.label").each(function (f, e) {
			c.push(b(e).val())
		});
		d = b(this).attr("id").substr(b(this).attr("id").indexOf("-") + 1);
		b.getJSON(spectemp_url + "&action=ecart_spec_template&category=" + d, function (e) {
			if (!e) {
				return true
			}
			for (d in e) {
				e[d].add = true;
				if (c.toString().search(e[d]["name"]) == -1) {
					addDetail(e[d])
				}
			}
		});
		b.getJSON(opttemp_url + "&action=ecart_options_template&category=" + d, function (f) {
			if (!(f && f.options)) {
				return true
			}
			var h = b("#variations-setting"),
				e = !f.options.v ? f.options : f.options.v,
				g = false;
			if (!h.attr("checked")) {
				h.attr("checked", true).trigger("toggleui")
			}
			if (optionMenus.length > 0) {
				b.each(e, function (j, i) {
					if (!(i && i.name && i.options)) {
						return
					}
					if (menu = optionMenuExists(i.name)) {
						g = false;
						b.each(i.options, function (k, l) {
							if (!(l && l.name)) {
								return
							}
							if (!optionMenuItemExists(menu, l.name)) {
								menu.addOption(l);
								g = true
							}
						});
						if (g) {
							addVariationPrices()
						}
					}
					else {
						delete i.id;
						b.each(i.options, function (k, l) {
							if (!(l && l.name)) {
								return
							}
							delete l.id
						});
						addVariationOptionsMenu(i)
					}
				})
			}
			else {
				loadVariations(e, f.prices)
			}
		})
	});

	function a(j) {
		var g = jqnc(),
			h, f, k, m, l = false,
			i = false,
			d = g("#new-category input").val(),
			e = g("#new-category select").val();
		if (e > 0) {
			if (g("#category-element-" + e + " ul li").size() > 0) {
				l = g("#category-element-" + e + " ul")
			}
			else {
				h = g("#category-element-" + e);
				f = g("<li></li>").insertAfter(h);
				l = g("<ul></ul>").appendTo(f)
			}
		}
		else {
			l = g("#category-menu > ul")
		}
		i = false;
		l.children().each(function () {
			k = g(this).children("label").text();
			if (k && d < k) {
				i = this;
				return false
			}
		});
		if (!i) {
			m = g('<li id="category-element-' + j.id + '"></li>').appendTo(l)
		}
		else {
			m = g('<li id="category-element-' + j.id + '"></li>').insertBefore(i)
		}
		g('<input type="checkbox" name="categories[]" value="' + j.id + '" id="category-' + j.id + '" checked="checked" />').appendTo(m);
		g('<label for="category-' + j.id + '"></label>').html(d).appendTo(m)
	}
}

function tags() {
	var a = jqnc();

	function b() {
		a("#tagchecklist").empty();
		var c = a("#tags").val().split(",");
		if (c[0].length > 0) {
			a(c).each(function (e, d) {
				entry = a("<span></span>").html(d).appendTo("#tagchecklist");
				deleteButton = a("<a></a>").html("X").addClass("ntdelbutton").click(function () {
					c = a("#tags").val().replace(new RegExp("(^" + d + ",?|," + d + "\\b)"), "");
					a("#tags").val(c);
					b()
				}).prependTo(entry)
			})
		}
	}
	a("#newtags").focus(function () {
		if (a(this).val() == a(this).attr("title")) {
			a(this).val("").toggleClass("form-input-tip")
		}
	});
	a("#newtags").blur(function () {
		if (a(this).val() == "") {
			a(this).val(a(this).attr("title")).toggleClass("form-input-tip")
		}
	});
	a("#add-tags").click(function () {
		if (a("#newtags").val() == a("#newtags").attr("title")) {
			return true
		}
		newtags = a("#newtags").val().split(",");
		a(newtags).each(function (e, c) {
			var d = a("#tags").val();
			c = a.trim(c);
			if (d == "") {
				a("#tags").val(c)
			}
			else {
				if (d != c && d.indexOf(c + ",") == -1 && d.indexOf("," + c) == -1) {
					a("#tags").val(d + "," + c)
				}
			}
		});
		b();
		a("#newtags").val("").blur()
	});
	b()
};