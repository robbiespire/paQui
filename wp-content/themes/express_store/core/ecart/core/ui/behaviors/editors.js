/*
 * editors.js - Product & Category editor behaviors
 */
function NestedMenu(d, e, g, j, b, f, a) {
	var c = jqnc(),
		h = this;
	if (!a) {
		a = {
			axis: "y"
		}
	}
	h.items = f;
	h.dataname = g;
	h.index = d;
	h.element = c('<li><div class="move"></div><input type="hidden" name="' + g.replace("[", "-").replace("]", "-") + '-sortorder[]" value="' + d + '" class="sortorder" /><input type="hidden" name="' + g + "[" + d + '][id]" class="id" /><input type="text" name="' + g + "[" + d + '][name]" class="label" /><button type="button" class="delete" style="color:red;">Delete This Group</button></li>').appendTo(c(e).children("ul"));
	h.moveHandle = h.element.find("div.move");
	h.sortorder = h.element.find("input.sortorder");
	h.id = h.element.find("input.id");
	h.label = h.element.find("input.label");
	h.deleteButton = h.element.find("button.delete").bind("delete", function () {
		var i = c(e).find("input.deletes");
		if (c(h.id).val() != "") {
			i.val((i.val() == "") ? c(h.id).val() : i.val() + "," + c(h.id).val())
		}
		if (f) {
			h.itemsElement.remove()
		}
		h.element.remove()
	}).click(function () {
		c(this).trigger("delete")
	});
	if (h.items) {
		if (f.type == "list") {
			h.itemsElement = c("<ul></ul>").appendTo(f.target).hide()
		}
		else {
			h.itemsElement = c("<li></li>").appendTo(f.target).hide()
		}
	}
	h.selected = function () {
		c(e).find("ul li").removeClass("selected");
		c(h.element).addClass("selected");
		if (f) {
			c(f.target).children().hide();
			c(h.itemsElement).show()
		}
	};
	h.element.click(this.selected).hover(function () {
		c(this).addClass("hover")
	}, function () {
		c(this).removeClass("hover")
	});
	h.label.mouseup(function (i) {
		this.select()
	}).focus(function () {
		c(this).keydown(function (i) {
			i.stopPropagation();
			if (i.keyCode == 13) {
				c(this).blur().unbind("keydown")
			}
		})
	});
	h.id.val(h.index);
	if (b && b.id) {
		h.id.val(b.id)
	}
	if (b && b.name) {
		h.label.val(htmlentities(b.name))
	}
	else {
		h.label.val(j + " " + h.index)
	}
	if (!c(e).children("ul").hasClass("ui-sortable")) {
		c(e).children("ul").sortable(a)
	}
	else {
		c(e).children("ul").sortable("refresh")
	}
}

function NestedMenuContent(b, e, a, d) {
	var c = jqnc();
	this.contents = c('<textarea name="' + a + "[" + b + '][value]" cols="40" rows="7"></textarea>').appendTo(e);
	if (d && d.value) {
		this.contents.val(htmlentities(d.value))
	}
}

function NestedMenuOption(d, g, c, a, f) {
	var e = jqnc(),
		b = this;
	b.index = e(g).contents().length;
	b.element = e('<li class="option"><div class="move"></div><input type="hidden" name="' + c + "[" + d + "][options][" + this.index + '][id]" class="id" /><input type="text" name="' + c + "[" + d + "][options][" + this.index + '][name]" class="label" /><button type="button" class="delete" style="color:red;">Delete This Feature</button></li>').appendTo(g);
	b.moveHandle = b.element.find("div.move");
	b.id = b.element.find("input.id");
	b.label = b.element.find("input.label");
	b.deleteButton = b.element.find("button.delete").click(function () {
		e(b.element).remove()
	});
	b.element.hover(function () {
		e(this).addClass("hover")
	}, function () {
		e(this).removeClass("hover")
	});
	b.label.click(function () {
		this.select()
	}).focus(function () {
		e(this).keydown(function (h) {
			h.stopPropagation();
			if (h.keyCode == 13) {
				e(this).blur().unbind("keydown")
			}
		})
	});
	b.id.val(b.index);
	if (f.id) {
		b.id.val(f.id)
	}
	if (f.name) {
		b.label.val(htmlentities(f.name))
	}
	if (!f.name) {
		b.label.val(a + " " + (b.index + 1))
	}
}

function loadVariations(a, b) {
	if (!a) {
		return
	}
	var c = jqnc();
	c.each(a, function (d, e) {
		if (e && e.id) {
			addVariationOptionsMenu(e)
		}
	});
	c.each(b, function (d, e) {
		if (this.context == "variation") {
			Pricelines.add(this.options.split(","), this, "#variations-pricing")
		}
	});
	Pricelines.updateVariationsUI();
	c.each(a, function (d, e) {
		if (!(e && e.options)) {
			return
		}
		c.each(e.options, function (f, g) {
			if (g && g.id && g.linked == "on") {
				Pricelines.linkVariations(g.id)
			}
		})
	})
}

