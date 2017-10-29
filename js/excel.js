/*----------------------------------------------------------------------------\
|                             XLSheet class 0.1                               |
| Adasa Sistemas                                 http://www.adasasistemas.com |
|-----------------------------------------------------------------------------|
| Created 2004-04-15 | jmartinez@adasasistemas.com       | Updated 2004-04-16 |
\----------------------------------------------------------------------------*/

//
function XLSheet(tableId, sortSpec, customFilterId, filterSpec)
{
	var tableElm = document.getElementById(tableId);
	var tableObj = new SortableTable(tableElm);

	// properties
	this.dataTable = tableObj;
	this.numColumns = this.dataTable.tBody.rows[0].cells.length;
	this.filterControls = new Array();
	this.sortedData = new Array();
	this.customFilter = document.getElementById(customFilterId);
	this.filters = new Array();

	// initializations
	for (var i=0; i<this.numColumns; i++)
	{
		if (!filterSpec || (filterSpec.charAt(i) == "1"))
		{
			var selectId = tableId + "__select_" + i;
			var headerCellElm = this.dataTable.tHead.rows[0].cells[i];
			createHeaderCell(headerCellElm, selectId);
			var selectElm = document.getElementById(selectId);
			this.filterControls[i] = selectElm;
			if (selectElm)
			{
				initSelectBox(selectElm);
				selectElm.xl = this;
			}
		}
	}
	this.dataTable.initHeader(sortSpec || []);

	for (var i=0; i<this.filterControls.length; i++)
	{
		var selectElm = this.filterControls[i];
		if (selectElm)
		{
			this.sortedData[i] = sortValues(this.dataTable, i);
			selectElm.populate(this.sortedData[i], i);
		}
		else
		{
			this.sortedData[i] = null;
		}
	}
	this.dataTable.setSortedData(this.sortedData);
	var xlobj = this;
	this.dataTable.onsort = function () { xlobj.onsort(); };
}



/* -----------------------
    METHODS
   ----------------------- */

//
XLSheet.prototype.isRowVisible = function(idx)
{

	var row = this.dataTable.tBody.rows[idx];
	return (row.style.display != "none");
};

//
XLSheet.prototype.onsort = function() {};

//
XLSheet.prototype.onfilter = function() {};

//
XLSheet.prototype.filterChange = function(selectElm)
{
	var filterInfo = selectElm.options[selectElm.selectedIndex].value;
	var column = selectElm.id.split("__")[1].split("_")[1];
	this.customFilter.xl = this;
	var filterObj = createFilter(filterInfo, this.sortedData[column], this.customFilter, this.filters[column], this.dataTable.titles[column]);
	if (filterObj)
	{
		this.filterData(selectElm, filterObj);
	}
	else
	{
		this.filterElement = selectElm;
	}
}

//
XLSheet.prototype.filterData = function(selectElm, filterObj)
{
	var filterInfo = selectElm.options[selectElm.selectedIndex].value;
	var column = selectElm.id.split("__")[1].split("_")[1];
	var tableObj = this.dataTable;
	var data = tableObj.tBody.rows;
	var datalen = data.length;
	this.filters[column] = filterObj;

	for (var i=0; i<datalen; i++)
	{
		var row = data[i];
		var value = tableObj.getRowValue(row, tableObj.getSortType(column), column);
		filter(row, column, value, this.filters[column]);
	}
	selectElm.changeStyle(filterInfo != "special_all");

	for (var i=0; i<this.filterControls.length; i++)
	{
		var fctrl = this.filterControls[i];
		if (fctrl)
		{
			fctrl.populate(this.sortedData[i], i);
		}
	}

	this.onfilter();
}


/* -----------------------
    PRIVATE FUNCTIONS
   ----------------------- */

//
function createFilter(filterInfo, values, customDialog, filterObj, title)
{
	var result = null;
	var filterFunc = filterInfo.split("_");

	if (filterFunc[0] == "value")
	{
		var filterValue = filterInfo.substring(6);
		result = new CustomFilter("==", filterValue, false);
	}
	else if (filterFunc[0] == "special")
	{
		if (filterFunc[1] == "all")
		{
			result = new DummyFilter();
		}
		else if (filterFunc[1] == "top10")
		{
			// hardcode params
			var num = 10;
			var isPercent = false;
			var isTop = true;
			// end hardcode params

			var numElems = isPercent?Math.floor(values.length*num/100):num;
			if (numElems < 1)
			{
				numElems = 1;
			}
			var pivote = isTop?values[values.length-numElems]:values[numElems-1];
			result = new CustomFilter(isTop?">=":"<=", pivote.value, false);
		}
		else if (filterFunc[1] == "custom")
		{
			showCustomFilter(customDialog, title, filterObj);
		}
		else if (filterFunc[1] == "empties")
		{
			result = new CustomFilter("==", "", true);
		}
		else if (filterFunc[1] == "nonempties")
		{
			result = new CustomFilter("!=", "", true);
		}
	}

	return result;
}

