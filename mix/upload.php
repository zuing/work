<?php
	// Work-around for setting up a session because Flash Player doesn't send the cookies
	// The Demos don't save files
	require_once "../../maincore.php";
	if ($_GET['root']=='work')
		$root = realpath(BASEDIR)."\\cache\\work\\";

	if ($_GET['root']=='msg')
		$root = realpath(BASEDIR)."\\cache\\message\\";
	if ($_GET['root']=='news')
	{
		$root = realpath(BASEDIR)."\\cache\\news\\";
		if (!file_exists($root. date('Y',time())))
			mkdir($root. date('Y',time()));
		$root=$root.date('Y',time())."\\";
	}

	if(($_FILES["file"]["error"]==0)&&($_FILES["file"]["size"]>0))
	{
		$md5=md5_file($_FILES["file"]["tmp_name"]);
		$path=md5_path($root,$md5);	
		copy($_FILES["file"]["tmp_name"],$path);
		unlink($_FILES["file"]["tmp_name"]); 

		echo " 
			{\"name\":\"".$_FILES["file"]["name"]."\",".
			"\"size\":\"".$_FILES["file"]["size"]."\",".
			"\"md5\":\"".$md5."\",".
			"\"path\":\"".str_replace("\\","/",$path)."\"}";
		
	}
?>