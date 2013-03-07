<?php
	require "dp_common.php";
	if ($_GET['act']=='loadmain')
	{
		/*
		$conn->sort($sql_str);
		*/
		if (isset($_GET['key']) && $_GET['key']!="")
			$conn->filter("wk_work like '%".$_GET['key']."%'");

		$conn->filter("wk_parent=0");
		$conn->sort("wk_lasttime","desc");
		$conn->event->attach("beforeRender","format_main");
		$conn->dynamic_loading(50);
		$conn->render_table(DB_PREFIX."work_item","wk_id","wk_work,wk_lasttime,wk_status");

	}	
	function format_main($row){
		//render field as details link
		//$data = $row->get_value("wf_subject");
		//$row->set_value("wf_subject","<a href='details.php?id=$data'>".$data."</a>");

		//formatting date field
		$data = $row->get_value("wk_lasttime");
		$row->set_value("wk_lasttime",date("Y-m-d H:i",$data));

		$data = $row->get_value("wk_work");
		$row->set_value("wk_work",htmlspecialchars_decode($data,ENT_QUOTES));


		$data = $row->get_value("wk_status");

		switch($data){
			case 0:
				$row->set_value("wk_status","<img src='".IMAGES."flag-gray.png' width='24px' height='24px'></img>");
			break;
			case 1:
				$row->set_value("wk_status","<img src='".IMAGES."flag-blue.png' width='24px' height='24px'></img>");
			break;
			case 2:
				$row->set_value("wk_status","<img src='".IMAGES."flag-green.png' width='24px' height='24px'></img>");
			break;
			case 3:
				$row->set_value("wk_status","<img src='".IMAGES."flag-red.png' width='24px' height='24px'></img>");
			break;
		}
	}


?>