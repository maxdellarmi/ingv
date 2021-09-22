<!DOCTYPE html>
<html>
<head>
    <?php
    $site = new stdClass();
    $site->name = 'prova';
    $site->longitude = 12.6508;
    $site->latitude = 42.5681;
    $markers_strat = [12.6508, 42.5681];
    $markers_cross = [ [12.4746484, 41.7660824],[12.4698199, 41.77023]];
    $markers_subs = [12.6508, 42.5681];
    $out = array_values($markers_strat);
    $varjvs= json_encode($out);

    /*<Locs>
  <Loc>
    <nloc_cfti>080299.00</nloc_cfti>
    <desloc_cfti>Aarau</desloc_cfti>
    <provlet></provlet>
    <nazione>Switzerland</nazione>
    <risentimenti>2</risentimenti>
    <ee>0</ee>
    <maxint>4</maxint>
    <lat_wgs84>47.3913</lat_wgs84>
    <lon_wgs84>8.0359</lon_wgs84>
    <notesito></notesito>
  </Loc>*/

    //legge file xml
    $objXmlDocument = simplexml_load_file("LocList.xml");

    if ($objXmlDocument === FALSE) {
        echo "There were errors parsing the XML file.\n";
        foreach(libxml_get_errors() as $error) {
            echo $error->message;
        }
        exit;
    }

    //Convert the SimpleXMLElement Object Into Its JSON Representation
    $objJsonDocument = json_encode($objXmlDocument);
    //Decode the JSON String Into an Array
    $arrOutput = json_decode($objJsonDocument, TRUE);


    /*    //ESECUZIONE QUERY SULL XML dei terremoti dopo il 1950 e in italia
    function filter($item) {
        return ($item['anno'] >= 1950 && $item['country'] == "Italy" );
    }*/
    /*function filter($item) {
        return ($item['country'] == "Italy" );
    }*/
    function filter($item): bool
    {
        //return true; //ritorna sempre true //SEMBRA non funzionare!!! Serve sempre imporre una consizione nel filter....
        //COSI FUNZIONA

        //return ($item['maxint'] >= 0 && $item['maxint'] >= 4   );
        return ($item['maxint'] >= 0 );
    }
    $filteredLocality = array_filter($arrOutput["Loc"], 'filter');
    $elementArray = array();

    //LOOP ATTRAVERSO LA CHIAVE PER INDICE DI ARRAY ASSOCIATIVO
    foreach ($filteredLocality as $key => $value) {
        //echo $key;
        $convertCoordinates = [];
        array_push($convertCoordinates, (float)$value["lon_wgs84"], (float)$value["lat_wgs84"]); //aggiunge 2 elementi all'array
        //$coordinates = json_encode($convertCoordinates); //questa istruzione converte in stringa non andava bene.


        $element = new stdClass();
        $element->name = $value["nazione"];
        $element->description = $value["desloc_cfti"];
        $element->coordinates = $convertCoordinates;//$coordinates;
        $element->url = "http://www.google.it";
        //$element->key = $key; //SE SI VUOLE AGGIUNGERE LA CHIAVE
        //aggiungo elemento nell'array
        $elementArray[] = $element;
        //DEBUG INFORMAZIONI
        //print_r($elementArray);
    }

    ?>
    <!--sezione mappa OL begin-->
    <script src="/plugins/global/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.0.1/ol.js"></script>
    <script src="/plugins/global/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/plugins/global/js.cookie.min.js" type="text/javascript"></script>
    <script src="/plugins/global/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="/plugins/global/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="/plugins/global/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="/plugins/global/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log(center);

            var center =[12.6508, 42.5681];

            var markers = [];
            var markersCoords;
            var vectorLayer;
            var map;
            console.log("carico i dati");

            var markersCoords =JSON.parse('<?php echo json_encode($elementArray, true) ?>');

            $.when(markersCoords).then(function( dummy ) {
                //alert( "I fired immediately" );
                console.log("prima esecuzione");
                console.log(markersCoords);
                markersCoords.map(function (item, index) {
                    var marker = new ol.Feature({
                        geometry: new ol.geom.Point(ol.proj.fromLonLat(item.coordinates)),
                        name: item.name,
                        description: item.description,
                        url: item.url
                    });

                    marker.setStyle(new ol.style.Style({
                        image: new ol.style.Icon(({
                            color: '#fffb22',
                            src: '/img/dot.png',
                            // the real size of your icon
                            size: [20, 20],
                            // the scale factor
                            scale: 0.5
                        }))
                    }));
                    markers.push(marker);
                })
            }).then(  function (x) {
                console.log("seconda esecuzione");
                console.log(markers);
                vectorLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: markers,
                    })
                });
                console.log(vectorLayer);
            }).done ( function (x)  {
                console.log("terza esecuzione");
                console.log(vectorLayer);
                map = new ol.Map({
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false
                        })
                    }),
                    layers: [
                        new ol.layer.Tile({
                            source: new ol.source.OSM()
                        }), vectorLayer
                    ],
                    target: document.getElementById('map'),
                    view: new ol.View({
                        center: ol.proj.fromLonLat(center),
                        zoom: 6,
                    })
                });

                var element = document.getElementById('popup');
                var popup = new ol.Overlay({
                    element: element,
                    positioning: 'bottom-center',
                    stopEvent: true,
                    offset: [0, -20],
                    autoPan: true,
                    autoPanAnimation: {
                        duration: 250
                    }
                });
                map.addOverlay(popup);

                // display popup on click
                map.on('click', function (evt) {
                    var feature = map.forEachFeatureAtPixel(evt.pixel,
                        function (feature) {
                            return feature;
                        });

                    if (feature) {
                        $(element).popover('destroy')
                        var coordinates = feature.getGeometry().getCoordinates();
                        popup.setPosition(coordinates);
                        $(element).popover({
                            'placement': 'top',
                            'animation': false,
                            'html': true,
                            'trigger': 'manual',
                            'content': '<div style="min-width:200px"><h4>' + feature.get('name') + '</h3>' + '<p>' + feature.get('description') + '</p>' + '<a href="' + feature.get('url') + '" class="details_lang" id="details">Details</a>'
                        });
                        $(element).popover('show');
                    } else {
                        $(element).popover('destroy');
                        popup.setPosition(undefined);
                    }

                });
                // change mouse cursor when over marker
                map.on('pointermove', function (e) {
                    if (e.dragging) {
                        $(element).popover('hide');
                        return;
                    }
                    var pixel = map.getEventPixel(e.originalEvent);
                    var hit = map.hasFeatureAtPixel(pixel);
                    map.getTarget().style.cursor = hit ? 'pointer' : '';
                });

                /****solo con zoom maggiore o uguale a 8 faccio vedere i punti***/
                map.on('moveend', function (event) {
                    console.log(map.getView().getZoom());
                        if (map.getView().getZoom() >= 7) {
                            vectorLayer.setVisible(true);
                        }
                        else {
                            vectorLayer.setVisible(false);
                        }
                });
            });

            /*var markersCoords = [
                {
                    name: "Name A",
                    description: "Description A",
                    coordinates: [15.942361, 40.786657],
                    url: "http://google.it"
                },
                {
                    name: "Name B",
                    description: "Description B",
                    coordinates: [15.227715,37.256637],
                    url: "http://google.it"
                },
            ];*/


            /*

            markersCoords.push(pippo);

            console.log("prima esecuzione");
            markersCoords.map(function (item, index) {
                var marker = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat(item.coordinates)),
                    name: item.name,
                    description: item.description,
                    url: item.url
                });

                marker.setStyle(new ol.style.Style({
                    image: new ol.style.Icon(({
                        color: '#ff5722',
                        src: '/img/dot.png'
                    }))
                }));
                markers.push(marker);
            });
            vectorLayer = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: markers,
                })
            });

            vectorLayer.visible=true;
            console.log(vectorLayer);

            map = new ol.Map({
                controls: ol.control.defaults({
                    attributionOptions: ({
                        collapsible: false
                    })
                }),
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    }), vectorLayer
                ],
                target: document.getElementById('map'),
                view: new ol.View({
                    center: ol.proj.fromLonLat(center),
                    zoom: 6,
                })
            });

            var element = document.getElementById('popup');
            var popup = new ol.Overlay({
                element: element,
                positioning: 'bottom-center',
                stopEvent: true,
                offset: [0, -20],
                autoPan: true,
                autoPanAnimation: {
                    duration: 250
                }
            });
            map.addOverlay(popup);

            // display popup on click
            map.on('click', function (evt) {
                var feature = map.forEachFeatureAtPixel(evt.pixel,
                    function (feature) {
                        return feature;
                    });

                if (feature) {
                    $(element).popover('destroy')
                    var coordinates = feature.getGeometry().getCoordinates();
                    popup.setPosition(coordinates);
                    $(element).popover({
                        'placement': 'top',
                        'animation': false,
                        'html': true,
                        'trigger': 'manual',
                        'content': '<div style="min-width:200px"><h4>' + feature.get('name') + '</h3>' + '<p>' + feature.get('description') + '</p>' + '<a href="' + feature.get('url') + '" class="details_lang" id="details">Details</a>'
                    });
                    $(element).popover('show');
                } else {
                    $(element).popover('destroy');
                    popup.setPosition(undefined);
                }

            });
            // change mouse cursor when over marker
            map.on('pointermove', function (e) {
                if (e.dragging) {
                    $(element).popover('hide');
                    return;
                }
                var pixel = map.getEventPixel(e.originalEvent);
                var hit = map.hasFeatureAtPixel(pixel);
                map.getTarget().style.cursor = hit ? 'pointer' : '';
            }); */


        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.0.1/ol.css" type="text/css">

    <style>
        .margins { margin-top: 5px; }
        .margins_bottom { margin-bottom: 0px !important; }
        .fontsize {font-size: 10px; }
        .form-control {

            font-size: 12px !important;
            border: 1px solid #c2cad8 !important;
        }
        .control-label {
            margin-top: 1px;
            font-weight: 400;
            font-size: 12px !important;
        }
        .help-block
        {
            color: red;
            opacity: 100 !important;
            font-weight: 400;
            font-size: 10px !important;
        }
        .width_auto
        {
            width: auto;
        }
        .width_fixed
        {
            width: 65px;
        }
        .has-error
        {
            height: 66px;
        }
        .collapse.in {
            display: block;
            margin-top: 10px;
        }

        .button_search
        {

        }

    </style>


    <!--sezione mappa OL end-->
</head>

<div id="loading" ><br><strong>Loading....</strong></div>

<!--       ==============    WMS-WMF LINKS   - CSW METADATA   =============         -->

<body >




<!--sezione mappa OL begin-->
<div class="col-lg-12">
    <!-- portlet -->
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-map"></i>
                <span class="caption-subject bold uppercase" id="geolocalization">Geolocalization</span>
            </div>
        </div>
        <div class="portlet-body">

            <div id="map" style="height: 713px">

            </div>
            <div id="popup"></div>
        </div>
    </div>
    <!-- /portlet -->
</div>
<!--sezione mappa OL end-->


</body>
</html>