function addVariationOptionsMenu(e) {
	var f = jqnc(),
		d = f("#variations-menu"),
		g = f("#variations-list"),
		h = f("#addVariationMenu"),
		a = f("#addVariationOption"),
		i = f("#linkOptionVariations"),
		b = variationsidx,
		c = new NestedMenu(b, d, "options[v]", OPTION_MENU_DEFAULT, e, {
			target: g,
			type: "list"
		}, {
			axis: "y",
			update: function () {
				orderOptions(d, g)
			}
		});
	c.addOption = function (l) {
		var m = false,
			k, j;
		if (!l) {
			l = new Object()
		}
		if (!l.id) {
			m = true;
			l.id = optionsidx
		}
		else {
			if (l.id > optionsidx) {
				optionsidx = l.id
			}
		}
		k = new NestedMenuOption(c.index, c.itemsElement, "options[v]", NEW_OPTION_DEFAULT, l);
		optionsidx++;
		j = k.id.val();
		k.linkIcon = f('<img src="' + uidir + '/icons/linked.png" alt="linked" width="16" height="16" class="link" />').appendTo(k.moveHandle);
		k.linked = f('<input type="hidden" name="options[v][' + c.index + "][options][" + k.index + '][linked]" class="linked" />').appendTo(k.element).change(function () {
			if (f(this).val() == "off") {
				k.linkIcon.addClass("invisible")
			}
			if (f(this).val() == "on") {
				k.linkIcon.removeClass("invisible")
			}
		});
		if (l.linked) {
			k.linked.val(l.linked).change()
		}
		else {
			k.linked.val("off").change()
		}
		k.selected = function () {
			if (k.element.hasClass("selected")) {
				g.find("ul li").removeClass("selected");
				selectedMenuOption = false
			}
			else {
				g.find("ul li").removeClass("selected");
				f(k.element).addClass("selected");
				selectedMenuOption = k
			}
			i.change()
		};
		k.element.click(k.selected);
		productOptions[j] = k.label;
		k.label.blur(function () {
			updateVariationLabels()
		});
		k.deleteButton.unbind("click");
		k.deleteButton.click(function () {
			if (c.itemsElement.children().length == 1) {
				deleteVariationPrices([j], true)
			}
			else {
				deleteVariationPrices([j])
			}
			k.element.remove()
		});
		if (!m) {
			addVariationPrices(j)
		}
		else {
			addVariationPrices()
		}
		g.dequeue().animate({
			scrollTop: g.attr("scrollHeight") - g.height()
		}, 200);
		k.label.click().focus().select().keydown(function (o) {
			var n = o.keyCode || o.which;
			if (n != 9) {
				return
			}
			o.preventDefault();
			k.label.blur();
			a.focus()
		});
		c.items.push(k)
	};
	c.items = new Array();
	if (e && e.options) {
		f.each(e.options, function () {
			c.addOption(this)
		})
	}
	else {
		c.addOption();
		c.addOption()
	}
	c.itemsElement.sortable({
		axis: "y",
		update: function () {
			orderVariationPrices()
		}
	});
	c.element.unbind("click", c.click).click(function () {
		c.selected();
		f(a).unbind("click").click(c.addOption)
	});
	optionMenus[variationsidx++] = c;
	c.deleteButton.unbind("click").click(function () {
		var j = new Array();
		f(c.itemsElement).find("li").not(".ui-sortable-helper").find("input.id").each(function (k, l) {
			j.push(f(l).val())
		});
		deleteVariationPrices(j, true);
		f(this).trigger("delete")
	});
	if (!e) {
		g.dequeue().animate({
			scrollTop: g.attr("scrollHeight") - g.height()
		}, 200);
		c.label.click().focus().select().keydown(function (k) {
			var j = k.keyCode || k.which;
			if (j != 9) {
				return
			}
			k.preventDefault();
			a.focus()
		})
	}
}

function buildVariations() {
	var d = jqnc(),
		e, l, g, h, m = new Array(),
		j = d("#variations-list ul"),
		f = j.length,
		a = f - 1,
		k = new Array(j.length),
		c = new Array(j.length),
		b = 0;
	j.each(function (n, i) {
		c[n] = d(i).children().length;
		if (b == 0) {
			b = d(i).children().length
		}
		else {
			b = b * d(i).children().length
		}
		k[n] = 0
	});
	for (e = 0; e < b; e++) {
		for (l = 0; l < j.length; l++) {
			g = d(j[l]).children("li").not(".ui-sortable-helper").children("input.id");
			if (!m[e]) {
				m[e] = [d(g[k[l]]).val()]
			}
			else {
				m[e].push(d(g[k[l]]).val())
			}
		}
		if (++k[a] >= c[a]) {
			for (h = a; h > -1; h--) {
				if (k[h] < c[h]) {
					continue
				}
				k[h] = 0;
				if (h - 1 > -1) {
					k[(h - 1)]++
				}
			}
		}
	}
	return m
}

function addVariationPrices(g) {
	if (g) {
		return
	}
	var f = jqnc(),
		c, e, a = buildVariations(),
		b = f("#variations-pricing"),
		h = f(b).children(),
		d = false;
	f(a).each(function (j, i) {
		c = xorkey(i);
		e = xorkey(i.slice(0, i.length - 1));
		if (e == "") {
			e = -1
		}
		if (!Pricelines.row[c]) {
			if (Pricelines.row[e]) {
				Pricelines.row[c] = Pricelines.row[e];
				delete Pricelines.row[e];
				Pricelines.row[c].setOptions(i)
			}
			else {
				if (h.length == 0) {
					Pricelines.add(i, {
						context: "variation"
					}, "#variations-pricing")
				}
				else {
					Pricelines.add(i, {
						context: "variation"
					}, Pricelines.row[xorkey(a[(j - 1)])].row, "after")
				}
				d = true
			}
		}
	});
	if (d) {
		Pricelines.updateVariationsUI()
	}
}

