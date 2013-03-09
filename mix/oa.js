function isNull(s){
	s=s.replace(/\s/,"");
	if (s.length==0)
		return true;
	return false;
}

function alert_obj(obj)
{
	for(var str in obj){
		alert(str + "= " + eval( "obj."+str))
	}; 
}

//alert(date2str(new Date(),"yyyy-MM-dd hh:mm:ss"));
//alert(date2str(new Date(),"yyyy-M-d h:m:s"));
function date2str(x,y) {
var z = {M:x.getMonth()+1,d:x.getDate(),h:x.getHours(),m:x.getMinutes(),s:x.getSeconds()};
y = y.replace(/(M+|d+|h+|m+|s+)/g,function(v) {return ((v.length>1?"0":"")+eval('z.'+v.slice(-1))).slice(-2)});
return y.replace(/(y+)/g,function(v) {return x.getFullYear().toString().slice(-v.length)});
}

function substr(str, len)
{
    if(!str || !len) { return ''; }
    //预期计数：中文2字节，英文1字节
    var a = 0;
    var i = 0;
    var temp = '';
    for (i=0;i<str.length;i++)
    {
        if (str.charCodeAt(i)>255) 
        {
            a+=2;
        }
        else
        {
            a++;
        }
        if(a > len) { return temp+"..."; }
 
        temp += str.charAt(i);
    }
    return str;
}