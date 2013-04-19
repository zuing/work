<?php
require "../maincore.php";
require "../header.php";
require_once THEME."theme.php";

if (!iMEMBER)
{
	echo "请登陆后查看";
	exit;
}
$wk_id=(int)$_GET['wk_id'];

if (isset($_POST['act']))
{
	if ($_POST['act']=="send_submit")
	{
		$sql = sprintf("update %swork_item set wk_submit=1,wk_lasttime=%d where wk_id=%d",DB_PREFIX,time(),$_GET['wk_id']);
		dbquery($sql);
		work_sms($wk_id,"工作督办:一项工作已经提交申请结束");
	}
	if ($_POST['act']=="back_submit")
	{
		$sql = sprintf("update %swork_item set wk_submit=0,wk_lasttime=%d where wk_id=%d",DB_PREFIX,time(),$wk_id);
		dbquery($sql);
		work_sms($wk_id,"工作督办:一项申请结束事项被退回");
	}
	if ($_POST['act']=="del_result")
	{
		$sql = sprintf("update %swork_item set wk_submit=1,wk_status=1,wk_lasttime=%d where wk_id=%d",DB_PREFIX,time(),$wk_id);
		dbquery($sql);
		$sql = sprintf("delete from %swork_result where wk_id=%d",DB_PREFIX,$wk_id);
		dbquery($sql);
		work_sms($wk_id,"工作督办:督办意见已经撤回");

	}

	if ($_POST['act']=="rst_submit")
	{
		$sql = sprintf("insert into %swork_result (wk_id,wk_result,wk_uid,wk_uname,wk_datetime) values(%d,'%s',%d,'%s',%d)",
			DB_PREFIX,
			$wk_id,
			htmlspecialchars($_POST['wk_text_rst'],ENT_QUOTES),
			$userdata['user_id'],
			$userdata['name'],
			time()
		);
		dbquery($sql);

		$sql = sprintf("update %swork_item set wk_status=%d,wk_submit=2,wk_lasttime=%d where wk_id=%d",DB_PREFIX,$_POST['rst_status'],time(),$wk_id);
		dbquery($sql);

		work_sms($wk_id,"工作督办:有一条新的督办意见");
	}

	if ($_POST['act']=="ok")
	{
		$sql = sprintf("select wk_aid from %swork_dept where wk_id=%d and wk_flag=%d and wk_uid=%d",DB_PREFIX,$wk_id,$_POST['wk_flag'],$userdata['user_id']);
		$wk_aid = dbdata($sql,0,0);
	
		if ($wk_aid>0)
		{
			$sql = sprintf("insert into %swork_process (wk_aid,wk_process,wk_uid,wk_uname,wk_datetime) values(%d,'%s',%d,'%s',%d)",
				DB_PREFIX,
				$wk_aid,
				htmlspecialchars($_POST['wk_p'],ENT_QUOTES),
				$userdata['user_id'],
				$userdata['name'],
				time()
			);

			dbquery($sql);
		}
		$wk_pid = mysql_insert_id();

		$sql = sprintf("update %swork_item set wk_lasttime=%d where wk_id=%d",DB_PREFIX,time(),$wk_id);
		dbquery($sql);

		//保存附件
		$cache_id=array();
		$s= iconv("GBK", "UTF-8", $_POST['h_file']);//转换成UTF-8才能被json_decode
		$json_arr=json_decode($s);

		for ($i=0;$i<count($json_arr);$i++)
		{
			//$attach_name[$i]= iconv("UTF-8", "GBK", $json_arr[$i]->name);
			$sql="select * from ".DB_PREFIX."work_cache where attach_hash='".$json_arr[$i]->md5."'";
			$rst_cache=dbquery($sql);
			if (dbrows($rst_cache))
			{
				$data_cache=dbarray($rst_cache);
				$cache_id[]=$data_cache['attach_id'];
			}
			else
			{
				$strsql="INSERT INTO ".$db_prefix."work_cache (attach_id, attach_name, attach_datestamp, attach_size, attach_hash) VALUES(NULL,'".addslashes(iconv("UTF-8", "GBK", $json_arr[$i]->name))."', ".time().", ".$json_arr[$i]->size.", '".$json_arr[$i]->md5."')";
				dbquery($strsql);
				$cache_id[]=mysql_insert_id($db_connect);
			}

		}//end 保存附件
		for ($i=0;$i<count($cache_id);$i++)
		{
			$sql=sprintf("insert into %swork_attach (id,attach_id,cat) values(%d,%d,'wk_process')",DB_PREFIX,$wk_pid,$cache_id[$i]);
			dbquery($sql);
		}

		//send sms
		work_sms($wk_id,"工作督办:有一条新的工作进展");

	}
	echo "<script>window.open('process.php?wk_id=".$wk_id."','_self')</script>";
}



