<?php
require_once "../maincore.php";
require_once "../subheader.php";
require_once "../side_left.php";
opentable("工作督办--帮助说明");
echo "<table><tr><td>";
echo "<b>工作督办主要流程：开始----新建工作事项----发布事项----过程进展填写----申请事项结束----督办意见填写----完成<p></b>";

echo "一、<b>新建工作事项:</b>分2类<br>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;1.1 新建主题工作督办事项（例如“XXX会议督办事项”,在该主题下可以添加子任务）<br>";
echo "<center>左侧上方工具栏单击“新建”按钮</center><br>";
echo "<center>双击单元格即可修改“会议事项主题”</center><br>";
echo "<center><img src='imgs/1.png'></img></center>";

echo "<center>单击工具栏右侧“管理事项”按钮，选择“新增事项”</center><br>";
echo "<center><img src='imgs/2.png'></img></center>";
echo "<hr>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;1.2 新建特别督办事项（临时需要督办的事项）<br>";
echo "<center>单击工具栏右侧“管理事项”按钮，选择“新增特别事项”</center><br>";
echo "<center><img src='imgs/3.png'></img></center>";
echo "<hr>";


echo "&nbsp;&nbsp;&nbsp;&nbsp;1.3 填写相关工作内容<br>";
echo "<center>双击“工作内容”单元格，填写工作内容</center>";
echo "<center>双击“开始时间”和“结束时间”，选择相应时间</center><br>";
echo "<center>单击“回形针”单元格，添加附件</center><br>";
echo "<center><img src='imgs/4.png'></img></center>";
echo "<hr>";


echo "&nbsp;&nbsp;&nbsp;&nbsp;1.4 选择相关部门和负责人<br>";
echo "<center>单击左侧 “+”</center>";
echo "<center>单击“新增”按钮，双击“负责部门”和“负责人”选择相应部门和负责人</center>";
echo "<center>如果是“配合部门”，在“是否配合部门”打钩</center><br>";
echo "<center><img src='imgs/5.png'></img></center>";
echo "<hr>";

echo "二、<b>发布工作事项:</b><br><font color='red'>发布工作事项后：责任部门和配合部门负责人会收到短信提醒，他们可以根据实际情况填写过程进展</font><br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;2.1 选中左侧的会议主题事项，单击“全部发布”可以同时发布该主题下的所有事项。<br>";
echo "<center><img src='imgs/6.png'></img></center>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;2.1 选中单个事项，单击“发布”可以单独发布被选择的事项。<br>";
echo "<center><img src='imgs/7.png'></img></center>";
echo "<hr>";

echo "三、<b>填写过程进展:</b><br><font color='red'>填写过程进展：事项督办人收到短信提醒</font><br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;3.1 选中单个事项，单击“过程进展”可以填写实际的事项过程进展。<br>";
echo "<center><img src='imgs/8.png'></img></center>";
echo "<center><img src='imgs/9.png'></img></center>";
echo "<hr>";


echo "四、<b>申请事项结束:</b><br><font color='red'>督办人会收到该事项申请结束的短信通知。</font><br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;4.1 责任部门负责人认为该项工作已经结束，单击“申请结束”按钮。<br>";
echo "<hr>";

echo "五、<b>填写督办意见:</b><br><br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;督办人对申请结束的事项填写督办意见。<br>
5.1 <img src='imgs/flag-green.png' width=30px;height=30px></img></center>已完成结束：该事项已按计划完成。<br>
5.2 <img src='imgs/flag-red.png' width=30px;height=30px></img></center>未完成结束：该事项未按计划完成，该任务标记为未完成<p>
5.3 退回申请：该事项未达到要求，退回责任部门。责任部门根据督办意见，继续该工作，并重新填写过程进展，重新申请结束<p>";
echo "<center><img src='imgs/10.png'></img></center>";
echo "<hr>";



echo "六、<b>分类查询:</b><br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;6.1 按事件状态查询：分6种（全部，进行中，已完成，未完成，已超时，未发布）<br>";
echo "<center><img src='imgs/11.png'></img></center><hr><p><p><p>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;6.2 按事件类别查询：<br>";
echo "<center><img src='imgs/12.png'></img></center><p><p><p>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;我的事项：责任部门负责人或配合部门负责人和本人有关的事项列表<br>
&nbsp;&nbsp;&nbsp;&nbsp;我的关注：我关注的事项列表<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;可以自定义设置关注事项，选中列表中的事项，单击“关注”按钮，即可关注该事项。<font color='red'>关注事项后，可以收到该事项的最新动态短信提醒。</font><br>";
echo "<center><img src='imgs/14.png'></img></center>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;我的督办：我督办的事项列表<br>
&nbsp;&nbsp;&nbsp;&nbsp;申请结束：已经提交申请结束的事项列表<br>
&nbsp;&nbsp;&nbsp;&nbsp;部门事项：根据部门查询部门事项列表<br>";
echo "<hr>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;6.3 按关键字查询：<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;左边可以按会议事项主题关键字查询：<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;右边可以按事项内容关键字查询：<br>";
echo "<center><img src='imgs/13.png'></img></center>";


echo "<hr>";




echo "</table>";

closetable();

require_once "../side_right.php";
require_once "../footer.php";
?>
