<?php
//code below is simplified - in real app you will want to have some kins session based autorization and input value checking
error_reporting(E_ALL ^ E_NOTICE);

//include db connection settings
require "dp_common.php";

function add_row($ids){
	
	global $newId;

	$did=$_POST[$ids.'_c4'];
	$dname=iconv('UTF-8','GBK',$_POST[$ids.'_c0']);
	$uid=$_POST[$ids.'_c5'];
	$uname=iconv('UTF-8','GBK',$_POST[$ids.'_c1']);
	$flag=$_POST[$ids.'_c2'];
	$wk_id=$_POST[$ids.'_c6'];
	$sql = sprintf("insert into %swork_dept (wk_id,wk_did,wk_dname,wk_uid,wk_uname,wk_flag) values(%d,%d,'%s','%s','%s',%d)",
			DB_PREFIX,
			$wk_id,
			$did,
			$dname,
			$uid,
			$uname,
			$flag
	);
	
	//LogMaster::log($sql);
	dbquery($sql);

	$newId = mysql_insert_id();

	return "insert";	
}

function update_row($ids){
	$did=$_POST[$ids.'_c4'];
	$dname=iconv('UTF-8','GBK',$_POST[$ids.'_c0']);
	$uid=$_POST[$ids.'_c5'];
	$uname=iconv('UTF-8','GBK',$_POST[$ids.'_c1']);
	$flag=$_POST[$ids.'_c2'];
	$sql = sprintf("update %swork_dept set wk_did=%d,wk_dname='%s',wk_uid='%s',wk_uname='%s',wk_flag='%d' where wk_aid=%d",DB_PREFIX,$did,$dname,$uid,$uname,$flag,$ids);
	//LogMaster::log($sql);
	dbquery($sql);
	return "update";	
}

function delete_row($ids){
	$sql = sprintf("delete from %swork_dept where wk_aid=%d",DB_PREFIX,$ids);
	dbquery($sql);
	//LogMaster::log($sql);

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
//if ($error!="")
	//echo "<action type='error' sid='".$rowId."' tid='".$newId."'>".$error."</action>";
echo "<action type='".$action."' sid='".$rowId."' tid='".$newId."'/>";
echo "</data>";
?>