<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title id = "title"> </title>
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="css/css.css" />
	<link rel="stylesheet" href="css/quake.css" />
	<script type="text/javascript" 	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTBYMJIfb4DMSGHl1681W0jLOOQSjP7MA&libraries=geometry,places"> </script>
	<script type="text/javascript" src="jquery/jquery.min.js"> </script>
	<link rel="stylesheet" href="jquery/jquery-ui.css">
	<script src="jquery/jquery-ui.min.js"></script>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<script type="text/javascript" src="js/manajax.js"> </script>
	<script src="js/oms.min.js"></script>

	<script type="text/javascript">
		var url = window.location.href;
		if (url.slice(-1) == '#') var nchar = url.length - 6 -2;
		else var nchar = url.length - 5 -2;
		var Nterr = url.substr(nchar,5);
		Langsel = url.substr(nchar+5, 2)
	</script>

	<script type="text/javascript" src="js/language.js"> </script>
	<script type="text/javascript" src="js/quake.js"> </script>
	<script type="text/javascript" src="js/js.js"> </script>
	<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
	<script src="js/jquery.translator.js"></script>
	<script type="text/javascript" src="js/pdfobject.js"></script>

	<link rel="stylesheet" type="text/css" href="css/cookies.css" />
	<script src="js/cookieconsent.min.js"></script>



</head>

<div id="loading" ><br><strong>Loading....</strong></div>

<body onresize="resizeMapQuake()" onload="InitializeQuake()">
	<div id="content">

		<div id="tdCursor"></div>

		<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


		<div id="leftside">

			<div id="topcolor" class="quakeColor">
				<div id="pagetype" class="quakeFontColor"><span id="pagetypeEQ"></span></div>
				<?php include("html/topmenu.html"); ?>
			</div>
			<div id = "locationString"></div>

			<div id="Intro" >
				<div id='nperiodExplanation'></div>
				<table id="quake_info">
					<thead>
						<tr>
							<th id="date" class="dateEQ"></th>
							<th id="time" class="timeEQ"></th>
							<th id="io" class="ioEQ"></th>
							<th id="imax" class="imaxEQ"></th>
							<th id="sites" class="sitesEQ"></th>
							<th id="me" class="meEQ"></th>
							<th id="lat" class="latEQ"></th>
							<th id="lon" class="lonEQ"></th>
							<th id="locationEQ" class="areaEQ"></th>
							<th id="etype" class="etypeEQ"></th>
							<th id="relEQ" class="relEQ"></th>
						</tr>
					</thead>
					<tbody id="quake_data" class="tbodyblock"></tbody>
				</table>
			</div>

			<div id="D0"></div>

			<div id="divLineTopTable">
				<abbr id="abbropenCOMM" title=""><div id="openCOMM">
					<a href="#" id = "commentsICON"><img src="images/comment_icon.png" height= "24px"><span id="readCOMM">Comm.</span></a>
				</div></abbr>

				<div id="level"> </div>
				<div id="morti"> </div>

				<?php include("html/export.html"); ?>
				<div id="ASMIlink"> </div>
				<div id="CPTIlink"> </div>
				<div id="NEW"> </div>


			</div>

			<div id="quakePQtable" class="quakePQtable">
					<table id="PQ_info">
						<thead>
							<tr>
								<th id="int" class="int"></th>
								<th id="nat" class="natEQ"></th>
								<th id="locality" class="locality"></th>
								<th id="latLOC" class="lat"></th>
								<th id="lonLOC" class="lon"></th>
								<th id="dist" class="dist"></th>

							</tr>
						</thead>
						<tbody id="PQ_data" class="tbodyblock"></tbody>
					</table>
			</div>
		</div>

		<div id="commentsWindow" class="ui-widget-content">
			<a href="#" id="closeCW">Close</a>
			<div id="dragpart" onmouseover="$( '#commentsWindow' ).draggable('enable');" onmouseout="$( '#commentsWindow' ).draggable('disable');"><div id="titleCommWin"></div>
			</div>

			<div id="commentsText" onmouseover="$( '#commentsWindow' ).draggable('disable');">
				<!-- <a href="#translate">Translate</a> -->
				<div id='tabs' class="tab"></div>
				<div id="embed" class="tabcontent Gtranslate"></div>
				<!-- <iframe id="embed" src="M1031.pdf" width="100%" height="" border="0" class="tabcontent"></iframe> -->
				<div id="EQparam" class="tabcontent Gtranslate"></div>
				<div id="EQsequence" class="tabcontent Gtranslate"></div>
				<div id="EQreview" class="tabcontent Gtranslate"></div>
				<div id="EQresilience" class="tabcontent Gtranslate"></div>
				<div id="EQscience" class="tabcontent Gtranslate"></div>
				<div id="EQeffectsAnt" class="tabcontent Gtranslate"></div>
				<div id="EQeffectsEnv" class="tabcontent Gtranslate"></div>
				<div id="EQbiblio" class="tabcontent">
					<br>
					<table id="biblio">
						<thead id='biblio_head'>
							<tr>
								<th id="Bauth" class="Bauth"></th>
								<th id="Btitle" class="Btitle"></th>
								<th id="Btype" class="Btype"></th>
								<th id="Byear" class="Byear"></th>
								<th id="Bplace" class="Bplace"></th>
								<th class="Bpdf"><abbr class="biblioEQ_pdfT" id="pdfT" title ="">PDF_T<img src="images/sort.png" width= "8" /></th>
									<!-- <img src="images/pdf_T_icon.png"/></abbr><img src="images/sort.png" width= "8" /> -->
								<th class="Bpdf2"><abbr class="biblioEQ_pdfR" id="pdfR" title ="">PDF_R<img src="images/sort.png" width= "8" /></th>
									<!-- <img src="images/pdf_R_icon.png"/></abbr> -->
							</tr>
						</thead>
						<tbody id="biblio_data"></tbody>
					</table>
				</div>

			</div>
		</div>

		<?php include("html/banlic.html"); ?>
		<?php include("html/legendPQ.html"); ?>



		<div id="map"></div>

	</div>

	<?php include("html/MapLayers_Gsearch_Strum.html"); ?>
	<?php include("html/dh.html"); ?>
</body>
</html>
