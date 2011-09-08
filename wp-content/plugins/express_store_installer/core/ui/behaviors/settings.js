/*
 * settings.js - Module settings UI library
 */
function ModuleSetting(e, d, c, g) {
	var f = jqnc(),
		b = this,
		a = 0;
	b.name = d;
	b.module = e;
	b.label = c;
	b.settings = new Array();
	b.ui = new Array();
	b.tables = new Array();
	b.columns = new Array();
	b.settingsTable = false;
	b.deleteButton = false;
	b.multi = g;
	b.payment = function () {
		if (b.label instanceof Array) {
			if (b.label[a]) {
				c = b.label[a]
			}
			else {
				c = b.name
			}
		}
		else {
			c = b.label
		}
		var o = c.toLowerCase().replace(/[^\w+]/, "-"),
			i = "settings[" + b.module + "]" + (b.multi ? "[label][" + a + "]" : "[label]"),
			l = '<th scope="row"><label>' + b.name + '</label><br /><input type="text" name="' + i + '" value="' + c + '" id="' + o + '-label" size="16" class="selectall" /><br /><small><label for="' + o + '-label">' + ECART_PAYMENT_OPTION + "</label></small></th>",
			m = f("<tr />").html(l).appendTo("#payment-settings"),
			k = f("<td/>").appendTo(m),
			n = f('<button type="button" name="deleteRate" class="delete deleteRate"><img src="' + ECART_PLUGINURI + '/core/ui/icons/delete.png" width="16" height="16" /></button>').appendTo(k).hide(),
			h = f("html").css("background-color"),
			j = "#fff";
		f("#active-gateways").val(f("#active-gateways").val() + "," + b.module);
		m.hover(function () {
			n.show()
		}, function () {
			n.hide()
		});
		n.hover(function () {
			m.animate({
				backgroundColor: j
			}, 250)
		}, function () {
			m.animate({
				backgroundColor: h
			}, 250)
		});
		n.click(function () {
			if (confirm(ECART_DELETE_PAYMENT_OPTION)) {
				m.remove();
				gateways = f("#active-gateways").val().split(",");
				var p = f.inArray(b.module, gateways);
				gateways.splice(p, 1);
				f("#active-gateways").val(gateways.join());
				f("#payment-option-menu option[value=" + b.module + "]").attr("disabled", false)
			}
		});
		b.tables[a] = f('<table class="settings"/>').appendTo(k);
		f.each(b.settings, function (s, r) {
			var q, p;
			if (b.multi) {
				q = new SettingInput(b.module, r.attrs, r.options, a)
			}
			else {
				q = new SettingInput(b.module, r.attrs, r.options)
			}
			p = q.generate();
			f(p).appendTo(b.column(r.target, a));
			if (q.type == "multimenu") {
				q.selectall()
			}
		});
		if (b.multi) {
			a++;
			if (b.label instanceof Array && b.label[a]) {
				b.payment()
			}
		}
	};
	b.shipping = function () {
		if (b.label instanceof Array) {
			if (b.label[a]) {
				c = b.label[a]
			}
			else {
				c = b.name
			}
		}
		else {
			c = b.label
		}
		var o = c.toLowerCase().replace(/[^\w+]/, "-"),
			i = "settings[" + b.module + "]" + (b.multi ? "[label][" + a + "]" : "[label]"),
			l = '<th scope="row"><label>' + b.name + '</label><br /><input type="text" name="' + i + '" value="' + c + '" id="' + o + '-label" size="16" class="selectall" /><br /><small><label for="' + o + '-label">' + ECART_PAYMENT_OPTION + "</label></small></th>",
			m = f("<tr />").html(l).appendTo("#payment-settings"),
			k = f("<td/>").appendTo(m),
			n = f('<button type="button" name="deleteRate" class="delete deleteRate"><img src="' + ECART_PLUGINURI + '/core/ui/icons/delete.png" width="16" height="16" /></button>').appendTo(k).hide(),
			h = f("html").css("background-color"),
			j = "#000";
		f("#active-gateways").val(f("#active-gateways").val() + "," + b.module);
		m.hover(function () {
			n.show()
		}, function () {
			n.hide()
		});
		n.hover(function () {
			m.animate({
				backgroundColor: j
			}, 250)
		}, function () {
			m.animate({
				backgroundColor: h
			}, 250)
		});
		n.click(function () {
			if (confirm(ECART_DELETE_PAYMENT_OPTION)) {
				m.remove();
				gateways = f("#active-gateways").val().split(",");
				var p = f.inArray(b.module, gateways);
				gateways.splice(p, 1);
				f("#active-gateways").val(gateways.join());
				f("#payment-option-menu option[value=" + b.module + "]").attr("disabled", false)
			}
		});
		b.tables[a] = f('<table class="settings"/>').appendTo(k);
		f.each(b.settings, function (s, r) {
			var q, p;
			if (b.multi) {
				q = new SettingInput(b.module, r.attrs, r.options, a)
			}
			else {
				q = new SettingInput(b.module, r.attrs, r.options)
			}
			p = q.generate();
			f(p).appendTo(b.column(r.target, a));
			if (q.type == "multimenu") {
				q.selectall()
			}
		});
		if (b.multi) {
			a++;
			if (b.label[a]) {
				b.payment()
			}
		}
	};
	b.storage = function () {
		f.each(b.settings, function (k, j) {
			j.attrs.setting = b.setting;
			var i, h = new SettingInput(b.module, j.attrs, j.options);
			h.name += "[" + b.setting + "]";
			h.id += "-" + b.setting;
			if (j.attrs.value) {
				if (j.attrs.value instanceof Object) {
					if (j.attrs.value[b.setting]) {
						h.value = j.attrs.value[b.setting]
					}
					else {
						h.value = ""
					}
				}
				else {
					h.value = j.attrs.value
				}
			}
			else {
				h.value = ""
			}
			if (j.attrs.selected) {
				if (j.attrs.selected instanceof Object) {
					if (j.attrs.selected[b.setting]) {
						h.selected = j.attrs.selected[b.setting]
					}
					else {
						h.selected = 0
					}
				}
				else {
					h.selected = j.attrs.selected
				}
			}
			else {
				h.selected = 0
			}
			i = h.generate();
			f(i).appendTo(b.element);
			if (h.type == "multimenu") {
				h.selectall()
			}
		})
	};
	b.newInput = function (k, j, i) {
		var h = {
			target: k,
			attrs: j,
			options: i
		};
		b.settings.push(h)
	};
	b.column = function (i, h) {
		if (!b.columns[h]) {
			b.columns[h] = new Array()
		}
		if (!b.columns[h][i]) {
			return b.columns[h][i] = f("<td/>").appendTo(b.tables[h])
		}
		else {
			return b.columns[h][i]
		}
	};
	b.behaviors = function () {}
}

