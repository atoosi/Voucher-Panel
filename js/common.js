//
function trim(unTexto)
{
	var unString = new String(unTexto);
	return unString.replace(/(^\s*)|(\s*$)/g, "");
}

//
function setSelectValue(sel, val)
{
	for (var i=0; i<sel.options.length; i++)
	{
		if (sel.options[i].value == val)
		{
			sel.selectedIndex = i;
			return;
		}
	}
}

//
function searchParentByClass(elm, className)
{
	if ((elm == null) || (elm.className == className))
	{
		return elm;
	}
	return searchParentByClass(elm.parentElement, className);
}

//
function searchChildByClass(elm, className)
{
	if ((elm == null) || (elm.className == className))
	{
		return elm;
	}
	var arr = elm.children;
	if (arr)
	{
		var arrlen = arr.length;
		for (var i=0; i<arrlen; i++)
		{
			var result = searchChildByClass(arr[i], className);
			if (result != null)
			{
				return result;
			}
		}
	}
	return null;
}

//
Array.prototype.contains = function(elm)
{
	for (var i=0; i<this.length; i++)
	{
		if (this[i] == elm)
		{
			return true;
		}
	}
	return false;
}

//
Array.prototype.remove = function(elm)
{
	var i=0;
	while (i<this.length)
	{
		if (this[i] == elm)
		{
			this.splice(i, 1);
		}
		else
		{
			i++;
		}
	}
}

//
function Option(html, value, css, selected)
{
	this.html = html;
	this.value = value;
	this.css = css;
	this.selected = selected;
}

//
function addClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var l = p.length;
	for (var i = 0; i < l; i++) {
		if (p[i] == sClassName)
			return;
	}
	p[p.length] = sClassName;
	el.className = p.join(" ").replace( /(^\s+)|(\s+$)/g, "" );
}

//
function removeClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++) {
		if (p[i] != sClassName)
			np[j++] = p[i];
	}
	el.className = np.join(" ").replace( /(^\s+)|(\s+$)/g, "" );
}