function deleteVariationPrices(f, h) {
	var c = jqnc(),
		e = buildVariations(),
		g = false,
		d, k, b, j, a;
	c(e).each(function (l, i) {
		k = xorkey(i);
		for (d = 0; d < f.length; d++) {
			if (i.indexOf(f[d]) != -1) {
				b = new Array();
				c(i).each(function (m, n) {
					if (n != f[d]) {
						b.push(n)
					}
				});
				j = xorkey(b);
				if (h && !Pricelines.row[j]) {
					if (j != 0) {
						Pricelines.row[j] = Pricelines.row[k]
					}
					else {
						Pricelines.row[k].row.remove()
					}
					delete Pricelines.row[k];
					if (Pricelines.row[j]) {
						Pricelines.row[j].setOptions(b);
						g = true
					}
				}
				else {
					if (Pricelines.row[k]) {
						a = c("#priceid-" + Pricelines.row[k].id).val();
						if (c("#deletePrices").val() == "") {
							c("#deletePrices").val(a)
						}
						else {
							c("#deletePrices").val(c("#deletePrices").val() + "," + a)
						}
						Pricelines.remove(k)
					}
				}
			}
		}
	});
	if (g) {
		Pricelines.updateVariationsUI()
	}
}

function optionMenuExists(a) {
	if (!a) {
		return false
	}
	var c = jqnc(),
		b = false;
	c.each(optionMenus, function (e, d) {
		if (d && c(d.label).val() == a) {
			return (b = e)
		}
	});
	if (optionMenus[b]) {
		return optionMenus[b]
	}
	return b
}

function optionMenuItemExists(d, a) {
	if (!d || !d.items || !a) {
		return false
	}
	var c = jqnc(),
		b = false;
	c.each(d.items, function (f, e) {
		if (e && c(e.label).val() == a) {
			return (b = true)
		}
	});
	return b
}

function updateVariationLabels() {
	var b = jqnc(),
		a = buildVariations();
	b(a).each(function (e, c) {
		var d = xorkey(c);
		if (Pricelines.row[d]) {
			Pricelines.row[d].updateLabel()
		}
	})
}

function orderOptions(c, a) {
	var b = jqnc();
	b(c).find("ul li").not(".ui-sortable-helper").find("input.id").each(function (d, e) {
		if (e) {
			b(optionMenus[b(e).val()].itemsElement).appendTo(a)
		}
	});
	orderVariationPrices()
}

function orderVariationPrices() {
	var c = jqnc(),
		b, a = buildVariations();
	c(a).each(function (e, d) {
		b = xorkey(d);
		if (b > 0 && Pricelines.row[b]) {
			Pricelines.reorderVariation(b, d)
		}
	});
	Pricelines.updateVariationsUI("tabs")
}

function xorkey(c) {
	for (var b = 0, a = 0; a < c.length; a++) {
		b = b ^ (c[a] * 7001)
	}
	return b
}

function variationsToggle() {
	var c = jqnc(),
		a = c(this),
		b = c("#variations"),
		d = c("#product-pricing");
	if (a.attr("checked")) {
		if (Pricelines.row[0]) {
			Pricelines.row[0].disable()
		}
		d.hide();
		b.show()
	}
	else {
		b.hide();
		d.show();
		if (Pricelines.row[0]) {
			Pricelines.row[0].enable()
		}
	}
}

function addonsToggle() {
	var c = jqnc(),
		a = c(this),
		b = c("#addons");
	if (a.attr("checked")) {
		b.show()
	}
	else {
		b.hide()
	}
}

function clearLinkedIcons() {
	jQuery("#variations-list input.linked").val("off").change()
}

function linkVariationsButton() {
	var a = jqnc();
	if (selectedMenuOption) {
		if (selectedMenuOption.linked.val() == "off") {
			if (Pricelines.allLinked()) {
				clearLinkedIcons();
				Pricelines.unlinkAll()
			}
			selectedMenuOption.linked.val("on").change();
			Pricelines.linkVariations(selectedMenuOption.id.val())
		}
		else {
			selectedMenuOption.linked.val("off").change();
			Pricelines.unlinkVariations(selectedMenuOption.id.val())
		}
	}
	else {
		clearLinkedIcons();
		if (Pricelines.allLinked()) {
			Pricelines.unlinkAll()
		}
		else {
			Pricelines.linkAll()
		}
	}
	a(this).change()
}

function linkVariationsButtonLabel() {
	var a = jqnc();
	if (selectedMenuOption) {
		if (selectedMenuOption.linked.val() == "on") {
			a(this).find("small").html(" " + UNLINK_VARIATIONS)
		}
		else {
			a(this).find("small").html(" " + LINK_VARIATIONS)
		}
	}
	else {
		if (Pricelines.allLinked()) {
			a(this).find("small").html(" " + UNLINK_ALL_VARIATIONS)
		}
		else {
			a(this).find("small").html(" " + LINK_ALL_VARIATIONS)
		}
	}
}

