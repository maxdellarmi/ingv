// $(document).ready(function () {
var markers = [];

var mapOL;

var quakeVector;

var rasterLayer;

var localityVector;

function creazioneMappa () {
    $(document).ready(function() {
        try {
            // do some crazy stuff
            var center = [12.6508, 42.5681];


            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });



            console.log('caricati i terremoti test')

            for (var i = 0; i < markersArray.length; i++) {
                markers.push(markersArray[i]['Marker']);
            }
            console.log("carico i markers");
            // console.log(markers);

            quakeVector = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: markers,
                    projection: 'EPSG:4326'
                })
            });

            quakeVector.setVisible(true);


           if (mapOL === undefined) {
               mapOL = new ol.Map({
                   controls: ol.control.defaults({
                       attributionOptions: ({
                           collapsible: false
                       })
                   }),
                   layers: [rasterLayer, quakeVector],
                   target: document.getElementById('mapOL'),
                   view: new ol.View({
                       projection: 'EPSG:4326',
                       center: center,
                       zoom: 6,
                   })
               });
           }
           else {
               //TODO: CLEANUP degli altri layers dalle variabili globali
               console.log("CLEANUP DEI LAYERS VARIABILI GLOBALI DICHIARATE");
               //nascondo gli altri layer che non interessano anche se lo fa da solo e' piu veloce
               if (localityVector!== undefined) {
                   console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                   console.log("CLEANUP pulizia del layer Locality");
                   // quakeVector.setVisible(false);
                   mapOL.removeLayer(localityVector); //meglio rimuovere a mano i layers se rimane reference non li toglie
                   // console.log("CLEANUP pulizia del layer Raster")
                   // mapOL.removeLayer(rasterLayer);
               }
               //////////////////////////////////////////
               /***forza la pulizia dei layer vecchi ***/
                //////////////////////////////////////////
               mapOL.getLayers().forEach(function (layer) {
                   console.log("CLEANUP DEI LAYERS DAL CLICLO DELLA MAPPA");
                   mapOL.removeLayer(layer);
               });
               console.log("ADDING NEW LAYERS");
               mapOL.addLayer(rasterLayer);
               mapOL.addLayer(quakeVector);
           }

            /*
            https://openlayers.org/en/latest/apidoc/module-ol_Map-Map.html
            setLayers(layers) inherited
            Clear any existing layers and add layers to the map.
            */


          /* //HOVER che on mouse over visualizza dati relativi alla feature
            let selected = null;
            const status = document.getElementById('status');

            mapOL.on('pointermove', function (e) {
                if (selected !== null) {
                    //selected.setStyle(undefined);
                    selected = null;
                }

                mapOL.forEachFeatureAtPixel(e.pixel, function (f) {
                    selected = f;
                    //f.setStyle(highlightStyle);
                    return true;
                });

                if (selected) {
                    //status.innerHTML = '&nbsp;Hovering: ' + selected.get('title');
                    alert('Hovering: ' + selected.get('title') +  selected.get('latlon') );
                } else {
                    //alert('&nbsp;');
                }
            });*/

          /*//overlay commentato

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

            mapOL.addOverlay(popup); */

            // display popup on click
            /*mapOL.on('click', function (evt) {
                var feature = mapOL.forEachFeatureAtPixel(evt.pixel,
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
                        // 'content': '<div class = "tooltip-inner" style="min-width:200px"><h4>' + feature.get('name') + '</h3>' + '<p>' + feature.get('description') + '</p>' + '<a href="' + feature.get('url') + '">Details</a>'
                        'content': '<div class = "tooltip-inner" style="min-width:200px"><h4>' + "CONTENUTO DETAILS"</a>'

                    });
                    $(element).popover('show');
                } else {
                    $(element).popover('destroy');
                    popup.setPosition(undefined);
                }
            });*/
            // change mouse cursor when over marker
            console.log('popover su mapOL.js gestione terremoti commentato perche andava in errore ');

            resizeMapIndex();
        } catch (e) {
            console.error(e, e.stack);
        }
    });

}

