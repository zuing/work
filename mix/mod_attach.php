<?php
require_once "../maincore.php";
if($type=="news") // 下载文件
{
	$sql = sprintf("SELECT * FROM %snews_cache WHERE attach_id='%d'", DB_PREFIX, $fid);
	$result = dbquery($sql);
	$root = realpath(BASEDIR)."\\cache\\news\\";

	$sql=sprintf("update %snews a inner join %snews_attach b on a.news_id=b.news_id set news_reads=news_reads+1 where news_attach_id=%d and news_cat=15",
		DB_PREFIX,
		DB_PREFIX,
		$fid
	);
	dbquery($sql);

}

if($type=="workflow") // 下载文件
{
	$sql = sprintf("SELECT * FROM %sworkflow_cache WHERE attach_id='%d'", DB_PREFIX, $fid);
	$result = dbquery($sql);
	$root = realpath(BASEDIR)."\\cache\\workflow\\";
}

if($type=="work") 
{
	$sql = sprintf("SELECT * FROM %swork_cache WHERE attach_id='%d'", DB_PREFIX, $fid);
	$result = dbquery($sql);
	$root = realpath(BASEDIR)."\\cache\\work\\";
}


if($data = dbarray($result))
{
	if(strlen(trim($data['attach_hash']))==32)
	{
		if($type=="news")
			$root =$root.date('Y',$data['attach_datestamp'])."\\";
		$filehash=$data['attach_hash'];
		$filename=$data['attach_name'];
		$a=explode(".",$filename);
		$filetype=$a[count($a)-1];
		$filesize=$data['attach_size'];
		include "mime.php";
		$type=$mimetypes[$filetype];
		if ($type=="")
			$type="application/octet-stream";
	//var_dump($mimetypes);
	//echo $type;
	//exit;	
		//header('Content-Type: application/octet-stream');
		header('Content-Type: '.$type);
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-Transfer-Encoding: binary');
		header("Content-Length: ".$filesize);
		header("Content-Disposition: attachment; filename=\"".$filename."\";");
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		$path = $root.substr($filehash, 0, 2)."\\".substr($filehash, 2, 2)."\\".$filehash;
		$fp=fopen($path,  "rb"); 
		if($fp)
		{
			$attach_data='';
			while (!feof($fp))
			{
				$buffer = fread ( $fp, 1024 * 1024 );    
				echo $buffer;    
				ob_flush ();    
				flush ();    
			}
			fclose($fp); 
		}
		exit;
	}
}






?>