function loadAddons(c, a) {
	var b = jqnc();
	if (!c) {
		return
	}
	b.each(c, function (e, d) {
		newAddonGroup(d)
	});
	b.each(a, function (d, e) {
		if (e.context == "addon") {
			var f = addonOptionsGroup[e.options];
			Pricelines.add(e.options, this, "#addon-pricegroup-" + f)
		}
	});
	Pricelines.updateVariationsUI()
}

function newAddonGroup(e) {
	var d = jqnc(),
		c = d("#addon-menu"),
		a = d("#addon-list"),
		h = d("#newAddonGroup"),
		b = d("#addAddonOption"),
		g = addon_group_idx,
		f = new NestedMenu(g, c, "options[a]", ADDON_GROUP_DEFAULT, e, {
			target: a,
			type: "list"
		}, {
			axis: "y",
			update: function () {
				orderAddonGroups()
			}
		});
	f.itemsElement.attr("id", "addon-group-" + g);
	f.pricegroup = d('<div id="addon-pricegroup-' + g + '" />').appendTo("#addon-pricing");
	f.pricegroupLabel = d("<label />").html("<h4>" + f.label.val() + "</h4>").prependTo(f.pricegroup);
	f.updatePriceLabel = function () {
		f.pricegroupLabel.html("<h4>" + f.label.val() + "</h4>")
	};
	f.label.blur(f.updatePriceLabel);
	f.addOption = function (k) {
		var l = false,
			j, i;
		if (!k) {
			k = new Object()
		}
		if (!k.id) {
			l = true;
			k.id = addonsidx
		}
		else {
			if (k.id > addonsidx) {
				addonsidx = k.id
			}
		}
		j = new NestedMenuOption(f.index, f.itemsElement, "options[a]", NEW_OPTION_DEFAULT, k);
		addonsidx++;
		i = j.id.val();
		j.selected = function () {
			if (j.element.hasClass("selected")) {
				a.find("ul li").removeClass("selected");
				selectedMenuOption = false
			}
			else {
				a.find("ul li").removeClass("selected");
				d(j.element).addClass("selected");
				selectedMenuOption = j
			}
		};
		j.element.click(j.selected);
		productAddons[i] = j.label;
		j.label.blur(function () {
			Pricelines.row[i].updateLabel()
		});
		j.deleteButton.unbind("click");
		j.deleteButton.click(function () {
			Pricelines.row[i].row.remove();
			j.element.remove()
		});
		if (l) {
			Pricelines.add(i, {
				context: "addon"
			}, f.pricegroup)
		}
		addonOptionsGroup[i] = f.index;
		f.items.push(j);
		a.dequeue().animate({
			scrollTop: a.attr("scrollHeight") - a.height()
		}, 200);
		j.label.click().focus().select().keydown(function (n) {
			var m = n.keyCode || n.which;
			if (m != 9) {
				return
			}
			n.preventDefault();
			j.label.blur();
			b.focus()
		})
	};
	f.items = new Array();
	if (e && e.options) {
		d.each(e.options, function () {
			f.addOption(this)
		})
	}
	else {
		f.addOption();
		f.addOption()
	}
	f.itemsElement.sortable({
		axis: "y",
		update: function () {
			orderAddonPrices(f.index)
		}
	});
	f.element.unbind("click", f.click);
	f.element.click(function () {
		f.selected();
		d(b).unbind("click").click(f.addOption)
	});
	addonGroups[addon_group_idx++] = f;
	f.deleteButton.unbind("click").click(function () {
		d("#addon-list #addon-group-" + f.index + " li").not(".ui-sortable-helper").find("input.id").each(function (j, i) {
			if (Pricelines.row[d(i).val()]) {
				Pricelines.row[d(i).val()].row.remove()
			}
		});
		f.deleteButton.trigger("delete");
		f.pricegroup.remove();
		f.element.remove()
	});
	if (!e) {
		c.dequeue().animate({
			scrollTop: c.attr("scrollHeight") - c.height()
		}, 200);
		f.label.click().focus().select().keydown(function (j) {
			var i = j.keyCode || j.which;
			if (i != 9) {
				return
			}
			j.preventDefault();
			f.label.blur();
			h.focus()
		})
	}
}

function orderAddonGroups() {
	var a = jqnc(),
		b;
	a("#addon-menu ul li").not(".ui-sortable-helper").find("input.id").each(function (c, d) {
		b = addonGroups[a(d).val()];
		b.pricegroup.appendTo("#addon-pricing")
	})
}

function orderAddonPrices(a) {
	var b = jqnc(),
		c = addonGroups[a];
	b("#addon-list #addon-group-" + c.index + " li").not(".ui-sortable-helper").find("input.id").each(function (e, d) {
		Pricelines.reorderAddon(b(d).val(), c.pricegroup)
	})
}

function readableFileSize(b) {
	var a = new Array("bytes", "KB", "MB", "GB"),
		d = b * 1,
		c = 0;
	if (d == 0) {
		return d
	}
	while (d > 1000) {
		d = d / 1024;
		c++
	}
	return d.toFixed(2) + " " + a[c]
}

