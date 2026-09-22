var langs = ['en', 'va', 'es'];
var langCode = '';
var langJS = null;

function translate(data)
{
	$("[tkey]").each (function (index)
	{		
		var strTr = data[$(this).attr ('tkey')];	
	    $(this).html (strTr);
	});
	$("[tkeyholder]").each (function (index)
	{		
		var strTr = data[$(this).attr ('tkeyholder')];	
	    $(this).attr('placeholder',strTr);
	});
}
