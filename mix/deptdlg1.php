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

// r:radio	c:checkbox
if(!isset($_GET['type']))
	$_GET['type']='c';
if ($_GET['type']=='r')
	$s_fun='r_ok()';
if ($_GET['type']=='c')
	$s_fun='c_ok()';

function showdeptment()
{
	global $db_connect, $id, $level;
	
	
	$rst = mysql_query("SELECT name,id FROM ".DB_PREFIX."emp_dept WHERE parent=$id order by idx",$db_connect);
	while ($row = mysql_fetch_assoc($rst))
	{
		//if (dbcount("(*)","emp_dept","parent=".$row['id']))
		if (dbrows(dbquery("select * from ".DB_PREFIX."emp_dept where parent=".$row['id'])))
		{
			if ($_GET['type']=="r")
				$str_t=sprintf("<TD nowrap onclick=\"javascript:showdep('%d');\" STYLE=\"cursor:hand\"><INPUT TYPE=\"radio\" name=\"r_dept\" value=\"%d\"><IMG SRC=\"images/folder.gif\">%s</TD><input type='hidden' id=\"did%d\" value=\"%s\">",$row['id'],$row['id'],$row['name'],$row['id'],$row['name']);
			if ($_GET['type']=="c")
				$str_t=sprintf("<TD nowrap onclick=\"javascript:showdep('%d');\" STYLE=\"cursor:hand\"><INPUT TYPE=\"checkbox\" name=\"c_dept\" value=\"%d\"><IMG SRC=\"images/folder.gif\">%s<input type='hidden' id=\"did%d\" value=\"%s\"></TD>",$row['id'],$row['id'], $row['name'],$row['id'],$row['name']);
		}
		else
		{
			if ($_GET['type']=="r")
				$str_t=sprintf("<TD nowrap><INPUT TYPE=\"radio\" name=\"r_dept\" value=\"%d\"><IMG SRC=\"images/folder.gif\">%s<input type='hidden' id=\"did%d\" value=\"%s\"></TD>", $row['id'],$row['name'],$row['id'],$row['name']);

			if ($_GET['type']=="c")
				$str_t=sprintf("<TD nowrap><INPUT TYPE=\"checkbox\" name=\"c_dept\" value=\"%d\"><IMG SRC=\"images/folder.gif\">%s<input type='hidden' id=\"did%d\" value=\"%s\"></TD>", $row['id'],$row['name'],$row['id'],$row['name']);
		}
		$str_utf8 = sprintf("<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><TR>
		<TD nowrap width='%dpx'></TD>".$str_t."</TR>
		</TABLE><div ID=\"d%d\" style=\"display:none\" LEVEL=\"%d\"></div>",
		12*$level,  $row['id'], $level+1);
		
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
//echo "<table><tr><td><INPUT TYPE=\"checkbox\" name=\"c_dept\" value=\"0\"><IMG SRC=\"images/folder.gif\"><b>所有部门</b><input type='hidden' name=\"did0\" value=\"所有部门\"></table>";

?>
<script language="javascript" src="ajax.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GetObj(objName)
{
	if(document.getElementById){
		return eval('document.getElementById("' + objName + '")');
	}else if(document.layers){
		return eval("document.layers['" + objName +"']");
	}else{
		return eval('document.all.' + objName);
	}
}

//返回用户列表，以“,”号分隔
function c_ok()
{
	var myObject = new Object();
	myObject.name = "";
	myObject.id = "";
	emps = document.getElementsByName("c_dept");
	if(emps)
	{
		for(i=0; i<emps.length; i++)
		{
			if(emps[i].checked)
			{
				if(myObject.id.length<1)
				{

					//myObject.name = emps[i].parentElement.innerText;
					myObject.id = emps[i].value;
					d_id=document.getElementById("did"+emps[i].value);
					myObject.name=d_id.value;

				}
				else
				{
					//myObject.name = myObject.name+","+emps[i].parentElement.innerText;
					myObject.id = myObject.id+","+emps[i].value;
					d_id=document.getElementById("did"+emps[i].value);
					myObject.name=myObject.name+","+d_id.value;

				}
				//window.alert(myObject.name);
				//break;
			}
		}
	}
	window.returnValue = myObject;
	window.close();
}


function r_ok()
{
	var myObject = new Object();
	myObject.name = "";
	myObject.id = "";
	emps = document.getElementsByName("r_dept");
	if(emps)
	{
		for(i=0; i<emps.length; i++)
		{
			if(emps[i].checked)
			{
				//myObject.name = emps[i].parentElement.innerText;				
				myObject.id = emps[i].value;
				d_id=document.getElementById("did"+emps[i].value);
				myObject.name=d_id.value;
				break;
			}
		}
	}
	//alert(myObject.name );
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
				elm.innerHTML="<img src='' width=\""+wid.toString()+"\" height=\"1\">正在读取......";
				ajaxpage("deptdlg1.php?type=<?php echo $_GET['type'] ?>&id="+id+"&level="+lev, "d"+id);
			}
		}
	}
}

function selectdep(id)
{
    var a=event.srcElement;
	elm = GetObj("d"+id);
	emps = elm.getElementsByTagName("INPUT");
	if(emps)
	{
		for(i=0; i<emps.length; i++)
		{
			if(!emps[i].disabled)
			{
				if(a.checked)
					emps[i].checked = true;
				else
					emps[i].checked = false;
			}
		}
	}
}

//-->
</SCRIPT>

<form>
<table border=0 height="100%">
<tr height=250>
<td width='380px'><div style="width:100%;height:250px;overflow-y:auto;overflow-x:hidden">
<table><tr><td><INPUT TYPE="checkbox" name="c_dept" value="0"><IMG SRC="images/folder.gif"><b>所有部门</b><input type='hidden' name="did0" id='did0' value="所有部门"></table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

<?php
}
echo "<tr><td>";
showdeptment();
if($id==0)
{
?>
</td></tr>
</table>


</div>
</td>
</tr>
<tr height=30>
<td align=center>
<input type="button" value=" 确定 " class="button" onclick="<?php echo $s_fun;?>">
<input type="button" value=" 取消 " class="button" onclick="window.close()">
</td>
</tr>
</table>
</form>

</BODY>
</HTML><?php
}
?>