function unsavedChanges() {
	var a = typeof (tinyMCE) != "undefined" ? tinyMCE.activeEditor : false;
	if (a && !a.isHidden()) {
		if (a.isDirty()) {
			return sjss.UNSAVED_CHANGES_WARNING
		}
	}
	if (changes && !saving) {
		return sjss.UNSAVED_CHANGES_WARNING
	}
}

function addDetail(f) {
	var e = jqnc(),
		c, b, d = e("#details-menu"),
		a = e("#details-list"),
		h = detailsidx++,
		g = new NestedMenu(h, d, "details", "Detail Name", f, {
			target: a
		});
	if (f && f.options) {
		b = e('<select name="details[' + g.index + '][value]"></select>').appendTo(g.itemsElement);
		for (c in f.options) {
			e("<option>" + f.options[c]["name"] + "</option>").appendTo(b)
		}
		if (f && f.value) {
			b.val(htmlentities(f.value))
		}
	}
	else {
		g.item = new NestedMenuContent(g.index, g.itemsElement, "details", f)
	}
	if (!f || f.add) {
		g.add = e('<input type="hidden" name="details[' + g.index + '][new]" value="true" />').appendTo(g.element);
		d.dequeue().animate({
			scrollTop: d.attr("scrollHeight") - d.height()
		}, 200);
		g.label.click().focus().select();
		if (g.item) {
			g.item.contents.keydown(function (j) {
				var i = j.keyCode || j.which;
				if (i != 9) {
					return
				}
				j.preventDefault();
				e("#addDetail").focus()
			})
		}
	}
}

