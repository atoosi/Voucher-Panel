var overOptionCss = "background: highlight; color: highlighttext";
var sizedBorderCss = "2 inset buttonhighlight";

var globalSelect;	//This is used when calling an unnamed selectbox with onclick="this.PROPERTY"

var ie4 = (document.all != null);

var q = 0;


function initSelectBox(el) {
	copySelected(el);

	var size = el.getAttribute("size");

// These two lines combined with execution in optionClick() allow you to write:
//		onchange="alert(this.options[this.selectedIndex].value)"
	el.options = el.children[1].children;
	el.selectedIndex = findSelected(el);	//Set the index now!
// Some methods that are supported on the real SELECT box
	el.remove = new Function("i", "int_remove(this,i)");
	el.item   = new Function("i", "return this.options[i]");
	el.add    = new Function("e", "i", "int_add(this, e, i)");
	el.addOption = new Function("o", "i", "int_addOption(this, o, i)");
	el.changeStyle = new Function("f", "int_changeStyle(this, f)");
	el.populate = int_populate;
	el.clear = int_clear;
// The real select box let you have lot of options with the same NAME. In that case the item
// needs two arguments. When using DIVs you can't have two with the same NAME (or ID) and
// then there is no need for the second argument

	el.dropdown = el.children[1];

	if (size != null) {
		if (size > 1) {
			el.size = size;
			el.dropdown.style.zIndex = 0;
			initSized(el);
		}
		else {
			el.size = 1;
			el.dropdown.style.zIndex = 99;
			if (el.dropdown.offsetHeight > 200) {
				el.dropdown.style.height = "200";
				el.dropdown.style.overflow = "auto";
			}
		}
	}

	if (el.options.length > 0)
	{
		el.options[el.selectedIndex].selected = true;
		highlightSelected(el,true);
	}
}

function int_clear()
{
	this.dropdown.innerHTML = "";
}

function int_populate(values, columnIdx)
{
	var curValue = null;
	if (this.options.length > 0)
	{
		curValue = this.options[this.selectedIndex].value;
	}

	this.clear();
	this.addOption(new Option("(All)", "special_all"));
	this.addOption(new Option("(Top 10 ...)", "special_top10"));
	this.addOption(new Option("(Custom ...)", "special_custom"));

	var lastValue = null;
	var empties = false;
	for (var i=0; i<values.length; i++)
	{
		var row = values[i].element;
		var numFilters = row.filteredby?row.filteredby.length:0;
		//if (row.style.display == "")
		if ((numFilters == 0) || ((numFilters == 1) && (row.filteredby[0] == columnIdx)))
		{
			var text = row.cells[columnIdx].innerText;
			if (trim(text) == "")
			{
				empties = true;
			}
			else
			{
				var value = values[i].value;
				if (value != lastValue)
				{
					this.addOption(new Option(text, "value_" + value));
					lastValue = value;
				}
			}
		}
	}

	if (empties)
	{
		this.addOption(new Option("(Empty)", "special_empties"));
		this.addOption(new Option("(Not empty)", "special_nonempties"));
	}

	var sel = -1;
	for (var i=0; i<this.options.length; i++)
	{
		if (this.options[i].value == curValue)
		{
			sel = i;
		}
	}
	if (sel == -1)
	{
		sel = 0;
	}

	this.selectedIndex = sel;
	this.options[this.selectedIndex].selected = true;
	highlightSelected(this, true);
}

function int_changeStyle(el, filtered)
{
	var color = filtered?"blue":"black";
	var buttonElm = searchChildByClass(el, "button").children[0];
	buttonElm.style.color = color;
}

function int_remove(el,i) {
	if (el.options[i] != null)
		el.options[i].outerHTML = "";
}

function addHTMLOption(el, html, i)
{
	if ((i == null) || (i >= el.options.length))
		i = el.options.length-1;


	if (el.options.length == 0)
	{
		el.dropdown.innerHTML = html;
	}
	else
	{
		el.options[i].insertAdjacentHTML("AfterEnd", html);
	}
}

function int_add(el, e, i) {
	var html = "<div class='option' noWrap";
	if (e.value != null)
		html += " value='" + e.value + "'";
	if (e.style.cssText != null)
		html += " style='" + e.style.cssText + "'";
	html += ">";
	if (e.text != null)
		html += e.text;
	html += "</div>\n"

	addHTMLOption(el, html, i);
}

function int_addOption(el, o, i) {
	var str = '<div class="option"';
	if (o.value != null)
		str += ' value="' + o.value + '"';
	if (o.css != null)
		str += ' style="' + o.css + '"';
	if (o.selected != null)
		str += ' selected';
	str += '>\n';
	str += o.html;
	str += '</div>\n';

	addHTMLOption(el, str, i);
}

function initSized(el) {
//alert("initSized -------->");
	var h = 0;
	el.children[0].style.display = "none";

	el.dropdown.style.visibility = "visible";

	if (el.dropdown.children.length > el.size) {
		el.dropdown.style.overflow = "auto";
		for (var i=0; i<el.size; i++) {
			h += el.dropdown.children[i].offsetHeight;
		}

		if (el.dropdown.style.borderWidth != null) {
			el.dropdown.style.pixelHeight = h + 4; //2 * parseInt(el.dropdown.style.borderWidth);
		}

		else
			el.dropdown.style.height = h;

	}

	el.dropdown.style.border = sizedBorderCss;


	el.style.height = el.dropdown.style.pixelHeight;
}

