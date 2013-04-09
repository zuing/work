<?php
require "../maincore.php";
require "../subheader.php";

if (!iMEMBER) fallback("../index.php");

$dhtmlx_accordion=INCLUDES."dhtmlx3.5/dhtmlxAccordion/codebase/";
echo "<td height=100%>";
echo '<script src="'.$dhtmlx_accordion.'dhtmlxcommon.js"></script>';

echo '<script src="'.$dhtmlx_accordion.'dhtmlxaccordion.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="'.$dhtmlx_accordion.'skins/dhtmlxaccordion_dhx_skyblue.css">';
echo '<script src="'.$dhtmlx_accordion.'dhtmlxcontainer.js"></script>';

$dhtmlx_Layout=INCLUDES."dhtmlx3.5/dhtmlxLayout/codebase/";

echo '<script src="'.$dhtmlx_Layout.'dhtmlxlayout.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="'.$dhtmlx_Layout.'dhtmlxlayout.css">';
echo '<link rel="stylesheet" type="text/css" href="'.$dhtmlx_Layout.'skins/dhtmlxlayout_dhx_skyblue.css">';

$dhtmlx_toolbar=INCLUDES."dhtmlx3.5/dhtmlxToolbar/codebase/";
echo "<script type='text/javascript' src='".$dhtmlx_toolbar."dhtmlxtoolbar.js'></script>\n";
echo "<link rel='stylesheet' type='text/css' href='".$dhtmlx_toolbar."skins/dhtmlxtoolbar_dhx_skyblue.css'></link>\n";


$dhtmlx_dadaview=INCLUDES."dhtmlx3.5/dhtmlxDataView/codebase/";
echo '<script src="'.$dhtmlx_dadaview.'dhtmlxdataview.js" type="text/javascript"></script>';
echo '<script src="'.$dhtmlx_dadaview.'connector/dhtmlxdataprocessor.js " type="text/javascript"></script>';
echo '<script src="'.$dhtmlx_dadaview.'connector/connector.js " type="text/javascript"></script>';
echo '<link rel="STYLESHEET" type="text/css" href="'.$dhtmlx_dadaview.'dhtmlxdataview.css">';

$dhtmlx_grid=INCLUDES."dhtmlx3.5/dhtmlxGrid/codebase/";
echo "<link rel='stylesheet' type='text/css' href='".$dhtmlx_grid."dhtmlxgrid.css'>\n";
echo "<link rel='stylesheet' type='text/css' href='".$dhtmlx_grid."skins/dhtmlxgrid_dhx_skyblue.css'>\n";
echo "<script src='".$dhtmlx_grid."dhtmlxgrid.js'></script>\n";
echo "<script src='".$dhtmlx_grid."ext/dhtmlxgrid_srnd.js'></script>\n";
echo "<script src='".$dhtmlx_grid."ext/dhtmlxgrid_filter.js'></script>\n";

echo "<script src='".$dhtmlx_grid."dhtmlxgridcell.js'></script>\n";
echo "<script src='".$dhtmlx_grid."excells/dhtmlxgrid_excell_sub_row.js'></script>";
echo "<script src='".$dhtmlx_grid."excells/dhtmlxgrid_excell_link.js'></script>";

$dhtmlx_cal=INCLUDES."dhtmlx3.5/dhtmlxCalendar/codebase/";
echo "<link rel='STYLESHEET' type='text/css' href='".$dhtmlx_cal."skins/dhtmlxcalendar_dhx_skyblue.css'>\n";
echo "<link rel='STYLESHEET' type='text/css' href='".$dhtmlx_cal."dhtmlxcalendar.css'>\n";
echo "<script  src='".$dhtmlx_cal."dhtmlxcalendar.js'></script>\n";
echo "<script  src='".$dhtmlx_grid."excells/dhtmlxgrid_excell_dhxcalendar.js'></script>\n";

$dhtmlx_tabbar=INCLUDES."dhtmlx3.5/dhtmlxTabbar/codebase/";
echo "<link rel='STYLESHEET' type='text/css' href='".$dhtmlx_tabbar."dhtmlxtabbar.css'>\n";
echo "<script src='".$dhtmlx_tabbar."dhtmlxtabbar.js'></script>\n";
echo '<textarea id="info" rows="5" cols="80" style="display:none"></textarea>';

if (checkrights("work"))
	echo "<script>b_wk=1</script>";
else
	echo "<script>b_wk=0</script>";

