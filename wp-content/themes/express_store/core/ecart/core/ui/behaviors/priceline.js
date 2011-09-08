/*
 * priceline.js - Priceline editor
 */
function Pricelines() {
	var b = jqnc(),
		a = this;
	a.idx = 0;
	a.row = new Object();
	a.variations = new Array();
	a.addons = new Array();
	a.linked = new Array();
	a.add = function (d, g, i, j) {
		if (!g) {
			g = {
				context: "product"
			}
		}
		var f, h, e, c;
		if (g.context == "variation") {
			f = xorkey(d);
			h = new Priceline(a.idx, d, g, i, j);
			a.row[f] = h;
			if (j) {
				e = parseInt(i.optionkey.val(), 10);
				c = b.inArray(e, a.variations);
				if (c != -1) {
					if (j == "before") {
						a.variations.splice(c, 0, xorkey(h.options))
					}
					else {
						a.variations.splice(c + 1, 0, xorkey(h.options))
					}
				}
			}
			else {
				a.variations.push(xorkey(h.options))
			}
		}
		if (g.context == "addon") {
			h = new Priceline(a.idx, d, g, i, j);
			a.row[d] = h
		}
		if (g.context == "product") {
			h = new Priceline(0, d, g, i, j);
			a.row[0] = h
		}
		b("#prices").val(a.idx++)
	};
	a.exists = function (c) {
		if (a.row[c]) {
			return true
		}
		return false
	};
	a.remove = function (d) {
		var c = b.inArray(d, a.variations);
		if (c != -1) {
			a.variations.splice(c, 1)
		}
		a.row[d].row.remove();
		delete a.row[d]
	};
	a.reorderVariation = function (f, d) {
		var e = a.row[f],
			c = b.inArray(f, a.variations);
		e.row.appendTo("#variations-pricing");
		e.setOptions(d);
		if (c == -1) {
			return
		}
		a.variations.splice(c, 1);
		a.variations.push(xorkey(e.options))
	};
	a.reorderAddon = function (e, d) {
		var c = a.row[e];
		c.row.appendTo(d)
	};
	a.updateVariationsUI = function (f) {
		var d, c, g, e;
		for (d in a.variations) {
			c = a.variations[d];
			if (!Pricelines.row[c]) {
				delete a.variations[d];
				continue
			}
			g = Pricelines.row[c];
			g.updateTabIndex(d);
			if (f && f == "tabs") {
				continue
			}
			g.unlinkInputs();
			for (e in a.linked) {
				if (b.inArray(e, a.row[c].options) != -1) {
					if (!a.linked[e][c]) {
						a.linked[e].push(c)
					}
					a.row[c].linkInputs(e)
				}
			}
		}
	};
	a.linkVariations = function (d) {
		if (!d) {
			return
		}
		for (var c in a.row) {
			if (b.inArray(d.toString(), a.row[c].options) != -1) {
				if (!a.linked[d]) {
					a.linked[d] = new Array()
				}
				a.linked[d].push(c);
				a.row[c].linkInputs(d)
			}
		}
	};
	a.unlinkVariations = function (c) {
		if (!c) {
			return
		}
		if (!a.linked[c]) {
			return
		}
		for (var d in a.linked[c]) {
			if (a.row[a.linked[c][d]]) {
				a.row[a.linked[c][d]].unlinkInputs(c)
			}
		}
		a.linked.splice(c, 1)
	};
	a.unlinkAll = function () {
		for (var c in a.row) {
			a.row[c].unlinkInputs()
		}
		a.linked.splice(0, 1)
	};
	a.updateVariationLinks = function () {
		if (!a.linked) {
			return
		}
		var c, d;
		for (c in a.row) {
			a.row[c].unlinkInputs()
		}
		for (d in a.linked) {
			a.linked[d] = false;
			a.linkVariations(d)
		}
	};
	a.allLinked = function () {
		if (a.linked[0]) {
			return true
		}
		return false
	};
	a.linkAll = function () {
		a.unlinkAll();
		a.linked = new Array();
		a.linked[0] = new Array();
		for (var c in a.row) {
			if (c == 0) {
				continue
			}
			a.linked[0].push(c);
			a.row[c].linkInputs(0)
		}
	}
}

