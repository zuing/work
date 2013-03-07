<?php
//code below is simplified - in real app you will want to have some kins session based autorization and input value checking
error_reporting(E_ALL ^ E_NOTICE);

//include db connection settings
require "dp_common.php";

function add_row($ids){
	global $newId,$userdata;
	$work=htmlspecialchars(iconv('UTF-8','GBK',$_POST[$ids.'_wk_work']),ENT_QUOTES);

	$sql = sprintf("insert into %swork_item (wk_work,wk_lasttime,wk_status,wk_parent,wk_uid,wk_createtime,wk_enddate,wk_startdate) values('%s',%d,%d,%d,%d,%d,%d,%d)",
		DB_PREFIX,
		$work,
		time(),
		0,
		$_POST[$ids.'_userdata'],
		$userdata['user_id'],
		time(),
		time(),
		time()
	);
	dbquery($sql);
	$newId = mysql_insert_id();
	return "insert";	
}

function update_row($ids){
	$work=htmlspecialchars((iconv('UTF-8','GBK',$_POST[$ids.'_wk_work'])),ENT_QUOTES);
	$sdate=strtotime($_POST[$ids.'_wk_startdate']);
	$edate=strtotime($_POST[$ids.'_wk_enddate']);
	$sql = sprintf("update %swork_item set wk_work='%s',wk_lasttime=%d,wk_startdate=%d,wk_enddate=%d where wk_id=%d",DB_PREFIX,$work,time(),$sdate,$edate,$ids);
	//LogMaster::log($sql);
	dbquery($sql);
	return "update";	
}

function delete_row($ids){
	$sql = sprintf("delete from %swork_item where wk_id=%d",DB_PREFIX,$ids);
	dbquery($sql);
	return "delete";	
}
/*
$k=array_keys($_POST);
LogMaster::log(implode(",",$k));
LogMaster::log(implode(",",$_POST));
*/
//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may differ in your case
echo('<?xml version="1.0" encoding="GBK"?>'); 


$ids   = $_POST["ids"];
$mode  = $_POST[$ids."_!nativeeditor_status"]; //get request mode
$rowId = $ids; //id or row which was updated 
$newId = $ids; //will be used for insert operation

switch($mode){
	case "inserted":
		//row adding request
		$action = add_row($ids);
	break;
	case "deleted":
		//row deleting request
		$action = delete_row($ids);
	break;
	default:
		//row updating request
		$action = update_row($ids);
	break;
}


//output update results
echo "<data>";
echo "<action type='".$action."' sid='".$rowId."' tid='".$newId."'/>";
echo "</data>";
?>