//
function createHeaderCell(cellElm, selectId)
{
	var title = cellElm.innerHTML;
	var content = "";
	content += "<span class=\"select\" size=\"1\" id=\"" + selectId + "\" onchange=\"this.xl.filterChange(this);\" style=\"margin-left: 10;\">";
		content += "<table class=\"simpleSelectTable\" cellspacing=\"0\" cellpadding=\"0\">";
			content += "<tr>";
				content += "<td class=\"header-title\" nowrap=\"true\">" + title + "</td>";
				content += "<td align=\"center\" valign=\"middle\" class=\"button\" onclick=\"toggleDropDown(searchParentByClass(this, 'select'))\" onmousedown=\"this.style.border='2 inset buttonhighlight'\" onmouseup=\"this.style.border='2 outset buttonhighlight'\">";
					content += "<span style=\"position: relative; left: 0; top: -2; width: 100%;\">6</span>";
				content += "</td>";
			content += "</tr>";
		content += "</table>";
		content += "<div class=\"dropDown\" onclick=\"optionClick()\" onmouseover=\"optionOver()\" onmouseout=\"optionOut()\"></div>";
	content += "</span>";

	cellElm.className = "header-container";
	cellElm.innerHTML = content;
}

//
function sortValues(tableObj, nColumn)
{
	var sSortType = tableObj.getSortType(nColumn);
	var f = tableObj.getSortFunction(sSortType, nColumn);
	var a = tableObj.getCache(sSortType, nColumn);
	a.sort(f);
	return a;
}

//
function filter(GUIElm, columnIdx, value, filterObj)
{
	if (typeof(GUIElm.filteredby) == "undefined")
	{
		GUIElm.setAttribute("filteredby", new Array());
	}

	GUIElm.filteredby.remove(columnIdx);
	if (filterObj.filters(value))
	{
		GUIElm.filteredby.push(columnIdx);
	}

	GUIElm.style.display = (GUIElm.filteredby.length == 0)?"":"none";
}

//
function doCustomFilter(elmid)
{
	var elm = document.getElementById(elmid);
	var xl = elm.xl;
	var frmname = elm.id + "-frm";
	var frm = document.forms[frmname];

	var tableObj = xl.dataTable;
	var column = xl.filterElement.id.split("__")[1].split("_")[1];
	var sSortType = tableObj.getSortType(column);

	var op1 = frm.op1.options[frm.op1.selectedIndex].value;
	var val1 = tableObj.getValueFromString(frm.val1.value, sSortType);
	var nexo = frm.nexo[0].checked?frm.nexo[0].value:frm.nexo[1].value;
	var op2 = frm.op2.options[frm.op2.selectedIndex].value;
	var val2 = tableObj.getValueFromString(frm.val2.value, sSortType);

	// alert(op1 + val1 + " " + nexo + " " + op2 + val2);
	if (! op1)
	{
		alert("Debe escoger el tipo del primer filtro");
		return;
	}

	var filterObj;
	var f1 = new CustomFilter(op1, val1, false);
	if (op2)
	{
		var f2 = new CustomFilter(op2, val2, false);
		filterObj = new GroupFilter(f1, nexo, f2);
	}
	else
	{
		filterObj = f1;
	}

	setDialogVisible(elm, false);
	xl.filterData(xl.filterElement, filterObj);
}

//
function showCustomFilter(elm, title, filterinfo)
{
	setText(elm, "title", title);

	var frmname = elm.id + "-frm";
	var frm = document.forms[frmname];

	var f1, f2, nexoIdx;
	if (filterinfo && filterinfo.vfilters)

	{
		f1 = filterinfo.vfilters[0];
		f2 = filterinfo.vfilters[1];
		nexoIdx = (filterinfo.operator=="and")?0:1;
	}
	else
	{
		f1 = filterinfo;
		f2 = null;
		nexoIdx = 0;
	}

	frm.op1.selectedIndex = 1;
	frm.val1.value = "";
	frm.nexo[nexoIdx].checked = true;
	frm.op2.selectedIndex = 0;
	frm.val2.value = "";

	if (f1 && f1.operator)
	{
		setSelectValue(frm.op1, f1.operator);
		frm.val1.value = f1.value;
	}
	if (f2 && f2.operator)
	{
		setSelectValue(frm.op2, f2.operator);
		frm.val2.value = f2.value;
	}

	setDialogVisible(elm, true);
}