function Priceline(n, c, y, x, j) {
	var e = jqnc(),
		w = this,
		t = template,
		l = Pricelines,
		r = "",
		p, h, d, g, q, a, f, s, k, b, m, o, v, u;
	w.id = n;
	w.options = c;
	w.data = y;
	w.label = false;
	w.links = new Array();
	w.inputs = new Array();
	w.lasttype = false;
	p = w.id;
	h = "price[" + p + "]";
	w.row = e('<div id="row-' + p + '" class="priceline" />');
	if (j == "after") {
		w.row.insertAfter(x)
	}
	else {
		if (j == "before") {
			w.row.insertBefore(x)
		}
		else {
			w.row.appendTo(x)
		}
	}
	d = e('<div class="pricing-label" />').appendTo(w.row);
	g = e('<label for="label-' + p + '" />').appendTo(d);
	w.label = e('<input type="hidden" name="price[' + p + '][label]" id="label-' + p + '" />').appendTo(d);
	w.label.change(function () {
		g.text(e(this).val())
	});
	if (!y.id) {
		y.id = ""
	}
	if (!y.product) {
		y.product = product
	}
	if (!y.donation) {
		y.donation = {
			"var": false,
			min: false
		}
	}
	e('<input type="hidden" name="' + h + '[id]" id="priceid-' + p + '" value="' + y.id + '" /><input type="hidden" name="' + h + '[product]" id="product-' + p + '" value="' + y.product + '" /><input type="hidden" name="' + h + '[context]" id="context-' + p + '"/><input type="hidden" name="' + h + '[optionkey]" id="optionkey-' + p + '" class="optionkey" /><input type="hidden" name="' + h + '[options]" id="options-' + p + '" value="" /><input type="hidden" name="sortorder[]" id="sortorder-' + p + '" value="' + p + '" />').appendTo(d);
	q = e("#priceid-" + p);
	a = e("#context-" + p);
	f = e("#options-" + p);
	s = e("#sortorder-" + p);
	k = e("#optionkey-" + p);
	w.row.optionkey = k;
	e(priceTypes).each(function (i, z) {
		r += '<option value="' + z.value + '">' + z.label + "</option>"
	});
	b = e('<select name="price[' + p + '][type]" id="type-' + p + '"></select>').html(r).appendTo(d);
	if (y && y.label) {
		w.label.val(htmlentities(y.label)).change();
		b.val(y.type)
	}
	m = e('<div class="pricing-ui clear" />').appendTo(w.row);
	o = e("<table/>").addClass("pricing-table").appendTo(m);
	v = e("<tr/>").appendTo(o);
	u = e("<tr/>").appendTo(o);
	w.price = function (z, i) {
		var B, A;
		B = e('<th><label for="price-' + p + '">' + PRICE_LABEL + "</label></th>").appendTo(v);
		A = e('<td><input type="text" name="' + h + '[price]" id="price-' + p + '" value="0" size="10" class="selectall money right" /><br /><input type="hidden" name="' + h + '[tax]" value="on" /><input type="checkbox" name="' + h + '[tax]" id="tax-' + p + '" value="off" /><label for="tax-' + p + '"> ' + NOTAX_LABEL + "</label><br /></td>").appendTo(u);
		w.p = e("#price-" + p).val(asMoney(new Number(z)));
		w.t = e("#tax-" + p).attr("checked", i == "off" ? true : false)
	};
	w.saleprice = function (i, C) {
		var B, A, z;
		B = e('<th><input type="hidden" name="' + h + '[sale]" value="off" /><input type="checkbox" name="' + h + '[sale]" id="sale-' + p + '" /><label for="sale-' + p + '"> ' + SALE_PRICE_LABEL + "</label></th>").appendTo(v);
		A = e('<td><span class="status">' + NOT_ON_SALE_TEXT + '</span><span class="ui"><input type="text" name="' + h + '[saleprice]" id="saleprice-' + p + '" size="10" class="selectall money right" /></span></td>').appendTo(u);
		z = A.find("span.status");
		A = A.find("span.ui").hide();
		w.sp = e("#saleprice-" + p);
		w.sp.val(asMoney(new Number(C)));
		w.spt = e("#sale-" + p).attr("checked", (i == "on" ? true : false)).toggler(z, A, w.sp)
	};
	w.donation = function (A, z, i, C) {
		var E, B, D, F;
		E = e('<th><label for="price-' + p + '"> ' + AMOUNT_LABEL + "</label></th>").appendTo(v);
		B = e('<td><input type="text" name="' + h + '[price]" id="price-' + p + '" value="0" size="10" class="selectall money right" /><br /><input type="hidden" name="' + h + '[tax]" value="on" /><input type="checkbox" name="' + h + '[tax]" id="tax-' + p + '" value="off" /><label for="tax-' + p + '"> ' + NOTAX_LABEL + "</label><br /></td>").appendTo(u);
		w.p = e("#price-" + p).val(asMoney(new Number(A)));
		w.t = e("#tax-" + p).attr("checked", z == "on" ? false : true);
		D = e("<th />").appendTo(v);
		F = e('<td width="80%"><input type="hidden" name="' + h + '[donation][var]" value="off" /><input type="checkbox" name="' + h + '[donation][var]" id="donation-var-' + p + '" value="on" /><label for="donation-var-' + p + '"> ' + DONATIONS_VAR_LABEL + '</label><br /><input type="hidden" name="' + h + '[donation][min]" value="off" /><input type="checkbox" name="' + h + '[donation][min]" id="donation-min-' + p + '" value="on" /><label for="donation-min-' + p + '"> ' + DONATIONS_MIN_LABEL + "</label><br /></td>").appendTo(u);
		w.dv = e("#donation-var-" + p).attr("checked", i == "on" ? true : false);
		w.dm = e("#donation-min-" + p).attr("checked", C == "on" ? true : false)
	};
	w.shipping = function (G, F, J, z) {
		var E, L, C, B, M, i, I, N, K, A, D = getCurrencyFormat();
		D.precision = "2";
		E = e('<th><input type="hidden" name="' + h + '[shipping]" value="off" /><input type="checkbox" name="' + h + '[shipping]" id="shipping-' + p + '" /><label for="shipping-' + p + '"> ' + SHIPPING_LABEL + "</label></th>").appendTo(v);
		L = e('<td><span class="status">' + FREE_SHIPPING_TEXT + '</span><span class="ui"><input type="text" name="' + h + '[weight]" id="weight-' + p + '" size="8" class="no-field selectall right" /><label class="no-field" for="weight-' + p + '" id="weight-label-' + p + '" title="' + WEIGHT_LABEL + '"> ' + WEIGHT_LABEL + ((weightUnit) ? " (" + weightUnit + ")" : "") + '</label><br /><input type="text" name="' + h + '[shipfee]" id="shipfee-' + p + '" size="8" class="selectall money right" /><label for="shipfee-' + p + '" title="' + SHIPFEE_XTRA + '"> ' + SHIPFEE_LABEL + "</label><br /></span></td>").appendTo(u);
		C = L.find("span.status");
		B = L.find("span.ui").hide();
		if (!F) {
			F = 0
		}
		w.w = e("#weight-" + p).val(formatNumber(new Number(F), D, true)).bind("change.value", function () {
			this.value = formatNumber(this.value, D, true)
		});
		w.fee = e("#shipfee-" + p);
		w.fee.val(asMoney(new Number(J)));
		w.st = E.find("#shipping-" + p).attr("checked", (G == "off" ? false : true)).toggler(C, B, w.w);
		if (dimensionsRequired) {
			A = function (P, Q) {
				var O = this.value;
				if (Q) {
					O = new Number(O)
				}
				this.value = formatNumber(O, D, true)
			};
			e("#weight-label-" + p).html(" " + dimensionUnit + "<sup>3</sup>/" + weightUnit);
			M = e('<div class="dimensions no-field"><div class="inline"><input type="text" name="' + h + '[dimensions][weight]" id="dimensions-weight-' + p + '" size="4" class="selectall right weight" />' + (weightUnit ? "<label>" + weightUnit + "&nbsp;</label>" : "") + '<br /><label for="dimensions-weight-' + p + '" title="' + WEIGHT_LABEL + '"> ' + WEIGHT_LABEL + '</label></div><div class="inline"><input type="text" name="' + h + '[dimensions][length]" id="dimensions-length-' + p + '" size="4" class="selectall right" /><label> x </label><br /><label for="dimensions-length-' + p + '" title="' + LENGTH_LABEL + '"> ' + LENGTH_LABEL + '</label></div><div class="inline"><input type="text" name="' + h + '[dimensions][width]" id="dimensions-width-' + p + '" size="4" class="selectall right" /><label> x </label><br /><label for="dimensions-width-' + p + '" title="' + WIDTH_LABEL + '"> ' + WIDTH_LABEL + '</label></div><div class="inline"><input type="text" name="' + h + '[dimensions][height]" id="dimensions-height-' + p + '" size="4" class="selectall right" /><label>' + dimensionUnit + '</label><br /><label for="dimensions-height-' + p + '" title="' + HEIGHT_LABEL + '"> ' + HEIGHT_LABEL + "</label></div></div>").hide().appendTo(L);
			if (!z) {
				z = {
					weight: 0,
					length: 0,
					width: 0,
					height: 0
				}
			}
			i = e("#dimensions-weight-" + p).val(z.weight).bind("change.value", A).trigger("change.value", true);
			I = e("#dimensions-length-" + p).val(z.length).bind("change.value", A).trigger("change.value", true);
			N = e("#dimensions-width-" + p).val(z.width).bind("change.value", A).trigger("change.value", true);
			K = e("#dimensions-height-" + p).val(z.height).bind("change.value", A).trigger("change.value", true);
			F = w.w;

			function H() {
				F.toggleClass("extoggle");
				M.toggle();
				i.focus();
				var P = 0,
					O = 0;
				M.find("input").each(function (R, Q) {
					if (e(Q).hasClass("weight")) {
						O = asNumber(Q.value)
					}
					else {
						if (P == 0) {
							P = asNumber(Q.value)
						}
						else {
							P *= asNumber(Q.value)
						}
					}
				});
				if (!isNaN(P / O)) {
					F.val((P / O)).trigger("change.value")
				}
			}
			K.blur(H);
			F.click(H);
			F.attr("readonly", true)
		}
	};
	w.inventory = function (i, D, C) {
		var B, A, z;
		B = e('<th><input type="hidden" name="' + h + '[inventory]" value="off" /><input type="checkbox" name="' + h + '[inventory]" id="inventory-' + p + '" /><label for="inventory-' + p + '"> ' + INVENTORY_LABEL + "</label></th>").appendTo(v);
		A = e('<td><span class="status">' + NOT_TRACKED_TEXT + '</span><span class="ui"><input type="text" name="' + h + '[stock]" id="stock-' + p + '" size="8" class="selectall right" /><label for="stock-' + p + '"> ' + IN_STOCK_LABEL + '</label><br /><input type="text" name="' + h + '[sku]" id="sku-' + p + '" size="8" title="' + SKU_XTRA + '" class="selectall" /><label for="sku-' + p + '" title="' + SKU_LABEL_HELP + '"> ' + SKU_LABEL + "</label></span></td>").appendTo(u);
		z = A.find("span.status");
		A = A.find("span.ui").hide();
		if (!D) {
			D = 0
		}
		w.stock = e("#stock-" + p);
		w.stock.val(D).trigger("change.value", function () {
			var E = new Number(this.value);
			this.value = E
		});
		w.sku = e("#sku-" + p);
		w.sku.val(C);
		w.it = B.find("#inventory-" + p).attr("checked", (i == "on" ? true : false)).toggler(z, A, w.stock)
	};
	w.download = function (D, i, z) {
		var C, A, B;
		C = e('<th><label for="download-' + p + '">' + PRODUCT_DOWNLOAD_LABEL + "</label></th>").appendTo(v);
		A = e('<td width="31%"><input type="hidden" name="' + h + '[downloadpath]" id="download_path-' + p + '"/><input type="hidden" name="' + h + '[downloadfile]" id="download_file-' + p + '"/><div id="file-' + p + '">' + NO_DOWNLOAD + "</div></td>").appendTo(u);
		B = e('<td rowspan="2" class="controls" width="75"><button type="button" class="button-secondary" style="white-space: nowrap;" id="file-selector-' + p + '">' + SELECT_FILE_BUTTON_TEXT + "</button></td>").appendTo(v);
		w.file = e("#file-" + p);
		w.selector = e("#file-selector-" + p).FileChooser(p, w.file);
		if (D) {
			if (z.mime) {
				z.mime = z.mime.replace(/\//gi, " ")
			}
			w.file.attr("class", "file " + z.mime).html(i + "<br /><small>" + readableFileSize(z.size) + "</small>").click(function () {
				window.location.href = adminurl + "admin.php?src=download&ecart_download=" + D
			})
		}
	};
	e.fn.toggler = function (i, A, z) {
		this.bind("change.value", function () {
			if (this.checked) {
				i.hide();
				A.show()
			}
			else {
				i.show();
				A.hide()
			}
			if (e.browser.msie) {
				e(this).blur()
			}
		}).click(function () {
			if (e.browser.msie) {
				e(this).trigger("change.value")
			}
			if (this.checked) {
				z.focus().select()
			}
		}).trigger("change.value");
		return e(this)
	};
	w.Shipped = function (i) {
		w.price(i.price, i.tax);
		w.saleprice(i.sale, i.saleprice);
		w.shipping(i.shipping, i.weight, i.shipfee, i.dimensions);
		if (!t) {
			w.inventory(i.inventory, i.stock, i.sku)
		}
	};
	w.Virtual = function (i) {
		w.price(i.price, i.tax);
		w.saleprice(i.sale, i.saleprice);
		if (!t) {
			w.inventory(i.inventory, i.stock, i.sku)
		}
	};
	w.Download = function (i) {
		w.price(i.price, i.tax);
		w.saleprice(i.sale, i.saleprice);
		if (!t) {
			w.download(i.download, i.filename, i.filedata)
		}
	};
	w.Donation = function (i) {
		w.donation(i.price, i.tax, i.donation["var"], i.donation.min)
	};
	b.bind("change.value", function () {
		v.empty();
		u.empty();
		var i = b.val();
		if (i == "Shipped") {
			w.Shipped(y)
		}
		if (i == "Virtual") {
			w.Virtual(y)
		}
		if (i == "Download") {
			w.Download(y)
		}
		if (i == "Donation") {
			w.Donation(y)
		}
		u.find("input.money").bind("change.value", function () {
			this.value = asMoney(this.value)
		}).trigger("change.value");
		quickSelects(u)
	}).trigger("change.value");
	w.disable = function () {
		w.lasttype = (b.val()) ? b.val() : false;
		b.val("N/A").trigger("change.value")
	};
	w.enable = function () {
		if (w.lasttype) {
			b.val(w.lasttype).trigger("change.value")
		}
	};
	if (y && y.context) {
		a.val(y.context)
	}
	else {
		a.val("product")
	}
	w.setOptions = function (i) {
		var z = false;
		if (i) {
			if (i != w.options) {
				z = true
			}
			w.options = i
		}
		if (a.val() == "variation") {
			k.val(xorkey(w.options))
		}
		if (z) {
			w.updateLabel()
		}
	};
	w.updateKey = function () {
		k.val(xorkey(w.options))
	};
	w.updateLabel = function () {
		var A = a.val(),
			i = "",
			z = "";
		if (w.options) {
			if (A == "variation") {
				e(w.options).each(function (B, C) {
					if (i == "") {
						i = e(productOptions[C]).val()
					}
					else {
						i += ", " + e(productOptions[C]).val()
					}
					if (z == "") {
						z = C
					}
					else {
						z += "," + C
					}
				})
			}
			if (A == "addon") {
				i = e(productAddons[w.options]).val();
				z = w.options
			}
		}
		if (i == "") {
			i = DEFAULT_PRICELINE_LABEL
		}
		w.label.val(htmlentities(i)).change();
		f.val(z)
	};
	w.updateTabIndex = function (i) {
		i = new Number(i);
		e.each(w.inputs, function (A, z) {
			e(z).attr("tabindex", ((i + 1) * 100) + A)
		})
	};
	w.linkInputs = function (i) {
		w.links.push(i);
		e.each(w.inputs, function (A, z) {
			if (!z) {
				return
			}
			var B = "change.linkedinputs",
				C = e(z);
			if (C.attr("type") == "checkbox") {
				B = "click.linkedinputs"
			}
			e(z).bind(B, function () {
				var E = e(this).val(),
					D = e(this).attr("checked");
				e.each(w.links, function (F, G) {
					e.each(l.linked[G], function (I, H) {
						if (H == xorkey(w.options)) {
							return
						}
						if (!l.row[H]) {
							return
						}
						if (C.attr("type") == "checkbox") {
							e(l.row[H].inputs[A]).attr("checked", D)
						}
						else {
							e(l.row[H].inputs[A]).val(E)
						}
						e(l.row[H].inputs[A]).trigger("change.value")
					})
				})
			})
		})
	};
	w.unlinkInputs = function (i) {
		if (i !== false) {
			index = e.inArray(i, w.links);
			w.links.splice(index, 1)
		}
		e.each(w.inputs, function (A, z) {
			if (!z) {
				return
			}
			var B = "blur.linkedinputs";
			if (e(z).attr("type") == "checkbox") {
				B = "click.linkedinputs"
			}
			e(z).unbind(B)
		})
	};
	if (b.val() != "N/A") {
		w.inputs = new Array(b, w.p, w.t, w.spt, w.sp, w.dv, w.dm, w.st, w.w, w.fee, w.it, w.stock, w.sku)
	}
	w.updateKey();
	w.updateLabel()
};