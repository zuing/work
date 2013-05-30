<?php
require_once "../maincore.php";
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if (!iMEMBER)
{
	echo "-1000";
	exit;
}

if (isset($_GET['t']) && $_GET['t']=='loaditem')
{
	if ($_GET['act']=="view")	//主题事项
	{
		$sql=sprintf("select wk_uid from %swork_item where wk_id=%d",DB_PREFIX,$_GET['id']);
		if (dbdata($sql,0,0)==$userdata['user_id'])
			$sql=sprintf("select * from %swork_item where wk_parent=%d order by wk_lasttime desc",DB_PREFIX,$_GET['id']);
		else
			$sql=sprintf("select * from %swork_item where wk_parent=%d and wk_status<>0 order by wk_lasttime desc",DB_PREFIX,$_GET['id']);
		
	}
	
	if ($_GET['act']=="my_star") //我的关注
		$sql=sprintf("select a.* from %swork_item a inner join %swork_star b on a.wk_id=b.wk_id where b.wk_uid=%d and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,DB_PREFIX,$userdata['user_id']);
	if ($_GET['act']=="my_work") //我的事项
		$sql = sprintf("select a.* from %swork_item a inner join %swork_dept b on a.wk_id=b.wk_id where b.wk_uid=%d and a.wk_status<>0 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,DB_PREFIX,$userdata['user_id']);
	if ($_GET['act']=="my_item") //我的督办
		$sql=sprintf("select * from %swork_item where wk_parent<>0 and wk_uid=%d and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,$userdata['user_id']);
	if ($_GET['act']=="wk_submit") //申请结束
		$sql=sprintf("select * from %swork_item where wk_submit=1 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
	if ($_GET['act']=="wk_dept") //部门事项
	{
		$arr_dept=get_child_dept($_GET['id']);
		$sql = sprintf("select a.* from %swork_item a inner join %swork_dept b on a.wk_id=b.wk_id where b.wk_did in (%s) and a.wk_status<>0 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,DB_PREFIX,implode(",",$arr_dept));
	}


	if ($_GET['act']=="wk_all") //全部
		$sql=sprintf("select * from %swork_item where wk_status<>0 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
	if ($_GET['act']=="wk_1") //进行中
		$sql=sprintf("select * from %swork_item where wk_status=1 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
	if ($_GET['act']=="wk_2") //同意结束
		$sql=sprintf("select * from %swork_item where wk_status=2 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
	if ($_GET['act']=="wk_3") //不同意结束
		$sql=sprintf("select * from %swork_item where wk_status=3 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
	if ($_GET['act']=="wk_0") //未下发
		$sql=sprintf("select * from %swork_item where wk_status=0 and wk_uid=%d and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,$userdata['user_id']);
	if ($_GET['act']=="wk_delay") //已经超时
		$sql=sprintf("select * from %swork_item where wk_status<>0 and wk_enddate<%d-(24*60*60) and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,time());

 	if ($_GET['act']=="search") //查找
	{
		if (trim($_GET['key'])=="")
			$sql=sprintf("select * from %swork_item and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX);
		else
			$sql=sprintf("select * from %swork_item where wk_work like '%%%s%%' and wk_parent<>0 and wk_parent<>0 order by wk_lasttime desc",DB_PREFIX,iconv("UTF-8","GBK",$_GET['key']));
	}
	//echo $sql;
	$rst=dbquery($sql);
	while ($data=dbarray($rst))
	{
		
		switch($data['wk_status']){
			case 0:
				$img="<img src='".IMAGES."flag-gray.png' width='24px' height='24px'></img>";
				break;
			case 1:
				$img="<img src='".IMAGES."flag-blue.png' width='24px' height='24px'></img>";
				break;
			case 2:
				$img="<img src='".IMAGES."flag-green.png' width='24px' height='24px'></img>";
				break;
			case 3:
				$img="<img src='".IMAGES."flag-red.png' width='24px' height='24px'></img>";
				break;
		}
		if ($data['wk_enddate']<(time()-(24*60*60)) && $data['wk_status']==1)
			$img="<img src='".IMAGES."flag-yellow.png' width='24px' height='24px'></img>";

		$arr_dept="";
		$arr_dept_0="";
		$arr_dept_1="";
		$sql = sprintf("select * from %swork_dept where wk_id=%d order by wk_flag",DB_PREFIX,$data['wk_id']);
		//echo $sql;
		$rst_dept=dbquery($sql);
		while ($data_dept=dbarray($rst_dept))
		{
			$arr_dept[]= sprintf("{id:%d,data:['%s','%s','%s','%s','%s','%s','%s']}",$data_dept['wk_aid'],$data_dept['wk_dname'],$data_dept['wk_uname'],$data_dept['wk_flag'],"删除^javascript:subgrid_del(".$data_dept['wk_id'].",".$data_dept['wk_aid'].");^_self",$data_dept['wk_did'],$data_dept['wk_uid'],$data_dept['wk_id']);
			if ($data_dept['wk_flag']==0) $arr_dept_0[]="【".$data_dept['wk_dname']."】".$data_dept['wk_uname'];
			if ($data_dept['wk_flag']==1) $arr_dept_1[]="【".$data_dept['wk_dname']."】".$data_dept['wk_uname'];
		}

		$sql = sprintf("select count(*) as cnt from %swork_attach where cat='wk_item' and id=%d",DB_PREFIX,$data['wk_id']);
		if (dbdata($sql,0,0))
			$attach="<img src='".IMAGES."attach.png' width='20px' height='20px'></img>^javascript:open_attach(".$data['wk_id'].");^_self";
		else
			$attach="<img src='".IMAGES."attach_dis.png' width='20px' height='20px'></img>^javascript:open_attach(".$data['wk_id'].");^_self";

		$wk_dept=sprintf("{rows:[%s]}",implode(",",$arr_dept));

		$arr_data[]= sprintf("{id:%d,data:[\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"]}",
			$data['wk_id'],
			$wk_dept,
			$img,
			$attach,
			preg_replace("%\n%", "<br>", $data['wk_work']),
			date("Y-m-d",$data['wk_startdate']),
			date("Y-m-d",$data['wk_enddate']),
			implode("<br>",$arr_dept_0),
			implode("<br>",$arr_dept_1),
			$data['wk_uname']
			);
	}

	echo iconv("GBK","UTF-8",sprintf("dataWk={rows:[%s]};\n",implode(",",$arr_data)));
}

if (isset($_GET['t']) && $_GET['t']=='del_attach')
{
	del_attach($_GET['id'],$_GET['aid'],$_GET['cat']);
}
if (isset($_GET['t']) && $_GET['t']=='del_process')
{
	$sql = sprintf("delete from %swork_process where wk_pid=%d",DB_PREFIX,$_GET['wk_pid']);
	dbquery($sql);
	
	$sql = sprintf("select * from %swork_attach where id=%d and cat='wk_process'",DB_PREFIX,$_GET['wk_pid']);
	$rst = dbquery($sql);
	while ($data=dbarray($rst))
	{
		del_attach($data['id'],$data['attach_id'],"wk_process");
	}
}
if (isset($_GET['t']) && $_GET['t']=='star')
{
	if ($_GET['act']=="load")
	{
		$sql = sprintf("select * from %swork_star where wk_id=%d and wk_uid=%d",DB_PREFIX,$_GET['wk_id'],$userdata['user_id']);
		echo dbrows(dbquery($sql));
	}
	if ($_GET['act']=="star")
	{
		$sql = sprintf("insert into %swork_star (wk_id,wk_uid) values(%d,%d)",DB_PREFIX,$_GET['wk_id'],$userdata['user_id']);
		dbquery($sql);
	}
	if ($_GET['act']=="unstar")
	{
		$sql = sprintf("delete from %swork_star where wk_id=%d and wk_uid=%d",DB_PREFIX,$_GET['wk_id'],$userdata['user_id']);
		dbquery($sql);
	}

}
if (isset($_GET['t']) && $_GET['t']=='my_item_uid')
{
	$sql = sprintf("select wk_uid from %swork_item where wk_id=%d",DB_PREFIX,$_GET['pid']);
	if ($userdata["user_id"]==dbdata($sql,0,0))
		echo "1";
}
if (isset($_GET['t']) && $_GET['t']=='del_work_item')
{
	//del item attach
	$sql = sprintf("select * from %swork_attach where id=%d and cat='wk_item'",DB_PREFIX,$_GET['wk_id']);
	$rst = dbquery($sql);
	while ($data=dbarray($rst))
	{
		del_attach($data['id'],$data['attach_id'],"wk_item");
	}

	//del process and attach
	$sql = sprintf("select * from %swork_dept a inner join %swork_process b on a.wk_aid=b.wk_aid where a.wk_id=%d",DB_PREFIX,DB_PREFIX,$_GET['wk_id']);
	$rst_w=dbquery($sql);
	while ($data_w=dbarray($rst_w))
	{
		$sql = sprintf("select * from %swork_attach where id=%d and cat='wk_process'",DB_PREFIX,$data_w['wk_pid']);
		$rst = dbquery($sql);
		while ($data=dbarray($rst))
		{
			del_attach($data['id'],$data['attach_id'],"wk_process");
		}
	}
	$sql = sprintf("delete from %swork_process where wk_aid in (select wk_aid from %swork_dept where wk_id=%d)",DB_PREFIX,DB_PREFIX,$_GET['wk_id']);
	dbquery($sql);
	//del result
	$sql = sprintf("delete from %swork_result where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
	dbquery($sql);
	
	//del work_dept
	$sql = sprintf("delete from %swork_dept where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
	dbquery($sql);

	//del work star
	$sql = sprintf("delete from %swork_star where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
	dbquery($sql);

	//del work_item in db_grid.php
}
if (isset($_GET['t']) && $_GET['t']=='del_work_dept')
{
	$sql = sprintf("select * from %swork_process where wk_aid=%d",DB_PREFIX,$_GET['aid']);
	if (dbrows(dbquery($sql))>0)
		echo "0";
}


if (isset($_GET['t']) && $_GET['t']=='out')
{
	$sql = sprintf("select wk_uid,wk_status from %swork_item where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
	$data=dbarray(dbquery($sql));
	if ($data['wk_uid']==$userdata["user_id"])
	{
		if ($_GET['cat']=="view")
		{
			$sql = sprintf("select * from %swork_item where wk_parent=%d and wk_status=0",DB_PREFIX,$_GET['wk_id']);
			if (dbrows(dbquery($sql)))
				echo "0";
		}
		else
			echo $data['wk_status'];
	}
	else
		echo "-1";

}
if (isset($_GET['t']) && $_GET['t']=='do_out')
{
	if ($_GET['act']=="view")
	{
		$sql = sprintf("select group_concat(DISTINCT b.wk_uid) from %swork_item a inner join %swork_dept b on a.wk_id=b.wk_id where wk_status=0 and wk_parent=%d",DB_PREFIX,DB_PREFIX,$_GET['wk_id']);
		$uids=dbdata($sql,0,0);
		$sql = sprintf("update %swork_item set wk_status=1 where wk_parent=%d and wk_status=0",DB_PREFIX,$_GET['wk_id']);
		dbquery($sql);
		//update main item
		$sql = sprintf("update %swork_item set wk_status=1 where wk_id=%d and wk_status=0",DB_PREFIX,$_GET['wk_id']);
		dbquery($sql);

	}
	else
	{
		$sql = sprintf("select group_concat(DISTINCT b.wk_uid) from %swork_item a inner join %swork_dept b on a.wk_id=b.wk_id where wk_status=0 and a.wk_id=%d",DB_PREFIX,DB_PREFIX,$_GET['wk_id']);
		$uids=dbdata($sql,0,0);
		$sql = sprintf("update %swork_item set wk_status=1 where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
		dbquery($sql);
		//update main item
		$sql = sprintf("select wk_parent from %swork_item where wk_id=%d",DB_PREFIX,$_GET['wk_id']);
		$sql = sprintf("update %swork_item set wk_status=1 where wk_id=%d and wk_status=0",DB_PREFIX,dbdata($sql,0,0));
		dbquery($sql);

	}

	//send sms
	if ($uids!="")
	{
		$sql = sprintf("select group_concat(name) names,group_concat(user_phone) phones from vw_users where user_id in(%s)",$uids);
		$data=dbarray(dbquery($sql));
		send_sms($data['names'],$data['phones'],"工作督办:您有新的工作等待处理",$userdata['user_id'],"工作督办",$userdata['name']);
	}
}
if (isset($_GET['t']) && $_GET['t']=='chg_uid')
{
	$sql = sprintf("update %swork_item set wk_uid=%d,wk_uname='%s' where wk_id=%d",DB_PREFIX,$_GET['uid'],iconv("UTF-8","GBK",$_GET['uname']),$_GET['wk_id']);
	dbquery($sql);
}

function del_attach($id,$aid,$cat)
{
	$sql = sprintf("delete from %swork_attach where id=%d and attach_id=%d and cat='%s'",DB_PREFIX,$id,$aid,$cat);
	dbquery($sql);

	$sql = sprintf("select count(*) as cnt from %swork_attach where attach_id=%d",DB_PREFIX,$aid);
	if (dbdata($sql,0,0)==0)
	{
		$sql = sprintf("select attach_hash from %swork_cache where attach_id=%d",DB_PREFIX,$aid);
		$data_md5=dbarray(dbquery($sql));

		$sql = sprintf("delete from %swork_cache where attach_id=%d",DB_PREFIX,$aid);
		dbquery($sql);
		$root = realpath(BASEDIR)."\\cache\\work\\";

		$path = $root.substr($data_md5['attach_hash'], 0, 2)."\\".substr($data_md5['attach_hash'], 2, 2)."\\".$data_md5['attach_hash'];
		unlink($path);
		rmdir($root.substr($data_md5['attach_hash'], 0, 2)."\\".substr($data_md5['attach_hash'], 2, 2));
		rmdir($root.substr($data_md5['attach_hash'], 0, 2));

	}

}

?>