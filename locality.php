<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title id = "title"> </title>
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="css/css.css" />
	<link rel="stylesheet" href="css/locality.css" />
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTBYMJIfb4DMSGHl1681W0jLOOQSjP7MA&libraries=geometry,places"> </script>
	<script type="text/javascript" src="jquery/jquery.min.js"> </script>
	<link rel="stylesheet" href="jquery/jquery-ui.css">
	<script src="jquery/jquery-ui.min.js"></script>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<script type="text/javascript" src="js/manajax.js"> </script>
	<script src="js/oms.min.js"></script>

	<script type="text/javascript">
		var url = window.location.href;
		if (url.slice(-1) == '#') url =  url.substr(0,url.length-1);
		var nloc = url.substr(url.indexOf('?')+1,url.length-url.indexOf('?')-3);
		Langsel = url.substr(url.length-2, 2)
	</script>

	<script type="text/javascript" src="js/language.js"> </script>
	<script type="text/javascript" src="js/locality.js"> </script>
	<script type="text/javascript" src="js/js.js"> </script>
	<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
	<script src="js/jquery.translator.js"></script>

	<!--Script for windows-1252 encoding export  -->
	<script>
		// 'Copy' browser build in TextEncoder function to TextEncoderOrg (because it can NOT encode windows-1252, but so you can still use it as TextEncoderOrg()  )
		var TextEncoderOrg = window.TextEncoder;
		// ... and deactivate it, to make sure only the polyfill encoder script that follows will be used
		window.TextEncoder = null;
	</script>
	<script src="lib/encoding-indexes.js"></script>
	<script src="lib/encoding.js"></script>

	<link rel="stylesheet" type="text/css" href="css/cookies.css" />
	<script src="js/cookieconsent.min.js"></script>

</head>

<div id="loading" ><br><strong>Loading....</strong></div>

<body onresize="resizeMapLoc()" onload="InitializeLoc()">
	<div id="content">
		<div id="tdCursor"></div>
		<div id="NumSel"></div>
		<div id="FakeGraph">
		</div>
		<div id="IntGraph"></div>
		<div id="IntGraphRed">
			<a href="#" id="ReduceGraph"></a>
		</div>
		<div id="IntGraphEnl">
			<a href="#" id="EnlargeGraph"></a>
		</div>
		<div id="SaveIcon">
		</div>

		<div id="leftside">
			<?php include("html/export.html"); ?>

			<div id="topcolor" class="localityColor">
				<div id="pagetype" class="localityFontColor"><span id="pagetypeLOC"></span></div>
				<?php include("html/topmenu.html"); ?>
			</div>

			<div id="Intro"></div>
			<div id="WikiLink"></div>



			<div id="feltrep" class="feltrep">
					<table id="Loc_info">
						<thead>
							<tr>
								<th id="int" class="int"></th>
								<th id="nat" class="nat"></th>
								<th id="date" class="date"></th>
								<th id="time" class="time"></th>
								<th id="io" class="io"></th>
								<th id="imax" class="imaxLoc"></th>
								<th id="sites" class="sites"></th>
								<th id="me" class="me"></th>
								<th id="location" class="location"></th>
								<th id="emap" class="emap"></th>
							</tr>
						</thead>
						<tbody id="loc_data" class="tbodyblock"></tbody>
					</table>
			</div>
			<!-- caso  EE non in PQ e associati a nperiod-->
			<div id="EEnperiod" class="feltrep">
				<div><span id="EEnperiod_title"></span></div>
				<br />
				<table id="Loc_info_NP">
					<thead>
						<tr>
							<!-- <th id="int" class="int"></th> -->
							<th id="natNP" class="nat"></th>
							<th id="dateNP" class="dateNP"></th>
							<th id="timeNP" class="time"></th>
							<th id="ioNP" class="io"></th>
							<!-- <th id="imax" class="imax"></th> -->
							<!-- <th id="sites" class="sites"></th> -->
							<th id="meNP" class="me"></th>
							<th id="locationNP" class="locationNP"></th>
							<th id="emapNP" class="emap"></th>
						</tr>
					</thead>
					<tbody id="Loc_info_NP_data" class="tbodyblock"</tbody>
				</table>
			</div>
		</div>
		<?php include("html/banlic.html"); ?>

		<?php include("html/legendEPI.html"); ?>
		<?php include("html/legendPQ.html"); ?>

		<div id = "legendmin">
			<a href="#" id="bigger"></a>
			<div id = "legendmintext"><b>Legenda</b></div>
		</div>


		<div id="map"></div>

		<div id="legend"></div>
	</div>
	<?php include("html/MapLayers_Gsearch_Strum.html"); ?>
	<?php include("html/dh.html"); ?>
</body>
</html>
