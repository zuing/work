<?php
require "../maincore.php";
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$dhtml_dadaview=INCLUDES."dhtmlx3.5/dhtmlxDataView/codebase/";
require_once($dhtml_dadaview."connector/dataview_connector.php");
$conn = new DataViewConnector($db_connect);
$conn->set_encoding("GBK");
//$conn->enable_log("log.txt");

?>