function ImageUploads(a, m) {
	var g = jqnc(),
		d, c = {
			button_text: '<span class="button">' + ADD_IMAGE_BUTTON_TEXT + "</span>",
			button_text_style: '.button { text-align: center; font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; font-size: 9px; color: #333333; }',
			button_text_top_padding: 3,
			button_height: "22",
			button_width: "100",
			button_image_url: uidir + "/icons/buttons.png",
			button_placeholder_id: "swf-uploader-button",
			upload_url: ajaxurl,
			flash_url: uidir + "/behaviors/swfupload/swfupload.swf",
			file_queue_limit: 0,
			file_size_limit: filesizeLimit + "b",
			file_types: "*.jpg;*.jpeg;*.png;*.gif",
			file_types_description: "Web-compatible Image Files",
			file_upload_limit: filesizeLimit,
			post_params: {
				action: "ecart_upload_image",
				parent: a,
				type: m
			},
			swfupload_loaded_handler: h,
			file_queue_error_handler: f,
			file_dialog_complete_handler: e,
			upload_start_handler: k,
			upload_progress_handler: n,
			upload_error_handler: b,
			upload_success_handler: l,
			custom_settings: {
				loaded: false,
				targetHolder: false,
				progressBar: false,
				sorting: false
			},
			prevent_swf_caching: g.browser.msie,
			debug: imageupload_debug
		};
	if (flashuploader) {
		d = new SWFUpload(c)
	}
	g("#image-upload").upload({
		name: "Filedata",
		action: ajaxurl,
		params: {
			action: "ecart_upload_image",
			type: m
		},
		onSubmit: function () {
			this.targetHolder = g('<li id="image-uploading"><input type="hidden" name="images[]" value="" /><div class="progress"><div class="bar"></div><div class="gloss"></div></div></li>').appendTo("#lightbox");
			this.progressBar = this.targetHolder.find("div.bar");
			this.sorting = this.targetHolder.find("input")
		},
		onComplete: function (r) {
			var s = false,
				o, t, q = this.targetHolder;
			try {
				s = g.parseJSON(r)
			}
			catch (p) {
				s.error = r
			}
			if (!s || !s.id) {
				q.remove();
				if (s.error) {
					alert(s.error)
				}
				else {
					alert(UNKNOWN_UPLOAD_ERROR)
				}
				return false
			}
			q.attr({
				id: "image-" + s.id
			});
			this.sorting.val(s.id);
			o = g('<img src="?siid=' + s.id + '" width="96" height="96" class="handle" />').appendTo(q).hide();
			t = g('<button type="button" name="deleteImage" value="' + s.src + '" title="Delete product image&hellip;" class="deleteButton"><img src="' + uidir + '/icons/delete.png" alt="-" width="16" height="16" /></button>').appendTo(g(q)).hide();
			g(this.progressBar).animate({
				width: "76px"
			}, 250, function () {
				g(this).parent().fadeOut(500, function () {
					g(this).remove();
					g(o).fadeIn("500");
					j(t)
				})
			})
		}
	});
	g(document).load(function () {
		if (!d.loaded) {
			g("#product-images .swfupload").remove()
		}
	});
	i();
	g("#lightbox li").each(function () {
		g(this).dblclick(function () {
			var r = g(this).attr("id") + "-details",
				p = g("#" + r),
				B = p.find("input[type=hidden]").val(),
				w = p.find("img"),
				t = p.find("input.imagetitle"),
				C = p.find("input.imagealt"),
				s = p.find("input.imagecropped"),
				y = g('<div class="image-details-editor"><div class="details-editor"><img class="thumb" width="96" height="96" /><div class="details"><p><label>' + IMAGE_DETAILS_TITLE_LABEL + ': </label><input type="text" name="title" /></p><p><label>' + IMAGE_DETAILS_ALT_LABEL + ': </label><input type="text" name="alt" /></p></div></div><div class="cropping"><p class="clear">' + IMAGE_DETAILS_CROP_LABEL + ': <select name="cropimage"><option></option></select></p><div class="cropui"></div><br class="clear"/></div><input type="button" class="button-primary alignright" value="&nbsp;&nbsp;' + IMAGE_DETAILS_DONE + '&nbsp;&nbsp;" /></div>'),
				o = y.find("img").attr("src", w.attr("src")),
				x = y.find("input[name=title]").val(t.val()).change(function () {
					t.val(x.val())
				}),
				u = y.find("input[name=alt]").val(C.val()).change(function () {
					C.val(u.val())
				}),
				z = y.find("input[type=button]").click(function () {
					g.fn.colorbox.close()
				}),
				q = y.find("div.cropping").hide(),
				A = y.find("div.cropui"),
				v = y.find("select[name=cropimage]").change(function () {
					if (v.val() == "") {
						A.empty();
						g.fn.colorbox.resize();
						return
					}
					var E = v.val().split(":"),
						D = s.filter("input[alt=" + v.val() + "]").val().split(",");
					A.empty().scaleCrop({
						imgsrc: "?siid=" + B,
						target: {
							width: parseInt(E[0], 10),
							height: parseInt(E[1], 10)
						},
						init: {
							x: parseInt(D[0], 10),
							y: parseInt(D[1], 10),
							s: new Number(D[2])
						}
					}).ready(function () {
						var F = 125;
						g.fn.colorbox.resize({
							innerWidth: (parseInt(E[0], 10)) + F
						})
					}).bind("change.scalecrop", function (F, G) {
						if (G) {
							s.filter("input[alt=" + v.val() + "]").val(G.x + "," + G.y + "," + G.s)
						}
					})
				});
			if (s.size() > 0) {
				s.each(function (D, E) {
					var F = g(E).attr("alt");
					g('<option value="' + F + '">' + (D + 1) + ": " + F.replace(":", "&times;") + "</option>").appendTo(v)
				});
				q.show()
			}
			g.fn.colorbox({
				title: IMAGE_DETAILS_TEXT,
				html: y
			})
		});
		j(g(this).find("button.deleteButton"))
	});

	function h() {
		g("#browser-uploader").hide();
		d.loaded = true
	}

	function i() {
		if (g("#lightbox li").size() > 0) {
			g("#lightbox").sortable({
				opacity: 0.8
			})
		}
	}

	function f(p, o, q) {
		if (o == SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You selected too many files to upload at one time. " + (q === 0 ? "You have reached the upload limit." : "You may upload " + (q > 1 ? "up to " + q + " files." : "only one file.")));
			return
		}
		else {
			alert(q)
		}
	}

	function e(p, q) {
		try {
			this.startUpload()
		}
		catch (o) {
			this.debug(o)
		}
	}

	function k(o) {
		this.targetHolder = g('<li class="image uploading"><input type="hidden" name="images[]" /><div class="progress"><div class="bar"></div><div class="gloss"></div></div></li>').appendTo(g("#lightbox"));
		this.progressBar = this.targetHolder.find("div.bar");
		this.sorting = this.targetHolder.find("input")
	}

	function n(p, o, q) {
		this.progressBar.animate({
			width: Math.ceil((o / q) * 76) + "px"
		}, 100)
	}

	function b(p, o, q) {}

	function l(s, r) {
		var t = false,
			o, u, q = this.targetHolder;
		try {
			t = g.parseJSON(r)
		}
		catch (p) {
			q.remove();
			alert(r);
			return false
		}
		if (!t.id) {
			q.remove();
			if (t.error) {
				alert(t.error)
			}
			else {
				alert(UNKNOWN_UPLOAD_ERROR)
			}
			return true
		}
		q.attr({
			id: "image-" + t.id
		});
		this.sorting.val(t.id);
		o = g('<img src="?siid=' + t.id + '" width="96" height="96" class="handle" />').appendTo(q).hide();
		u = g('<button type="button" name="deleteImage" value="' + t.id + '" title="Delete product image&hellip;" class="deleteButton"><input type="hidden" name="ieisstupid" value="' + t.id + '" /><img src="' + uidir + '/icons/delete.png" alt="-" width="16" height="16" /></button>').appendTo(q).hide();
		i();
		this.progressBar.animate({
			width: "76px"
		}, 250, function () {
			g(this).parent().fadeOut(500, function () {
				g(this).remove();
				g(o).fadeIn("500");
				j(u)
			})
		})
	}

	function j(o) {
		o.hide();
		o.parent().hover(function () {
			o.show()
		}, function () {
			o.hide()
		});
		o.click(function () {
			if (confirm(DELETE_IMAGE_WARNING)) {
				var p = (o.val().substr(0, 1) == "<") ? o.find("input[name=ieisstupid]").val() : o.val(),
					q = g("#deleteImages"),
					r = q.val();
				q.val(r == "" ? p : r + "," + p);
				o.parent().fadeOut(500, function () {
					g(this).remove()
				})
			}
		})
	}
}
jQuery.fn.FileChooser = function (k, d) {
	var e = jqnc(),
		j = this,
		g = e("#import-url"),
		i = e("#attach-file"),
		b = e("#download_path-" + k),
		c = e("#download_file-" + k),
		a = e("#file-" + k),
		h = false,
		f = false;
	j.line = k;
	j.status = d;
	g.unbind("keydown").unbind("keypress").suggest(sugg_url + "&action=ecart_storage_suggestions&t=download", {
		delay: 500,
		minchars: 3,
		multiple: false,
		onSelect: function () {
			g.change()
		}
	}).change(function () {
		var l = e(this);
		l.removeClass("warning").addClass("verifying");
		e.ajax({
			url: fileverify_url + "&action=ecart_verify_file&t=download",
			type: "POST",
			data: "url=" + l.val(),
			timeout: 10000,
			dataType: "text",
			success: function (m) {
				l.attr("class", "fileimport");
				if (m == "OK") {
					return l.addClass("ok")
				}
				if (m == "NULL") {
					l.attr("title", FILE_NOT_FOUND_TEXT)
				}
				if (m == "ISDIR") {
					l.attr("title", FILE_ISDIR_TEXT)
				}
				if (m == "READ") {
					l.attr("title", FILE_NOT_READ_TEXT)
				}
				l.addClass("warning")
			}
		})
	});
	e(this).click(function () {
		fileUploads.updateLine(k, d);
		i.unbind("click").click(function () {
			e.fn.colorbox.hide();
			if (h) {
				b.val(g.val());
				g.val("").attr("class", "fileimport");
				return true
			}
			var o = false,
				m = false,
				l = g.val(),
				n = function () {
					e.ajax({
						url: fileimportp_url + "&action=ecart_import_file_progress&proc=" + o,
						timeout: 500,
						dataType: "text",
						success: function (q) {
							var s = parseInt(m.size, 10),
								r = Math.ceil((q / s) * 76),
								p = a.find("div.progress > div.bar");
							if (q < s) {
								setTimeout(n, 1000)
							}
							else {
								if (p) {
									p.css({
										width: "100%"
									}).fadeOut(500, function () {
										if (!m.name) {
											return $this.attr("class", "")
										}
										a.attr("class", "file " + m.mime.replace("/", " ")).html(m.name + "<br /><small>" + readableFileSize(m.size) + "</small>");
										b.val(m.path);
										c.val(m.name);
										g.val("").attr("class", "fileimport")
									})
								}
								return
							}
							if (p) {
								p.animate({
									width: r + "px"
								}, 500)
							}
						}
					})
				};
			a.attr("class", "").html('<div class="progress"><div class="bar"></div><div class="gloss"></div></div><iframe width="0" height="0" src="' + fileimport_url + "&action=ecart_import_file&url=" + l + '"></iframe>');
			a.find("iframe").load(function () {
				var p = e(this).contents().find("body").html();
				m = e.parseJSON(p);
				if (m.error) {
					return a.attr("class", "error").html("<small>" + m.error + "</small>")
				}
				if (!m.path) {
					return a.attr("class", "error").html("<small>" + FILE_UNKNOWN_IMPORT_ERROR + "</small>")
				}
				if (m.stored) {
					a.attr("class", "file " + m.mime.replace("/", " ")).html(m.name + "<br /><small>" + readableFileSize(m.size) + "</small>");
					b.val(m.path);
					c.val(m.name);
					g.val("").attr("class", "fileimport");
					return
				}
				else {
					savepath = m.path.split("/");
					o = savepath[savepath.length - 1];
					n()
				}
			})
		})
	});
	e(this).colorbox({
		title: "Digital Product File Uploader",
		innerWidth: "380",
		innerHeight: "100",
		inline: true,
		href: "#chooser"
	})
};