echo "<form id='frm' name='frm' enctype='multipart/form-data' method=post action='process.php?wk_id=".$wk_id."'>";
echo "<INPUT TYPE='hidden' NAME='h_file' id='h_file' value=''>";

$sql = sprintf("select wk_status,wk_uid,wk_submit from %swork_item where wk_id=%d",DB_PREFIX,$wk_id);
$data=dbarray(dbquery($sql));
$wk_status=$data['wk_status'];
$wk_item_uid=$data['wk_uid'];
$wk_submit=$data['wk_submit'];
$sql = sprintf("select wk_id,wk_flag,group_concat(concat('【',wk_dname,'】',wk_uname) separator '<br>') as name,group_concat(wk_uid) as uid  from %swork_dept where wk_id=%d group by wk_flag order by wk_flag",DB_PREFIX,$wk_id);
$rst=dbquery($sql);
while ($data=dbarray($rst))
{
	
	
	echo "<table align='center' cellpadding='1' cellspacing='1' width='80%' class='tbl-border' style='border:1px solid green'>";
	echo sprintf("<tr class='tbl2'><td>%s",($data["wk_flag"]=="0" ? "责任部门" : "配合部门"));
	echo sprintf("<tr class='tbl1'><td align='center'>%s",$data["name"]);
	if (in_array($userdata['user_id'],explode(",",$data['uid'])) && $wk_status==1 && $wk_submit==0 && $data['wk_flag']==0)
		echo "<br><span id='spn_submit'><button type='button' onclick='send_submit(".$wk_id.")'>申请结束</button></span>";

	$sql = sprintf("select b.* from %swork_dept a inner join %swork_process b on a.wk_aid=b.wk_aid where wk_id=%d and wk_flag=%d order by wk_datetime desc",DB_PREFIX,DB_PREFIX,$data['wk_id'],$data['wk_flag']);
	$rst_p=dbquery($sql);
	$b_submit=0;
	while ($data_p=dbarray($rst_p))
	{
		$sql = sprintf("select * from %swork_attach a inner join %swork_cache b on a.attach_id=b.attach_id where a.cat='wk_process' and id=%d",DB_PREFIX,DB_PREFIX,$data_p['wk_pid']);
		$rst_att=dbquery($sql);
		$att="";
		while ($data_att=dbarray($rst_att))
		{
			$att .= "<span id='spn_".$data_att['attach_id']."'><a href='".MODS."mod_attach.php?fid=".$data_att['attach_id']."&type=work'>".$data_att['attach_name']."(".parsebytesize($data_att['attach_size'],2).")</a>";
			if ($userdata['user_id']==$data_p["wk_uid"] && $wk_status<2)
				$att .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:del_attach(".$data_att['attach_id'].",".$data_p['wk_pid'].")'>删除</a>";
			$att .="</span><br>";

		}
		
		if ($userdata['user_id']==$data_p["wk_uid"] && $wk_status<2)
		{
			$del="&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' onclick='del_process(".$data_p["wk_pid"].")' >删除</button>";
		}
		else
			$del="";
		echo sprintf("<tr class='wf_yes' id='tr_%d'><td style='padding-left:10px'>%s%s<br><span style='float:right'>%s--%s%s</span>",$data_p["wk_pid"],$att,htmlspecialchars_decode($data_p["wk_process"],ENT_QUOTES),$data_p["wk_uname"],date("Y-m-d H:i",$data_p["wk_datetime"]),$del);
	}
	if (in_array($userdata['user_id'],explode(",",$data['uid'])) && $wk_status==1)
	{
		echo "<input type='hidden' name='wk_flag' value='".$data["wk_flag"]."'>";
		echo "<tr class='tbl1' align='center'><td><TEXTAREA NAME='wk_p' id='wk_p' ROWS='5' COLS='100'></TEXTAREA>附件<span id='spanButtonPlaceHolder'></span>";
		echo "<tr><td align='center'><div class='fieldset flash' id='fsUploadProgress' style='display:none'></div>";

		echo "<tr align='center'><td><button type='submit' id='b_ok'>提交</button>";

		echo "<script type='text/javascript' src='".INCLUDES."swfupload/swfupload.js'></script>
		<script type='text/javascript' src='".INCLUDES."swfupload/handlers.js'></script>
		<script type='text/javascript' src='".INCLUDES."swfupload/fileprogress.js'></script>";
		?>
		<script>
		var swfu;
		var settings = {
			flash_url : "../includes/swfupload/swfupload.swf",
			flash9_url : "../includes/swfupload/swfupload_fp9.swf",
			upload_url: "../includes/swfupload/upload.php?root=work",
			file_post_name: "file",
			//post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
			file_size_limit : "200 MB",
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : 100,
			file_queue_limit : 0,
			custom_settings : {
				progressTarget : "fsUploadProgress",
				formid : "frm",
				submitid:"b_ok",
				width:61,
				height:22
			},
			debug: false,

			// Button settings
			button_image_url: "../includes/swfupload/Upload61x22_t.png",
			button_width: "61",
			button_height: "22",
			button_placeholder_id: "spanButtonPlaceHolder",
		
			// The event handler functions are defined in handlers.js
			swfupload_loaded_handler : swfUploadLoaded,
			swfupload_preload_handler : preLoad,
			swfupload_load_failed_handler : loadFailed,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete
			//queue_complete_handler : queueComplete	// Queue plugin event
			};
			swfu = new SWFUpload(settings);
		</script>
		<?php
	}
	echo "</table><p>";
}
//督办意见
echo "<table align='center' cellpadding='1' cellspacing='1' width='80%' class='tbl-border' style='border:1px solid green'>";
echo "<tr class='tbl2'><td>督办意见";
echo "<tr class='tbl1'><td align='center'>".get_uname($wk_item_uid,"dept","");
$sql = sprintf("select * from %swork_result where wk_id=%d",DB_PREFIX,$wk_id);
$rst = dbquery($sql);
while ($data_p=dbarray($rst))
{
	if ($userdata['user_id']==$wk_item_uid)
	{
		$del="&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' onclick='del_result(".$data_p["wk_pid"].")' >重新填写意见</button>";
	}
	else
		$del="";
	
	echo sprintf("<tr class='".($wk_status==3 ? "wf_no" : "wf_yes" )."' ><td style='padding-left:10px'>%s<br><span style='float:right'>%s--%s%s</span>",htmlspecialchars_decode($data_p["wk_result"],ENT_QUOTES),$data_p["wk_uname"],date("Y-m-d H:i",$data_p["wk_datetime"]),$del);
}
if ($wk_submit==1 && $wk_item_uid==$userdata["user_id"])
{
	echo "<tr class='tbl1' align='center'><td><TEXTAREA NAME='wk_text_rst' id='wk_text_rst' ROWS='5' COLS='100'></TEXTAREA>";
	echo "<tr align='center'><td><button type='button' onclick='rst_submit(2)'>已完成结束</button><button type='button' onclick='rst_submit(3)'>未完成结束</button><button type='button' onclick='back_submit()'>退回申请</button>";

}
echo "</table>";
echo "<input type='hidden' name='act' id='act' value=''>";
echo "<input type='hidden' name='rst_status' id='rst_status' value=''>";

