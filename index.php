<!DOCTYPE html>
<html>
<title>Remote System Info</title>
<head>
<link rel="stylesheet" href="./css/bootstrap.min.css">
<link rel="stylesheet" href="./css/easypiechart.css">
<link rel="stylesheet" href="./css/jquery-ui.min.css">
<script src="./js/jquery-2.0.3.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src="./js/jquery.easypiechart.min.js"></script>
<script src="./js/jquery-ui.min.js"></script>

<script>
// check if server is offline or online first

var reload_interval;
var cpuLoadContent;

function reload() {
	$.getJSON("./php/serverlist.php", function(data){
		$('#sortable').empty();
		for (var i = 0; i < data.length; i++) {

			if (data[i].status === "offline") {
				data[i].cpuload = "0";
				data[i].cputemp = "0";
				data[i].cputotal = "0";
				data[i].cpuclock = "0";
				data[i].memoryload = "0";
				data[i].memoryused = "0";
				data[i].hddtemp = "0";
				data[i].hddused = "0";
			}

			cpuload_array = $.grep(data[i].cpuload.split(";"),function(n){ return(n) });
			cputemp_array = $.grep(data[i].cputemp.split(";"),function(n){ return(n) });
			cpuclock_array = $.grep(data[i].cpuclock.split(";"),function(n){ return(n) });
			hddtemp_array = $.grep(data[i].hddtemp.split(";"),function(n){ return(n) });
			hddused_array = $.grep(data[i].hddused.split(";"),function(n){ return(n) });
			var coreNum = cpuload_array.length;
			
			var styleStatus = "";
			if (data[i].status === "online") {
				styleStatus = '<span style="color:#5CB85C;font-weight:bold">Online</span>';
			} else {
				styleStatus = '<span style="color:#D9534F;font-weight:bold">Offline</span>';
			}

			var content = 
			'<li id="item_'+(i+1)+'" style="float:left;margin-left:10px;padding-left:0px;min-width:330px;"><table class="table-bordered" id="pcname-'+data[i].pcname+'">'+
			'<tr><td colspan="2" style="text-align:center;background-color:#EAEAEA;cursor:move;">PC name: '+data[i].pcname+'</td></tr>'+
			'<tr><td colspan="2" style="text-align:center;">Status: '+styleStatus+'</td></tr>'+
			'<tr><td colspan="2" style="text-align:center;">Number of cores: '+coreNum+'</td></tr>';
			
			content = content +
			'<tr id="cputotal"><td colspan="2">'+
			'<div>Total CPU Load</div><span class="chart-'+data[i].pcname+'" data-percent="'+Math.floor(Number(data[i].cputotal.replace(",",".").replace(";","")))+'"><span class="percent"></span></span>'+
			'</td></tr>';
			
			for (var j = 0; j < cpuload_array.length; j++) {
				content = content +
				'<tr>'+
				'<td>CPU Load #'+(j+1)+'</td>'+
				'<td><div class="progress"><div id="cpu-load-'+(j+1)+'" class="progress-bar progress-bar-success" role="progressbar" style="width:'+Number(cpuload_array[j].replace(",","."))+'%"><span>'+Math.floor(Number(cpuload_array[j].replace(",",".")))+'%</span></div></div></td>'+
				'</tr>';
			}
			for (var j = 0; j < cputemp_array.length; j++) {
				content = content +
				'<tr>'+
				'<td>CPU Temp #'+(j+1)+'</td>'+
				'<td><div class="progress"><div id="cpu-temp-'+(j+1)+'" class="progress-bar progress-bar-success" role="progressbar" style="width:'+Number(cputemp_array[j].replace(",","."))+'%"><span>'+Math.floor(Number(cputemp_array[j].replace(",",".")))+'%</span></div></div></td>'+
				'</tr>';
			}

			content = content +
			'<tr><td>Memory Load</td>'+
			'<td><div class="progress"><div id="memoryload" class="progress-bar progress-bar-success" role="progressbar" style="width:'+Number(data[i].memoryload.replace(",",".").replace(";",""))+'%"><span>'+Math.floor(Number(data[i].memoryload.replace(",",".").replace(";","")))+'%</span></div></div></td>';

			for (var j = 0; j < hddtemp_array.length; j++) {
				content = content +
				'<tr>'+
				'<td>HDD Temp #'+(j+1)+'</td>'+
				'<td><div class="progress"><div id="hddtemp-'+(j+1)+'" class="progress-bar progress-bar-success" role="progressbar" style="width:'+Number(hddtemp_array[j].replace(",","."))+'%"><span>'+Math.floor(Number(hddtemp_array[j].replace(",",".")))+'%</span></div></div></td>'+
				'</tr>';
			}
			for (var j = 0; j < hddused_array.length; j++) {
				content = content +
				'<tr>'+
				'<td>HDD Used #'+(j+1)+'</td>'+
				'<td><div class="progress"><div id="hddused-'+(j+1)+'" class="progress-bar progress-bar-success" role="progressbar" style="width:'+Number(hddused_array[j].replace(",","."))+'%"><span>'+Math.floor(Number(hddused_array[j].replace(",",".")))+'%</span></div></div></td>'+
				'</tr>';
			}

			content = content + '</table></li>';

			$("#sortable").append(content);
		}

		$("span[class^='chart']").each(function() {
			var dataPercent = Number($(this).attr("data-percent"));
			if (dataPercent > 80) {
				barColor = "#D9534F";
			} else if (dataPercent > 50) {
				barColor = "#F0AD4E";
			} else {
				barColor = "#5CB85C";
			}
			$("#sortable").append("<script>$(function() { $('."+$(this).attr("class")+"').easyPieChart({ scaleColor: false, animate: 1, barColor: '"+barColor+"', trackColor: '#E6E6E6', lineWidth: '8', onStep: function(from, to, percent) { $(this.el).find('.percent').text(Math.round(percent)); } }); });"+"</"+"script>");
		});
		
		$(".progress-bar span").each(function(){
			var currentProgress = $(this).text().replace("%","");
			if (currentProgress > 80) {
				$(this).parent().removeClass("progress-bar-warning");
				$(this).parent().addClass("progress-bar-danger");
				$(this).parent().removeClass("progress-bar-success");
			} else if (currentProgress > 50) {
				$(this).parent().addClass("progress-bar-warning");
				$(this).parent().removeClass("progress-bar-danger");
				$(this).parent().removeClass("progress-bar-success");
			} else {
				$(this).parent().removeClass("progress-bar-warning");
				$(this).parent().removeClass("progress-bar-danger");
				$(this).parent().addClass("progress-bar-success");
			}
		});

		$("#sortable").append(
			"<script>$(function() { if (localStorage.getItem('sorted') !== null) {"+
					"var arrValuesForOrder = localStorage.getItem('sorted').substring(7).split('&item[]='); var $ul = $('#sortable'); $items = $('#sortable').children();"+
					"for (var i = arrValuesForOrder.length - 1; i >= 0; i--) { $ul.prepend( $items.get((arrValuesForOrder[i] - 1)) ); }"+
			"} });"+"</"+"script>"
		);

	});

}

reload();

function refresh(func, freq) {
	clearInterval(reload_interval);
	reload_interval = setInterval(func, freq);
}

//refresh(reload, 5000);

</script>

<script>
$(function() {
	$( "#sortable" ).sortable({
//		placeholder: "ui-state-highlight"
	});
	$("#sortable").on("sortupdate", function(event, ui) {
		var sorted = $(this).sortable("serialize");
		console.log(sorted);
		localStorage.setItem('sorted', sorted) ;
	});
	$( "#sortable" ).disableSelection();
});

</script>

</head>
<body>

<ul id="sortable" style="display:inline;list-style:none;">
Connecting to database...
</ul>

</body>
</html>