function FileUploader(e, f) {
	var b = jqnc(),
		i = this;
	i.swfu = false;
	i.settings = {
		button_text: '<span class="button">' + UPLOAD_FILE_BUTTON_TEXT + "</span>",
		button_text_style: '.button { text-align: center; font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; font-size: 9px; color: #333333; }',
		button_text_top_padding: 3,
		button_height: "22",
		button_width: "100",
		button_image_url: uidir + "/icons/buttons.png",
		button_placeholder_id: e,
		button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE,
		flash_url: uidir + "/behaviors/swfupload/swfupload.swf",
		upload_url: ajaxurl,
		file_queue_limit: 1,
		file_size_limit: filesizeLimit + "b",
		file_types: "*.*",
		file_types_description: "All Files",
		file_upload_limit: filesizeLimit,
		post_params: {
			action: "ecart_upload_file"
		},
		swfupload_loaded_handler: d,
		file_queue_error_handler: j,
		file_dialog_complete_handler: h,
		upload_start_handler: a,
		upload_progress_handler: c,
		upload_success_handler: g,
		custom_settings: {
			loaded: false,
			targetCell: false,
			targetLine: false,
			progressBar: false
		},
		prevent_swf_caching: b.browser.msie,
		debug: fileupload_debug
	};
	if (flashuploader) {
		i.swfu = new SWFUpload(i.settings)
	}
	f.upload({
		name: "Filedata",
		action: ajaxurl,
		params: {
			action: "ecart_upload_file"
		},
		onSubmit: function () {
			b.fn.colorbox.hide();
			i.targetCell.attr("class", "").html("");
			b('<div class="progress"><div class="bar"></div><div class="gloss"></div></div>').appendTo(i.targetCell);
			i.progressBar = i.targetCell.find("div.bar")
		},
		onComplete: function (m) {
			var n = false,
				l = i.targetCell;
			try {
				n = b.parseJSON(m)
			}
			catch (k) {
				n.error = m
			}
			if (!n.id && !n.name) {
				l.html(NO_DOWNLOAD);
				if (n.error) {
					alert(n.error)
				}
				else {
					alert(UNKNOWN_UPLOAD_ERROR)
				}
				return false
			}
			n.type = n.type.replace(/\//gi, " ");
			b(i.progressBar).animate({
				width: "76px"
			}, 250, function () {
				b(this).parent().fadeOut(500, function () {
					l.attr("class", "file " + n.type).html(n.name + "<br /><small>" + readableFileSize(n.size) + '</small><input type="hidden" name="price[' + i.targetLine + '][download]" value="' + n.id + '" />');
					b(this).remove()
				})
			})
		}
	});
	b(i).load(function () {
		if (!i.swfu || !i.swfu.loaded) {
			b(f).parent().parent().find(".swfupload").remove()
		}
	});

	function d() {
		b(f).hide();
		this.loaded = true
	}
	i.updateLine = function (l, k) {
		if (!i.swfu) {
			i.targetLine = l;
			i.targetCell = k
		}
		else {
			i.swfu.targetLine = l;
			i.swfu.targetCell = k
		}
	};

	function j(l, k, m) {
		if (k == SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You selected too many files to upload at one time. " + (m === 0 ? "You have reached the upload limit." : "You may upload " + (m > 1 ? "up to " + m + " files." : "only one file.")));
			return
		}
		else {
			alert(m)
		}
	}

	function h(l, m) {
		b.fn.colorbox.hide();
		if (!l) {
			return
		}
		try {
			this.startUpload()
		}
		catch (k) {
			this.debug(k)
		}
	}

	function a(k) {
		this.targetCell.attr("class", "").html("");
		b('<div class="progress"><div class="bar"></div><div class="gloss"></div></div>').appendTo(this.targetCell);
		this.progressBar = this.targetCell.find("div.bar")
	}

	function c(l, k, m) {
		this.progressBar.animate({
			width: Math.ceil((k / m) * 76) + "px"
		}, 100)
	}

	function g(o, n) {
		var p = false,
			k = this.targetCell,
			m = this.targetLine;
		try {
			p = b.parseJSON(n)
		}
		catch (l) {
			p.error = n
		}
		if (!p.id && !p.name) {
			k.html(NO_DOWNLOAD);
			if (p.error) {
				alert(p.error)
			}
			else {
				alert(UNKNOWN_UPLOAD_ERROR)
			}
			return false
		}
		p.type = p.type.replace(/\//gi, " ");
		b(this.progressBar).animate({
			width: "76px"
		}, 250, function () {
			b(this).parent().fadeOut(500, function () {
				b(this).remove();
				b(k).attr("class", "file " + p.type).html(p.name + "<br /><small>" + readableFileSize(p.size) + '</small><input type="hidden" name="price[' + m + '][download]" value="' + p.id + '" />')
			})
		})
	}
}

function SlugEditor(d, b) {
	var c = jqnc(),
		a = this;
	a.edit_permalink = function () {
		var f, k = 0,
			h = c("#editable-slug"),
			e = h.html(),
			m = c("#slug_input"),
			n = m.html(),
			l = c("#edit-slug-buttons"),
			j = l.html(),
			g = c("#editable-slug-full").html();
		l.html('<button type="button" class="save button">' + SAVE_BUTTON_TEXT + '</button> <button type="button" class="cancel button">' + CANCEL_BUTTON_TEXT + "</button>");
		l.children(".save").click(function () {
			var i = h.children("input").val();
			c.post(editslug_url + "&action=ecart_edit_slug", {
				id: d,
				type: b,
				slug: i
			}, function (o) {
				h.html(e);
				l.html(j);
				if (o != -1) {
					h.html(o);
					c("#editable-slug-full").html(o);
					m.val(o)
				}
				a.enable()
			}, "text")
		});
		c("#edit-slug-buttons .cancel").click(function () {
			h.html(e);
			l.html(j);
			m.attr("value", n);
			a.enable()
		});
		for (f = 0; f < g.length; ++f) {
			if ("%" == g.charAt(f)) {
				k++
			}
		}
		slug_value = (k > g.length / 4) ? "" : g;
		h.html('<input type="text" id="new-post-slug" value="' + slug_value + '" />').children("input").keypress(function (o) {
			var i = o.which;
			if (i == 13 || i == 27) {
				o.preventDefault()
			}
			if (13 == i) {
				l.children(".save").click()
			}
			if (27 == i) {
				l.children(".cancel").click()
			}
			m.val(this.value)
		}).focus()
	};
	a.enable = function () {
		c("#edit-slug-buttons").children(".edit-slug").click(function () {
			a.edit_permalink()
		});
		c("#edit-slug-buttons").children(".view").click(function () {
			document.location.href = canonurl + c("#editable-slug-full").html()
		});
		c("#editable-slug").click(function () {
			c("#edit-slug-buttons").children(".edit-slug").click()
		})
	};
	a.enable()
};