function indexLocalita () {
    $(document).ready(function() {
        try {
            var center =[12.6508, 42.5681];
            console.log(center);

            console.log("carico i dati");
            console.log("LOC Markers" + LOCMarkers.length);
            //console.log(LOCMarkers);

            localityVector = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: LOCMarkers,
                    projection: 'EPSG:4326'
                })
            });
            localityVector.setVisible(true);

            console.log("localityVector");
            console.log(localityVector);

            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });


            if (mapOL === undefined) {
                mapOL = new ol.Map({
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false
                        })
                    }),
                    layers: [rasterLayer, localityVector],
                    target: document.getElementById('mapOL'),
                    view: new ol.View({
                        projection: 'EPSG:4326',
                        center: center,
                        zoom: 6,
                    })
                });
            }
            else {
                //TODO: CLEANUP degli altri layers dalle variabili globali
                console.log("CLEANUP DEI LAYERS VARIABILI GLOBALI DICHIARATE");
                //nascondo gli altri layer che non interessano anche se lo fa da solo e' piu veloce
                if (quakeVector!== undefined) {
                    console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                    console.log("CLEANUP pulizia del layer Quakes");
                    // quakeVector.setVisible(false);
                    mapOL.removeLayer(quakeVector); //meglio rimuovere a mano i layers se rimane reference non li toglie
                    // console.log("CLEANUP pulizia del layer Raster")
                    // mapOL.removeLayer(rasterLayer);
                }
                ////////////////////////////////////////////////////////////////////////
                /***forza la pulizia dei layer vecchi anche se stesso vecchia versione***/
                ////////////////////////////////////////////////////////////////////////
                mapOL.getLayers().forEach(function (layer) {
                    console.log("CLEANUP DEI LAYERS DAL CLICLO DELLA MAPPA");
                    mapOL.removeLayer(layer);
                });
                console.log("ADDING NEW LAYERS");
                mapOL.addLayer(rasterLayer);
                mapOL.addLayer(localityVector);
            }



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
            mapOL.addOverlay(popup);

            // display popup on click
            mapOL.on('click', function (evt) {
                var feature = mapOL.forEachFeatureAtPixel(evt.pixel,
                    function (feature) {
                        return feature;
                    });

                if (feature) {
                    $(element).popover('destroy')
                    var coordinates = feature.getGeometry().getCoordinates();
                    console.log("FEATURE ONCLICK:")
                    console.log(feature.OnClickTextIT);
                    var popupContent = feature.OnClickTextIT;
                    popup.setPosition(coordinates);
                    $(element).popover({
                        'placement': 'top',
                        'animation': false,
                        'html': true,
                        'trigger': 'manual',
                        'content': popupContent // feature.OnClickTextIT;
                    });
                    $(element).popover('show');
                } else {
                    $(element).popover('destroy');
                    popup.setPosition(undefined);
                }

            });
                // change mouse cursor when over marker
                mapOL.on('pointermove', function (e) {
                    if (e.dragging) {
                       // $(element).popover('hide'); element popover non trovato andava in errore
                        return;
                    }
                    var pixel = mapOL.getEventPixel(e.originalEvent);
                    var hit = mapOL.hasFeatureAtPixel(pixel);
                    mapOL.getTarget().style.cursor = hit ? 'pointer' : '';
                });

                /****solo con zoom maggiore o uguale a 8 faccio vedere i punti***/
                /*
                 Attualmente commentato
                 mapOL.on('moveend', function (event) {
                     console.log(mapOL.getView().getZoom());
                         if (mapOL.getView().getZoom() >= 7) {
                             localityVector.setVisible(true);
                         }
                         else {
                             localityVector.setVisible(false);
                         }
                 });*/
        } catch (e) {
            console.error(e, e.stack);
        }
    });

}