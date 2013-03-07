<?php
require "../maincore.php";
require "../header.php";
require_once THEME."theme.php";
echo "<script type='text/javascript' src='".INCLUDES."swfupload/swfupload.js'></script>
<script type='text/javascript' src='".INCLUDES."swfupload/handlers.js'></script>
<script type='text/javascript' src='".INCLUDES."swfupload/fileprogress.js'></script>
";
if (!iMEMBER)
{
	echo "请登陆后查看";
	exit;
}
$wk_id=(int)$_GET['wk_id'];

if (isset($_POST['b_send']))
{
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
			$sql=sprintf("insert into %swork_attach (id,attach_id,cat) values(%d,%d,'wk_item')",DB_PREFIX,$wk_id,$cache_id[$i]);
			dbquery($sql);
		}

		echo "<script>window.open('attach.php?wk_id=".$wk_id."','_self')</script>";

}

echo "<form name='inputform' id='inputform' enctype='multipart/form-data' method='post' action='".FUSION_SELF."?wk_id=".$wk_id."' >\n";
echo "<INPUT TYPE='hidden' NAME='h_file' id='h_file' value=''>";
echo "<INPUT TYPE='hidden' NAME='b_send' id='b_send' value='0'>";

echo "<input type='hidden' name='MAX_FILE_SIZE' value='209715200' />";
echo "<table style='padding:50px' align=center>";

$sql = sprintf("select * from %swork_attach a inner join %swork_cache b on a.attach_id=b.attach_id where a.cat='wk_item' and id=%d",DB_PREFIX,DB_PREFIX,$wk_id);
$rst=dbquery($sql);
while ($data=dbarray($rst))
{
	echo "<tr id='tr_".$data['attach_id']."'><td align='center'><a href='".MODS."mod_attach.php?fid=".$data['attach_id']."&type=work'>".$data['attach_name']."(".parsebytesize($data['attach_size'],2).")</a>";
	echo "<td><a href='javascript:del_attach(".$data['attach_id'].",".$wk_id.")'>删除</a>";

}
echo "<tr><td><div class='fieldset flash' id='fsUploadProgress'></div>
		<td><span id='spanButtonPlaceHolder'></span><br>
		<tr align='center'><td colspan=2><input type='submit' name='add_attach' id='add_attach' value='上传' class='button'>\n
</form>";

?>
<script type="text/javascript">
//swf upload
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
				formid : "inputform",
				submitid:"add_attach",
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
function ValidateForm()
{
	return true;
}
function del_attach(aid,wk_id)
{
	$.get
	(
		"ajax.php",
		{t:"del_attach",aid:aid,id:wk_id,cat:'wk_item'},
		function (data){
			if (data==""){
				$("#tr_"+aid).remove();
			}
				
		}
	)
}
</script>