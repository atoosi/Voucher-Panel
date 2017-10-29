/*----------------------------------------------------------------------------\
|                           CustomFilter class 0.1                            |
| Adasa Sistemas                                 http://www.adasasistemas.com |
|-----------------------------------------------------------------------------|
| Created 2004-04-16 | jmartinez@adasasistemas.com       | Updated 2004-04-16 |
\----------------------------------------------------------------------------*/

//
function CustomFilter(op, val, trm)
{
	// properties
	this.operator = op;
	this.value = val;
	this.trim = trm;
}



/* -----------------------
    METHODS
   ----------------------- */

//
CustomFilter.prototype.toString = function()
{
	var result =  "CustomFilter " + this.operator + " ";
	if (this.trim)
	{
		result += "trim(" + this.value + ")";
	}
	else
	{
		result += this.value;
	}
	return result;
}

//
CustomFilter.prototype.getNegatedOperator = function(op)
{
	switch (op)
	{
		case "!=":			return "==";
		case ">=":			return "<";
		case "<=":			return ">";
		case "noprefix":	return "prefix";
		case "noinfix":		return "infix";
		case "nosuffix":	return "suffix";
	}
	return null;
}

//
CustomFilter.prototype.checkREValue = function(uvalstr, op, fvalstrraw)
{
	var fvalstr = fvalstrraw.replace(/\*/g, ".*").replace(/\?/g, ".");
	switch (op)
	{
		case "==":
			fvalstr = "^" + fvalstr + "$";
			break;
		case "prefix":
			fvalstr = "^" + fvalstr;
			break;
		case "infix":
			break;
		case "suffix":
			fvalstr = fvalstr + "$";
			break;
	}
	return uvalstr.match(new RegExp(fvalstr, "g"));
}

//
CustomFilter.prototype.checkValue = function(uval, op, fval)
{
	var negop = this.getNegatedOperator(op);
	if (negop != null)
	{
		return !this.checkValue(uval, negop, fval);
	}

	var uvalstr = new String(uval);
	var fvalstr = new String(fval);
	if ((op != "<") && (op != ">") && ((fvalstr.indexOf("*") != -1) || (fvalstr.indexOf("?") != -1)))
	{
		return this.checkREValue(uvalstr, op, fvalstr);
	}

	switch (op)
	{
		case "==":
			return (uval == fval);
		case "<":
			return (uval < fval);
		case ">":
			return (uval > fval);
		case "prefix":
			return (uvalstr.substring(0, fvalstr.length) == fvalstr);
		case "infix":
			return (uvalstr.indexOf(fvalstr) != -1);
		case "suffix":
			return (uvalstr.substring(uvalstr.length - fvalstr.length) == fvalstr);
	}

	alert("InvalidOperatorException: CustomFilter.checkValue");
	return false;
}

//
CustomFilter.prototype.filters = function(valIn)
{
	var val = this.trim?trim(valIn):valIn;
	return !this.checkValue(val, this.operator, this.value);
}



/* -----------------------
    Class DummyFilter
   ----------------------- */
function DummyFilter() {}
DummyFilter.prototype.filters = function() { return false; };



/* -----------------------
    Class GroupFilter
   ----------------------- */

//
function GroupFilter(f1, op, f2)
{
	// properties
	this.vfilters = new Array();
	this.vfilters[0] = f1;
	this.vfilters[1] = f2;
	this.operator = op;
}

//
GroupFilter.prototype.filters = function(valIn)
{
	var result = !this.vfilters[0].filters(valIn);
	for (var i=1; i<this.vfilters.length; i++)
	{
		if (this.operator == "and")
		{
			// logical optimization
			if (! result) return !result;
			result &= !this.vfilters[i].filters(valIn);
		}
		else if (this.operator == "or")
		{
			// logical optimization
			if (result) return !result;
			result |= !this.vfilters[i].filters(valIn);
		}
	}
	return !result;
}
