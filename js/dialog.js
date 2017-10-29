///////////////////////////////////////////////////////////////////////
//     This script was designed by Erik Arvidsson for WebFX          //
//                                                                   //
//     For more info and examples see: http://webfx.eae.net          //
//     or send mail to erik@eae.net                                  //
//                                                                   //
//     Feel free to use this code as lomg as this disclaimer is      //
//     intact.                                                       //
///////////////////////////////////////////////////////////////////////

// JMM. too slow!!!
var checkZIndex = false;

var dragobject = null;
var tx;
var ty;

var ie5 = document.all != null && document.getElementsByTagName != null;

function getRealDialog(el) {
	temp = el;

	while ((temp != null) && (temp.tagName != "BODY")) {
		if ((temp.className == "moveme") || (temp.className == "handle")){
			el = temp;
			return el;
		}
		temp = temp.parentElement;
	}
	return el;
}


function moveme_onmousedown()
{
	el = getRealDialog(window.event.srcElement)

	if (el.className == "moveme" || el.className == "handle")
	{
		if (el.dragobject)
		{
			dragobject = el.dragobject;
		}
		else
		{
			if (el.className == "handle")
			{
				tmp = el.getAttribute("handlefor");
				if (tmp == null)
				{
					dragobject = null;
					return;
				}
				else
				{
					dragobject = document.getElementById(tmp);
				}
			}
			else
			{
				dragobject = el;
			}

			el.dragobject = dragobject;
		}

		if (checkZIndex) makeOnTop(dragobject);

		ty = window.event.clientY - getTopPos(dragobject);
		tx = window.event.clientX - getLeftPos(dragobject);

		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}
	else
	{
		dragobject = null;
	}
}

function moveme_onmouseup() {
	if(dragobject) {
		dragobject = null;
	}
}

function moveme_onmousemove() {
	if (dragobject) {
		if (window.event.clientX >= 0 && window.event.clientY >= 0) {
			dragobject.style.left = window.event.clientX - tx;
			dragobject.style.top = window.event.clientY - ty;
		}
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}
}

function getLeftPos(el) {
	if (ie5) {
		if (el.currentStyle.left == "auto")
			return 0;
		else
			return parseInt(el.currentStyle.left);
	}
	else {
		return el.style.pixelLeft;
	}
}

function getTopPos(el) {
	if (ie5) {
		if (el.currentStyle.top == "auto")
			return 0;
		else
			return parseInt(el.currentStyle.top);
	}
	else {
		return el.style.pixelTop;
	}
}

function makeOnTop(el) {
	var daiz;
	var max = 0;
	var da = document.all;

	for (var i=0; i<da.length; i++) {
		daiz = da[i].style.zIndex;
		if (daiz != "" && daiz > max)
			max = daiz;
	}

	el.style.zIndex = max + 1;
}


if (document.all) { //This only works in IE4 or better
	document.onmousedown = moveme_onmousedown;
	document.onmouseup = moveme_onmouseup;
	document.onmousemove = moveme_onmousemove;
}

document.write("<style>");
document.write(".moveme		{cursor: move;}");
document.write(".handle		{cursor: move;}");
document.write("</style>");


// JMM

function setDialogVisible(elm, vis)
{
	// elm.style.visibility = vis?"visible":"hidden";
	if (! vis) elm.style.visibility = "hidden";
	for (var i=0; i<hideOnPopup.length; i++)
	{
		hideOnPopup[i].style.visibility = vis?"hidden":"visible";
	}
	if (vis) elm.style.visibility = "visible";
}

function setDialogVisibleById(elmid, vis)
{
	var elm = document.getElementById(elmid);
	setDialogVisible(elm, vis);
}

function setText(parentelm, fieldname, txt)
{
	var id = parentelm.id + "-" + fieldname;
	var elm = document.getElementById(id);
	var str = txt + "&nbsp;";
	elm.innerHTML = str;
}

var hideOnPopup = new Array();
function addToHideOnPopup(elmid)
{
	var elm = document.getElementById(elmid);
	hideOnPopup.push(elm);
}