function SettingInput(e, c, b, g) {
	var f = jqnc(),
		a = this,
		d = new Array("text", "password", "hidden", "checkbox", "menu", "textarea", "multimenu", "p", "button");
	if (!c.name) {
		return ""
	}
	a.type = (f.inArray(c.type, d) != -1) ? c.type : "text";
	a.name = "settings[" + e + "][" + c.name + "]";
	if (g !== undefined) {
		a.name += "[" + g + "]"
	}
	if (c.value) {
		if (c.value instanceof Array) {
			if (c.value[g]) {
				a.value = c.value[g]
			}
			else {
				a.value = ""
			}
		}
		else {
			a.value = c.value
		}
	}
	else {
		a.value = ""
	}
	a.normal = (c.normal) ? c.normal : "";
	a.keyed = (c.keyed) ? (c.keyed == "true" ? true : false) : true;
	a.selected = (c.selected) ? c.selected : false;
	a.checked = (c.checked) ? c.checked : false;
	a.readonly = (c.readonly) ? "readonly" : false;
	a.size = (c.size) ? c.size : "20";
	a.cols = (c.size) ? c.size : "40";
	a.rows = (c.size) ? c.size : "3";
	a.classes = (c.classes) ? c.classes : "";
	a.id = (c.id) ? c.id : "settings-" + e.toLowerCase().replace(/[^\w+]/, "-") + "-" + c.name.toLowerCase();
	if (g !== undefined) {
		a.id += "-" + g
	}
	a.options = b;
	a.content = (c.content) ? c.content : "";
	a.label = (c.label) ? c.label : false;
	if (a.label instanceof Object && c.setting) {
		a.label = c.label[c.setting]
	}
	a.generate = function () {
		if (!a.name) {
			return
		}
		if (a.type == "p") {
			return a.paragraph()
		}
		if (a.type == "button") {
			return a.button()
		}
		if (a.type == "checkbox") {
			return a.checkbox()
		}
		if (a.type == "menu") {
			return a.menu()
		}
		if (a.type == "multimenu") {
			return a.multimenu()
		}
		if (a.type == "textarea") {
			return a.textarea()
		}
		return a.text()
	};
	a.text = function () {
		var h = (a.readonly) ? ' readonly="readonly"' : "",
			i = '<div><input type="' + a.type + '" name="' + a.name + '" value="' + a.value + '" size="' + a.size + '" class="' + a.classes + '" id="' + a.id + '"' + h + " />";
		if (a.label) {
			i += '<br /><label for="' + a.id + '">' + a.label + "</label></div>\n"
		}
		return i
	};
	a.textarea = function () {
		var h = '<div><textarea name="' + a.name + '" cols="' + a.cols + '" rows="' + a.rows + '" class="' + a.classes + '" id="' + a.id + '">' + a.value + "</textarea>";
		if (a.label) {
			h += '<br /><label for="' + a.id + '">' + a.label + "</label></div>\n"
		}
		return h
	};
	a.checkbox = function () {
		var h = '<div><label for="' + a.id + '">';
		h += '<input type="hidden" name="' + a.name + '" value="' + a.normal + '" id="' + a.id + '-default" />';
		h += '<input type="' + a.type + '" name="' + a.name + '" value="' + a.value + '" class="' + a.classes + '" id="' + a.id + '"' + ((a.checked) ? ' checked="checked"' : "") + " />";
		if (a.label) {
			h += "&nbsp;" + a.label
		}
		h += "</label></div>\n";
		return h
	};
	a.menu = function () {
		var i, l, k = a.selected,
			h = a.keyed,
			j = "<div>";
		j += '<select name="' + a.name + '" class="' + a.classes + '" id="' + a.id + '">';
		if (a.options) {
			f.each(a.options, function (n, m) {
				l = (h && n !== false) ? ' value="' + n + '"' : "";
				i = ((h && k == n) || k == m) ? ' selected="selected"' : "";
				j += "<option" + l + i + ">" + m + "</option>"
			})
		}
		j += "</select>";
		if (a.label) {
			j += '<br /><label for="' + a.id + '">' + a.label + "</label></div>\n"
		}
		return j
	};
	a.multimenu = function () {
		var h = '<div><div class="multiple-select">',
			j = true,
			i = a.selected;
		h += '<ul id="' + a.id + '" class="' + a.classes + '">';
		if (a.options) {
			h += '<li><input type="checkbox" name="select-all" id="' + a.id + '-select-all" class="selectall" /><label for="' + a.id + '-select-all"><strong>' + ECART_SELECT_ALL + "</strong></label></li>";
			f.each(a.options, function (l, k) {
				var n = a.id + "-" + k.toLowerCase().replace(/[^\w]/, "-"),
					m = "";
				if (f.inArray(l, i) != -1) {
					m = ' checked="true"'
				}
				h += "<li" + (j ? ' class="odd"' : "") + ">";
				h += '<input type="checkbox" name="' + a.name + '[]" value="' + l + '" id="' + n + '"' + m + " />";
				h += '<label for="' + n + '">' + k + "</label>";
				h += "</li>";
				j = !j
			})
		}
		h += "</ul></div>";
		if (a.label) {
			h += '<br /><label for="' + a.id + '">' + a.label + "</label></div>\n"
		}
		return h
	};
	a.button = function () {
		classes = (a.classes) ? ' class="button-secondary ' + a.classes + '"' : ' class="button-secondary"';
		type = (a.type) ? ' type="' + a.type + '"' : "";
		var h = "<div><button" + type + ' name="' + a.name + '" value="' + a.value + '" id="' + a.id + '"' + classes + ">" + a.label + "</button></div>\n";
		return h
	};
	a.paragraph = function () {
		var j = (a.id) ? ' id="' + a.id + '"' : "",
			i = (a.classes) ? ' class="' + a.classes + '"' : "",
			h = "";
		if (a.label) {
			h += "<label><strong>" + a.label + "</strong></label>"
		}
		h += "<div" + j + i + ">" + a.content + "</div>";
		return h
	};
	a.selectall = function () {
		var h = a.id;
		f("#" + h + "-select-all").change(function () {
			if (this.checked) {
				f("#" + h + " input").attr("checked", true)
			}
			else {
				f("#" + h + " input").attr("checked", false)
			}
		})
	}
};