echo "</form>";

function work_sms($wk_id,$sms)
{
	//责任单位，配合单位，督办人 接受短信
	global $userdata;
	$sql = sprintf("select group_concat(DISTINCT wk_uid) from %swork_dept where wk_id=%d and wk_uid<>%d",DB_PREFIX,DB_PREFIX,$wk_id,$userdata['user_id']);
	$uids=dbdata($sql,0,0);
	$arr_uid=array();
	if ($uids!="")
		$arr_uid=explode(",",$uids);
	$sql = sprintf("select wk_uid from %swork_item where wk_id=%d and wk_uid<>%d",DB_PREFIX,$wk_id,$userdata['user_id']);
	$uids=dbdata($sql,0,0);
	if ($uids!="")
		array_push($arr_uid,$uids);
	array_unique($arr_uid);
	$uids=implode(",",$arr_uid);
	if ($uids!="")
	{
		$sql = sprintf("select group_concat(name) names,group_concat(user_phone) phones from vw_users where user_id in(%s)",$uids);
		$data=dbarray(dbquery($sql));
		send_sms($data['names'],$data['phones'],$sms,$userdata['user_id'],"工作督办",$userdata['name']);
	}
}
?>
<script type="text/javascript">

function ValidateForm()
{
	$("#act").val("ok");
	if (isNull($("#wk_p").val()))
	{
		alert("请填写进展内容");
		return false;
	}

	return true;
}
function del_attach(aid,wk_pid)
{
	$.get
	(
		"ajax.php",
		{t:"del_attach",aid:aid,id:wk_pid,cat:'wk_process'},
		function (data){
			if (data==""){
				$("#spn_"+aid).remove();
			}
				
		}
	)
}
function del_process(wk_pid)
{
	$.get
	(
		"ajax.php",
		{t:"del_process",wk_pid:wk_pid},
		function (data){
			if (data==""){
				$("#tr_"+wk_pid).remove();
			}
				
		}
	)
}
//提交申请
function send_submit()
{
	$("#act").val("send_submit");
	$("#frm").submit();
}
//提交督办意见
function rst_submit(b)
{
	$("#act").val("rst_submit");
	if (isNull($("#wk_text_rst").val()))
	{
		alert("请填写内容");
		return false;
	}
	$("#rst_status").val(b);
	$("#frm").submit();
}
//退回申请
function back_submit()
{
	$("#act").val("back_submit");
	$("#frm").submit();

}
//删除意见，重新填写
function del_result()
{
	$("#act").val("del_result");
	$("#frm").submit();

}

</script>