echo '<div id="data_layout" style="position: relative; top: 2px; left: 1px;width: 100%; height: 100%; border: #B5CDE4 1px solid;"></div>';
echo "<div id='data_container' style='width:100%;height:100%;'></div>";

//echo "<script  src='".INCLUDES."dhtmlx3.5\dhtmlxDataProcessor\codebase\dhtmlxdataprocessor_debug.js'></script>\n";

//require "../side_right.php";
require "../footer.php";
?>

<script type="text/javascript">
	//full Accord
	$.ajaxSetup({async: false });	

	var dhxLayout,dhxAccord_main,dhxtabbar;
	$("#tbl_body").css("height","75%");
	//Layout
	dhxLayout = new dhtmlXLayoutObject("data_layout", "2U");
    //dhxLayout = dhxAccord.cells("a1").attachLayout("2U");
	dhxLayout.cells("a").setText("");
	dhxLayout.setCollapsedText("a", "a");
	dhxLayout.cells("a").setWidth("250");
	dhxLayout.cells("a").hideHeader();
	dhxLayout.cells("b").hideHeader();
	dhxLayout.cells("a").attachObject("data_container");

	//left Layout a Toolbar
	dhxToolbar = dhxLayout.cells("a").attachToolbar();
    dhxToolbar.setIconsPath("../includes/dhtmlx3.5/dhtmlxToolbar/samples/common/imgs/");
    dhxToolbar.addButton("new", 2, "新建", "new.gif", "new_dis.gif");
    dhxToolbar.addButton("delete", 5, "删除", "delete.png", "delete.png");
	dhxToolbar.addInput("key", 16, "", 80);
    dhxToolbar.addButton("search", 5, "", "search.png", "search.png");

	dhxToolbar.attachEvent("onClick", function(id) {
			if (id == "new"){
				view.add({
					wk_status:"<img src='<?php echo IMAGES;?>flag-gray.png' width='24px' height='24px'></img>",
					wk_work: "双击输入事项主题",
					wk_lasttime:date2str(new Date(),"yyyy-MM-dd hh:mm"),
					wk_uname:"<?php echo $userdata['name'];?>"
				}, 0);
			}
			if (id == "delete"){
				if(view.getSelected()=="")
					return false;
				if (mygrid.getRowsNum()>0)
				{
					alert("请先删除该主题工作事项");
					return false;
				}
				view.remove(view.getSelected());

			}
			if (id == "search"){
				key = dhxToolbar.getValue("key");
				view.clearAll();
				view.load("loaddata.php?act=loadmain&key="+key);
			}
			
		});


	//right Layout b Toolbar
	dhxToolbar1 = dhxLayout.cells("b").attachToolbar();
    dhxToolbar1.setIconsPath("../includes/dhtmlx3.5/dhtmlxToolbar/samples/common/imgs/");

	dhxToolbar1.loadXML("main_toolbar.xml?etc=" + new Date().getTime(),add_split);


	//dhxToolbar1.addSpacer("se");
	dhxToolbar1.attachEvent("onClick", function(id) {
			if (id == "new"){
				if (view.getSelected()=="")
				{
					alert("请在左侧选择一个主题");
					return false;
				}
				mygrid.setUserData("","userdata", view.getSelected());
				mygrid.addRow((new Date()).valueOf(),[" ","<img src='<?php echo IMAGES;?>flag-gray.png' width='24px' height='24px'></img>","<img src='<?php echo IMAGES;?>attach_dis.png' width='20px' height='20px'></img>^javascript:open_attach();^_self","请输入工作内容",date2str(new Date(),"yyyy-MM-dd"),date2str(new Date(),"yyyy-MM-dd"),"","","<?php echo $userdata['name'];?>"],0);
				return;
			}
			if (id == "new_single"){
				mygrid.setUserData("","userdata", -1);
				mygrid.addRow((new Date()).valueOf(),[" ","<img src='<?php echo IMAGES;?>flag-gray.png' width='24px' height='24px'></img>","<img src='<?php echo IMAGES;?>attach_dis.png' width='20px' height='20px'></img>^javascript:open_attach();^_self","请输入工作内容",date2str(new Date(),"yyyy-MM-dd"),date2str(new Date(),"yyyy-MM-dd"),"","","<?php echo $userdata['name'];?>"],0);
				return;
			}

			if (id == "delete"){
				wk_id=mygrid.getSelectedId();
				if (wk_id)
				{
					if (confirm("确认：相关工作进展及附件都将被删除？"))
					{
						$.get
						(
							"ajax.php",
							{t:"del_work_item",wk_id:wk_id},
							function (data){
								if (data==""){
									mygrid.deleteSelectedRows();
								}
							}
						)
					}
					add_split();
					if (view.getSelected()!="")
					{
						dhxToolbar1.enableListOption("wk_m", "new");
					}
				}
				return;
			}
			if (id == "search"){

				key = dhxToolbar1.getValue("key");
				loaditem("search",0,key);
				view.unselectAll();
			
			}
			if (id == "process"){
				wk_id=mygrid.getSelectedId();
				//alert(wk_id);
				if(wk_id != null)
				{
					dhxAccord_main.cells("a2").show();
					dhxAccord_main.cells("a3").hide();
					dhxAccord_main.openItem("a2");
					dhxtabbar.removeTab("a"+wk_id);
					dhxtabbar.addTab("a"+wk_id, substr(mygrid.cells(wk_id,3).getValue(),20), "200px");
					dhxtabbar.setContentHref("a"+wk_id, "process.php?wk_id="+wk_id);
					dhxtabbar.setTabActive("a"+wk_id);
				}
				return;
			}
			if (id == "star")
			{
				wk_id=mygrid.getSelectedId();
				if(wk_id != null)
				{
					if (dhxToolbar1.getItemText("star")=="关注")
						act = "star";
					else
						act = "unstar";
					$.get
					(
						"ajax.php",
						{t:"star",wk_id:wk_id,act:act},
						function (data){
							if (act=="unstar"){
								dhxToolbar1.setItemText("star","关注");
								dhxToolbar1.setItemImage("star","star.png");
							}
							if (act=="star"){
								dhxToolbar1.setItemText("star","取消关注");
								dhxToolbar1.setItemImage("star","star_dis.png");
							}
								
						}
					)
				}
				return;
			}
			if (id == "out")
			{
				wk_id=mygrid.getSelectedId();
				if(wk_id != null)
				{
					$.get
					(
						"ajax.php",
						{t:"do_out",wk_id:wk_id,act:""},
						function (data){
							if (data=="")
							{
								mygrid.cells(wk_id,1).setValue("<img src='<?php echo IMAGES;?>flag-blue.png' width='24px' height='24px'></img>");
								dhxToolbar1.hideItem("out");
							}
						}
					)
				}
				return;
			}
			if (id == "outall")
			{
				wk_id = view.getSelected();
				$.get
				(
					"ajax.php",
					{t:"do_out",wk_id:wk_id,act:"view"},
					function (data){
						if (data=="")
						{	
							loaditem("view",wk_id);
							dhxToolbar1.hideItem("outall");
						}
					}
				)
				return;
			}

			if (id == "my_star")
			{
				loaditem("my_star",0);
				
			}
			if (id == "my_item")
			{
				loaditem("my_item",0);
		
			}
			if (id == "my_work")
			{
				loaditem("my_work",0);
		
			}
			if (id == "wk_submit")
			{
				loaditem("wk_submit",0);
		
			}
			if (id == "wk_dept")
			{
				var obj_dept=showdeptdlg()
				if (obj_dept)
				{
					loaditem("wk_dept",obj_dept.id);
					dhxAccord_main.cells("a1").setText("部门事务--"+obj_dept.name);
				}
				obj_dept = null;		
			}

			if (id == "wk_all")
			{
				loaditem("wk_all",0);
		
			}
			if (id == "wk_1")
			{
				loaditem("wk_1",0);
		
			}
			if (id == "wk_2")
			{
				loaditem("wk_2",0);
		
			}
			if (id == "wk_3")
			{
				loaditem("wk_3",0);
		
			}
			if (id == "wk_0")
			{
				loaditem("wk_0",0);
		
			}
			if (id == "wk_delay")
			{
				loaditem("wk_delay",0);
				
			}

			view.unselectAll();
			dhxAccord_main.openItem("a1");
			dhxAccord_main.cells("a2").hide();
			dhxAccord_main.cells("a3").hide();
			add_split();
		
		});


	//Accrord 
	dhxAccord_main = dhxLayout.cells("b").attachAccordion();
    dhxAccord_main.addItem("a1", "事项主题");
    dhxAccord_main.addItem("a2", "过程进展");
	dhxAccord_main.addItem("a3", "附件");
	dhxAccord_main.openItem("a1");
	dhxAccord_main.cells("a2").hide();
	dhxAccord_main.cells("a3").hide();
	dhxAccord_main.attachEvent("onActive", function(itemId) {
		dhxAccord_main.cells("a2").hide();
		dhxAccord_main.cells("a3").hide();

    });
	dhxtabbar = dhxAccord_main.cells("a2").attachTabbar();
	dhxtabbar.setImagePath("<?php echo $dhtmlx_tabbar?>imgs/");
	dhxtabbar.setSkin("modern");
	dhxtabbar.enableTabCloseButton(true);
	dhxtabbar.setHrefMode("iframes-on-demand");

	view = new dhtmlXDataView({
		container: "data_container",
		edit:b_wk,
		type: {
			template: "<span class='dhx_strong'>#wk_status#&nbsp;&nbsp;&nbsp;#wk_work#</span><span class='dhx_light'>#wk_lasttime#--#wk_uname#</span>",
			template_edit: "<input onkeydown='if(event.keyCode==27){view.stopEdit(true)}' class='dhx_item_editor' bind='obj.wk_work'>",
			height: 50
		}
	});
	view.attachEvent("onItemClick",viewclick);
	view.load("loaddata.php?act=loadmain");


	//grid
	var config = new Object();
	config.header="&nbsp;,状态,&nbsp;,工作内容,开始<br>时间,要求完成<br>时间,承办单位<br>/部门,配合单位<br>/部门,督办人";
	config.width="30,50,30,*,80,80,130,130,60";
	config.align="center,center,center,left,center,center,center,center,center";
	config.type="sub_row_grid,ro,link,txt,dhxCalendar,dhxCalendar,ro,ro,ro";
	config.tips="false,false,false,true,false,false,false,false,false";
	config.ids=",wk_status,,wk_work,wk_startdate,wk_enddate,,,";

	mygrid = dhxAccord_main.cells("a1").attachGrid();

	mygrid.setImagePath("<?php echo $dhtmlx_grid;?>imgs/");
	mygrid.setHeader(config.header);
	
	mygrid.setInitWidths(config.width);
	mygrid.setColAlign(config.align);
	mygrid.setColTypes(config.type);
	mygrid.enableTooltips(config.tips);
	mygrid.setColumnIds(config.ids);
	mygrid.setDateFormat("%Y-%m-%d");
	mygrid.enableMultiline(true);
	mygrid.attachEvent("onRowSelect", function(id,ind){
			dhxToolbar1.enableItem("star");
			dhxToolbar1.enableItem("process");

			$.get
			(
				"ajax.php",
				{t:"star",wk_id:id,act:"load"},
				function (data){
					if (data==""){
						dhxToolbar1.disableItem("star");
					}else if (data=="0"){
						dhxToolbar1.setItemText("star","关注");
						dhxToolbar1.setItemImage("star","star.png");
					}
					else{
						dhxToolbar1.setItemText("star","取消关注");
						dhxToolbar1.setItemImage("star","star_dis.png");
					}
						
				}
			)
			if (my_item_status(id)=="0")
				dhxToolbar1.showItem("out");
			else
				dhxToolbar1.hideItem("out");

			$.get
			(
				"ajax.php",
				{t:"my_item_uid",pid:id},
				function (data){
					if (data=="1"){
						dhxToolbar1.enableListOption("wk_m", "delete");
					}
					else
					{
						dhxToolbar1.disableListOption("wk_m", "delete");
					}
						
				}
			)				

	});

	mygrid.attachEvent("onSubGridCreated",function(subgrid,rowid,rowindex,content){
		var my_item=my_item_status(rowid);
		if (my_item!="0" && my_item!="1") //未发布或进行中 可以修改
			return true;
		subgrid.setImagePath("<?php echo $dhtmlx_grid;?>imgs/");
		subgrid.setHeader("负责部门,负责人,是否配合部门,<a href='javascript:subgrid_new("+rowid+")'>新增</a>,did,uid,wf_id");
		subgrid.setColTypes("ro,ro,ch,link,ro,ro,ro");
		subgrid.setInitWidths("100,200,100,50,0,0,0");
		subgrid.setColAlign("center,center,center,center,center,center,center");
		subgrid.attachEvent("onRowDblClicked", deptRowDblClicked);
		subgrid.attachEvent("onEditCell", doOnCellEdit);

		
		subgrid.setSkin("xp");
		subgrid.init();
		if (!isNull(content))
		{
			content=content.replace(/\'/g,"\"");
			var js = eval ("(" + content + ")");
			subgrid.parse(js,"json");
		}
		dp_subgrid.init(subgrid);
		subgrid.enableAutoHeight(true);
		return false;  // block default behavior
	});
	
	mygrid.enableKeyboardSupport(false);
	//mygrid.enableColumnAutoSize(true);
	mygrid.attachEvent("onRowDblClicked", mygridRowDblClicked);

	mygrid.enableDistributedParsing(true, 20, 250);
	mygrid.init();
	//mygrid.adjustColumnSize(3);
	mygrid.setSkin("light");
	
	

	var dp_view = new dataProcessor("db_view.php");
	dp_view.init(view);

	var dp_grid = new dataProcessor("db_grid.php");
	dp_grid.enableDataNames(true);
	dp_grid.init(mygrid);
	dp_grid.attachEvent("onAfterUpdate", function(sid, action, tid, tag){
	   if (action == "insert"){
			   mygrid.cells(tid, 2).setValue("<img src='<?php echo IMAGES;?>attach_dis.png' width='20px' height='20px'></img>^javascript:open_attach("+tid+");^_self");
			   
	   }
	});
	
	var dp_subgrid = new dataProcessor("db_subgrid.php");
	dp_subgrid.attachEvent("onAfterUpdate", function(sid, action, tid, tag){
		if (action=='insert')
		{
			this.obj.cells2(0,3).setValue("删除^javascript:subgrid_del("+this.obj.cells2(0,6).getValue()+","+tid+");^_self");
		}
		var arr_s = new Array();
		var arr_s1 = new Array();
		var n=0,k=0;
		for (var i=0; i<this.obj.getRowsNum(); i++)
		{
			if (this.obj.cells2(i,2).getValue()=="1")
				arr_s[n++]="【"+this.obj.cells2(i,0).getValue()+"】"+this.obj.cells2(i,1).getValue();
			else
				arr_s1[k++]="【"+this.obj.cells2(i,0).getValue()+"】"+this.obj.cells2(i,1).getValue();
		}
		mygrid.cells(this.obj.cells2(0,6).getValue(),6).setValue(arr_s1.join("<br>"));
		mygrid.cells(this.obj.cells2(0,6).getValue(),7).setValue(arr_s.join("<br>"));
	});
	loaditem("my_work",0);
	
	if (b_wk==0)
	{
		dhxToolbar.hideItem("new");
		dhxToolbar.hideItem("delete");
	}

function my_item_status(wk_id,cat) //我的督办
{
	b_edit="";
	$.get
	(
		"ajax.php",
		{t:"out",wk_id:wk_id,cat:cat},
		function (data){
			b_edit=data;
		}
	)

	return b_edit;

}
function mygridRowDblClicked(rowId,cellIndex)
{
	var my_item=my_item_status(rowId);
	if (my_item=="0" || my_item=="1") //未发布或进行中 可以修改
		return true;
	else
		return false;
}
function add_split()
{
	dhxToolbar1.addSpacer("sep4");
	dhxToolbar1.disableItem("star");
	dhxToolbar1.disableItem("process");
	dhxToolbar1.hideItem("out");
	dhxToolbar1.hideItem("outall");
	
	dhxToolbar1.disableListOption("wk_m", "new");	
	dhxToolbar1.disableListOption("wk_m", "delete");
	if (b_wk==0) //没权限新增
		dhxToolbar1.disableListOption("wk_m", "new_single");	
}
function doOnCellEdit(stage, rowId, cellInd) {
	dp_subgrid.obj=this;
	return true;
}

function showuserdlg(uids)
{
	var r;
	r= window.showModalDialog("../mods/userdlg_r.php","", "dialogHeight:320px;dialogWidth:380px;scroll:no;status:no");
	if(typeof r != "undefined")
	{
		return r;
		//alert(r.phone);
		//document.getElementById("to_phones").value=r.phone;
	}
	return false;
}

function showdeptdlg()
{
	var r;
	r= window.showModalDialog("../mods/deptdlg1.php?type=r", "", "dialogHeight:320px;dialogWidth:380px;scroll:no;status:no");
	if(typeof r != "undefined")
	{
		return r;
		//alert(r.phone);
		//document.getElementById("to_phones").value=r.phone;
	}
	return false;
}
function doOnCheck(rowId, cellInd, state) {
	dp_subgrid.obj=this;
	return true;
}
function subgrid_new(subid)
{
	sg=mygrid.cells(subid,0).getSubGrid();
	dp_subgrid.obj=sg;
	//sg.attachEvent("onCheck", doOnCheck);
	sg.addRow((new Date()).valueOf(),["选择部门","选择负责人","0","","0","0",subid],0);

}
function subgrid_del(subid,rid)
{
	sg=mygrid.cells(subid,0).getSubGrid();
	dp_subgrid.obj=sg;

	$.get
	(
		"ajax.php",
		{t:"del_work_dept",wk_id:subid,aid:rid},
		function (data){
			if (data=="0"){
				alert("不能删除,该负责人已经填写过进展记录");
				return;
			}
			else
				sg.deleteRow(rid);
		}
	)
	
}
function deptRowDblClicked(rowId,cellIndex)
{
	dp_subgrid.obj=this;
	//alert(this.getSelectedId());
	if (cellIndex == 1)
	{
		var uids=this.cells(rowId,5).getValue();
		obj_emp = showuserdlg(uids);
		if (obj_emp)
		{
			this.cells(rowId,1).setValue(obj_emp.name);
			this.cells(rowId,5).setValue(obj_emp.id);
			dp_subgrid.setUpdated(rowId,true);
			obj_emp = null;
		}
	}
	if (cellIndex == 0)
	{
		var obj_dept = showdeptdlg();
		if (obj_dept)
		{
			this.cells(rowId,0).setValue(obj_dept.name);
			this.cells(rowId,4).setValue(obj_dept.id);
			dp_subgrid.setUpdated(rowId,true);
			obj_dept = null;
		}
	}
	return false;
}
function viewclick(id, ev, html)
{
	dhxAccord_main.cells("a1").setText(view.get(id).wk_work);

	loaditem("view",id);

	dhxAccord_main.openItem("a1");
	dhxAccord_main.cells("a2").hide();
	dhxAccord_main.cells("a3").hide();
	add_split();
	$.get
	(
		"ajax.php",
		{t:"my_item_uid",pid:id},
		function (data){
			if (data==1){
				
				dhxToolbar1.enableListOption("wk_m", "new");
			}
			else
			{
				dhxToolbar1.disableListOption("wk_m", "new");
			}
				
		}
	)


	if (my_item_status(id,"view")=="0")
		dhxToolbar1.showItem("outall");
	else
		dhxToolbar1.hideItem("outall");
}

function loaditem(act,id,key)
{
	//部门事项：id(部门id)
	//主题事项：id(主题事项id)
	$.get
	(
		"ajax.php",
		{t:"loaditem",act:act,id:id,key:key},
		function (data){
		//$("#info").val(data);
			if (data!=""){
				if (data=="-1000")
				{
					alert("超时，请重新登录");
					return;
				}
				eval (data);
				mygrid.clearAll();
				mygrid.parse(dataWk,"json");
			}
		}
	)
	if (act=="my_star")
		dhxAccord_main.cells("a1").setText("我的关注");
	if (act=="my_item")
		dhxAccord_main.cells("a1").setText("我的督办");
	if (act=="my_work")
		dhxAccord_main.cells("a1").setText("我的事项");

	if (act=="wk_all")
		dhxAccord_main.cells("a1").setText("全部事项");
	if (act=="wk_1")
		dhxAccord_main.cells("a1").setText("进行中事项");
	if (act=="wk_2")
		dhxAccord_main.cells("a1").setText("已完成事项");
	if (act=="wk_3")
		dhxAccord_main.cells("a1").setText("未完成事项");
	if (act=="wk_0")
		dhxAccord_main.cells("a1").setText("未发布事项");
	if (act=="wk_delay")
		dhxAccord_main.cells("a1").setText("超期事项");
	if (act=="search")
		dhxAccord_main.cells("a1").setText("搜索结果");

}
function loadAttach(wk_id)
{
	$.get
	(
		"ajax.php",
		{t:"loadAttach",pid:id},
		function (data){
			if (data!=""){
				eval (data);
				mygrid.clearAll();
				mygrid.parse(dataWk,"json");
			}
				
		}
	)
}
function open_attach(wk_id)
{
	if(wk_id != null)
	{
		dhxAccord_main.cells("a2").hide();
		dhxAccord_main.cells("a3").show();
		dhxAccord_main.openItem("a3");
		dhxAccord_main.cells("a3").attachURL("attach.php?wk_id="+wk_id);
	}
}
</script>