function copySelected(el) {
// JMM: commented out
/*
	var selectedIndex = findSelected(el);

	selectedCell = el.children[0].rows[0].cells[0];
	selectedDiv  = 	el.children[1].children[selectedIndex];

	selectedCell.innerHTML = selectedDiv.outerHTML;
*/
}

// This function returns the first selected option and resets the rest
// in case some idiot has set more than one to selcted :-)
function findSelected(el) {
	var selected = null;
	if (typeof(el.children[1]) == "undefined")
	{
		alert("NoSuchElementException: select.js: findSelected");
		return;
	}

	var ec = el.children[1].children;	//the table is the first child
	var ecl = ec.length;

	for (var i=0; i<ecl; i++) {
		if (ec[i].getAttribute("selected") != null) {
			if (selected == null) {	// Found first selected
				selected = i;
			}
			else
				ec[i].removeAttribute("selected");	//Like I said. Only one selected!
		}
	}
	if (selected == null)
		selected = 0;	//When starting this is the most logic start value if none is present

	return selected;
}

function toggleDropDown(el) {
	if (el.size == 1) {
		if (el.dropdown.style.visibility == "")
			el.dropdown.style.visibility = "hidden";

		if (el.dropdown.style.visibility == "hidden")
			showDropDown(el.dropdown);
		else
			hideDropDown(el.dropdown);
	}
}

function optionClick() {
	el = getReal(window.event.srcElement, "className", "option");
	if (el.className != "option") return;

	if (el.className == "option") {
		selectBox = el.parentElement.parentElement;

		oldSelected = selectBox.dropdown.children[findSelected(selectBox)]

		if(oldSelected != el) {
			oldSelected.removeAttribute("selected");
			el.setAttribute("selected", 1);
			selectBox.selectedIndex = findSelected(selectBox);
		}

		if (selectBox.onchange != null) {	// This executes the onchange when you chnage the option
			if (selectBox.id != "") {		// For this to work you need to replace this with an ID or name
				eval(selectBox.onchange.replace(/this/g, selectBox.id));
			}
			else {
				globalSelect = selectBox;
				eval(selectBox.onchange.replace(/this/g, "globalSelect"));
			}
		}

		if (el.backupCss != null)
			el.style.cssText = el.backupCss;
		copySelected(selectBox);
		toggleDropDown(selectBox);
		highlightSelected(selectBox, true);
	}
}

function optionOver() {
	var toEl = getReal(window.event.toElement, "className", "option");
	if (toEl.className != "option") return;
	var fromEl = getReal(window.event.fromElement, "className", "option");
	if (toEl == fromEl) return;
	var el = toEl;

	if (el.className == "option") {
		if (el.backupCss == null)
			el.backupCss = el.style.cssText;
		highlightSelected(el.parentElement.parentElement, false);
		el.style.cssText = el.backupCss + "; " + overOptionCss;
		this.highlighted = true;
	}
}

function optionOut() {
	var toEl = getReal(window.event.toElement, "className", "option");
	var fromEl = getReal(window.event.fromElement, "className", "option");
	if (fromEl.className != "option") return;

	if (fromEl == fromEl.parentElement.children[findSelected(fromEl.parentElement.parentElement)]) {
		if (toEl == null)
			return;
		if (toEl.className != "option")
			return;
	}

	if (toEl != null) {
		if (toEl.className != "option") {
			if (fromEl.className == "option")
				highlightSelected(fromEl.parentElement.parentElement, true);
		}
	}

	if (toEl == fromEl) return;
	var el = fromEl;

	if (el.className == "option") {
		if (el.backupCss != null)
			el.style.cssText = el.backupCss;
	}

}

function highlightSelected(el,add) {
	var selectedIndex = findSelected(el);

	selected = el.children[1].children[selectedIndex];

	if (add) {
		if (selected.backupCss == null)
			selected.backupCss = selected.style.cssText;
		selected.style.cssText = selected.backupCss + "; " + overOptionCss;
	}
	else if (!add) {
		if (selected.backupCss != null)
			selected.style.cssText = selected.backupCss;
	}
}

function hideShownDropDowns() {
	var el = getReal(window.event.srcElement, "className", "select");

	var spans = document.all.tags("SPAN");
	var selects = new Array();
	var index = 0;

	for (var i=0; i<spans.length; i++) {
		if ((spans[i].className == "select") && (spans[i] != el)) {
			dropdown = spans[i].dropdown;
			if ((spans[i].size == 1) && (dropdown.style.visibility == "visible"))
				selects[index++] = dropdown;
		}
	}

	for (var j=0; j<selects.length; j++) {
		hideDropDown(selects[j]);
	}

}

function hideDropDown(el) {
	if (typeof(fade) == "function")
		fade(el, false);
	else
		el.style.visibility = "hidden";
}

function showDropDown(el) {
	if (typeof(fade) == "function")
		fade(el, true);
	else if (typeof(swipe) == "function")
		swipe(el, 2);
	else
		el.style.visibility = "visible";
}

function initSelectBoxes() {
	var spans = document.all.tags("SPAN");
	var selects = new Array();
	var index = 0;

	for (var i=0; i<spans.length; i++) {
		if (spans[i].className == "select")
			selects[index++] = spans[i];
	}

	for (var j=0; j<selects.length; j++) {
		initSelectBox(selects[j]);
	}
}

function getReal(el, type, value) {
	temp = el;
	while ((temp != null) && (temp.tagName != "BODY")) {
		if (eval("temp." + type) == value) {
			el = temp;
			return el;
		}
		temp = temp.parentElement;
	}
	return el;
}


// global hook
document.onclick = hideShownDropDowns;
