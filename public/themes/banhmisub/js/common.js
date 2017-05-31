function UnFortmatPrice(str){
	if(str!=undefined){
		str = str.replace("$","");
		str = str.replace(/,/g,"");
		str = parseFloat(str);
		return str;
	}else return 0;
}

function FortmatPrice(values){
	values = parseFloat(values);
	values = values.formatMoney(2, '.', ',');
	return values;
}
Number.prototype.formatMoney = function(c, d, t){
var n = this,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

function alertBox(text){
	
}
function closeAlertBox(text){
	
}
function playmusic(){
	document.getElementById('play').play();
}