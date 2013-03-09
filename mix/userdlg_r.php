<?php
require_once "../maincore.php";
require_once THEME."theme.php";
$level=0;
$id=0;
if(isset($_GET['id']))
	$id=$_GET['id'];
if(isset($_POST['id']))
	$id=$_POST['id'];

if(isset($_GET['level']))
	$level=$_GET['level'];
if(isset($_POST['level']))
	$level=$_POST['level'];

function showdeptment()
{
	global $db_connect, $id, $level;
$str_utf8="<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
$rst = mysql_query("SELECT a.name,a.id,a.socso,b.user_name,b.user_id FROM ".DB_PREFIX."emp_member as a LEFT JOIN ".DB_PREFIX."users as b ON a.socso=b.user_name WHERE a.dept=$id ORDER BY idx",$db_connect);
$i=0;
while ($row = mysql_fetch_assoc($rst))
{
	if(($i%5)==0)
		$str_utf8.="<TR><TD><IMG SRC=\"\" WIDTH=\"".($level*12)."\" HEIGHT=\"1px\"></TD>";
	$str_utf8.="<TD nowrap><INPUT TYPE=\"radio\"".($row['user_name']?"":" DISABLED")." NAME=\"emp\" VALUE=\"".$row['user_id']."\">".$row['name']."</TD>";
	if(($i%5)==4)
		$str_utf8.="</TR>";
	$i++;
}
mysql_free_result($rst);
if($i%5)
{
	while($i%5)
	{
		$str_utf8.="<td />";
		$i++;
	}
	$str_utf8.="</tr>";
}
$str_utf8.="</TABLE>";
if($id!=0)
	$str_utf8 = iconv("GBK", "UTF-8",$str_utf8);
echo $str_utf8;

$rst = mysql_query("SELECT name,id FROM ".DB_PREFIX."emp_dept WHERE parent=$id order by idx",$db_connect);
while ($row = mysql_fetch_assoc($rst))
{
	$str_utf8 = sprintf("<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
<TR>
	<TD nowrap><IMG SRC=\"\" WIDTH=\"%d\" HEIGHT=\"1px\"></TD>
	<TD nowrap onclick=\"javascript:showdep('%d');\" STYLE=\"cursor:hand\"><IMG SRC=\"images/folder.gif\">%s</TD>
</TR>
</TABLE><div ID=\"d%d\" style=\"display:none\" LEVEL=\"%d\"></div>",
12*$level, $row['id'], $row['name'], $row['id'], $level+1);
	if($id!=0)
		$str_utf8 = iconv("GBK", "UTF-8",$str_utf8);
	echo $str_utf8;
}
mysql_free_result($rst);
}

if($id==0)
{
require_once "../header.php";
echo "<body bgcolor='$body_bg' text='$body_text'>\n";
?>
<script language="javascript" src="ajax.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ok()
{
	var myObject = new Object();
	myObject.name = "";
	myObject.id = "";
	emps = document.getElementsByName("emp");
	if(emps)
	{
		for(i=0; i<emps.length; i++)
		{
			if(emps[i].checked)
			{
				myObject.name = emps[i].parentElement.innerText;
				myObject.id = emps[i].value;
				break;
			}
		}
	}
	//window.returnValue = "asdf";
	window.returnValue = myObject;
	window.close();
}
function showdep(id)
{
	elm = document.getElementById("d"+id);
	if(elm)
	{
		if(elm.style.display=='')
		{
			elm.style.display='none';
		}
		else
		{
			elm.style.display='';
			if(elm.innerHTML.length==0)
			{
				var lev = elm.getAttribute("LEVEL");
				var wid = 12*lev;
				elm.innerHTML="<img src='' width=\""+wid.toString()+"\" height=\"1px\">正在读取......";
				ajaxpage("userdlg_r.php?id="+id+"&level="+lev, "d"+id);
			}
		}
	}
}
$(function(){
	$("body").css("overflow","hidden"); //chrome only
});
//-->
</SCRIPT>

<form>
<table border=0 height="100%">

<tr >
<td width='380px' valign="top"><div style="width:100%;height:230px;overflow-y:auto;overflow-x:hidden">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td>
<?php
}
showdeptment();
if($id==0)
{
?>
</td></tr>
</table>
</div>
</td>
</tr>
<tr height='30px'>
<td align='center'>
<input type="button" value=" 确定 " class="button" onclick="ok()">
<input type="button" value=" 取消 " class="button" onclick="window.close()">
</td>
</tr>
</table>
</form>

</BODY>
</HTML><?php
}
?>