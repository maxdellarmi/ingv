// $(document).ready(function () {
var markers = [];

var localityPHPmarkers = [];

var quakesPQMarkers = [];

//var EEmarkers = [];

var mapOL;

var quakeVector;

var rasterLayer;

var localityVector;

var EEVector;

var templateStr = 'Lat:{y}, Lon:{x}';

var mousePositionControl;

function template(string, obj){
    var s = string;
    for(var prop in obj) {
        if (obj[prop] === undefined) {  //se la properties e' vuota toglie il tag
            s = s.replace(new RegExp('{'+ prop +'}','g'), '');
        } else {
            s = s.replace(new RegExp('{'+ prop +'}','g'), obj[prop]);
        }
    }
    return s;
}


function calculateMousePosition() {
   return  new ol.control.MousePosition({
        coordinateFormat: function(coord) {
            //console.log(coord);
            //console.log(ol.coordinate.format(coord, templateStr, 3).toString());
            return ol.coordinate.format(coord, templateStr, 3);},
        projection: 'EPSG:4326',
        className: 'custom-mouse-position',
        target: document.getElementById('tdCursor'),
        undefinedHTML: '&nbsp;',
    });
}

function creazioneMappa () {
    $(document).ready(function() {
        try {
            //setting mousePositionControl
            /* mousePositionControl = new ol.control.MousePosition({
                coordinateFormat: function(coord) {
                    //console.log(coord);
                    //console.log(ol.coordinate.format(coord, templateStr, 3).toString());
                    return ol.coordinate.format(coord, templateStr, 3);},
                projection: 'EPSG:4326',
                className: 'custom-mouse-position',
                target: document.getElementById('tdCursor'),
                undefinedHTML: '&nbsp;',
            });*/
            var center = [12.6508, 42.5681];


            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });

            console.log('caricati i terremoti test')

            for (var i = 0; i < markersArray.length; i++) { //inizializzato nel js index.js
                markers.push(markersArray[i]['Marker']);    //markers inizializzato in questo file.
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
                   /*controls: ol.control.defaults({
                       attributionOptions: ({
                           collapsible: false
                       })
                   }),*/
                   //settings maps control
                   controls: ol.control.defaults({
                       attributionOptions: ({
                           collapsible: false})}).extend([calculateMousePosition()]).extend([new ol.control.FullScreen()]),
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
               console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
               console.log("CLEANUP pulizia del layer quakes");
               // quakeVector.setVisible(false);
               (quakeVector!== undefined)? mapOL.removeLayer(quakeVector): null; //meglio rimuovere a mano i layers se rimane reference non li toglie
               console.log("CLEANUP pulizia del layer Localita");
               (localityVector!== undefined)? mapOL.removeLayer(localityVector): null;// console.log("CLEANUP pulizia del layer Raster")
               console.log("CLEANUP pulizia del layer Eventi ambientali");
               (EEVector!== undefined)? mapOL.removeLayer(EEVector): null;    // console.log("CLEANUP pulizia del layer Raster")
                   // mapOL.removeLayer(rasterLayer);
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
                    console.log("FEATURE ONCLICK popup data:")
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

            // change mouse cursor when over marker
            console.log('popover su mapOL.js gestione terremoti commentato perche andava in errore ');

            resizeMapIndex();
        } catch (e) {
            console.error(e, e.stack);
        }
    });

}


/**
 * Terremoti quakes di dettaglio caricati: localityPHPmarkers.push(epiMarkers[i]);
 * Pinpoint della location: localityPHPmarkers.push(markerLOC);
 * vengono quindi inseriti tutti quanti su un unico array e visualizzati su mappa.
 * Alla fine la mappa zomma e si posiziona in automatico nel range delle coordinate recuperando l'extent dal source del vectorLayer
 *
 * all'interno delle feature ci sono oggetti con tipo
 * type: "pinpoint", oppure type: "quakes" per rendere piu facile la ricerca successiva
 *
 * mapOL.getView().fit(  quakeVector.getSource().getExtent(), mapOL.getSize());
 * mapOL.getView().setZoom(9);
 *
 * @param quakes
 */
function creazioneMappaLocalityPHP (quakes) {
    $(document).ready(function() {
        try {
            // do some crazy stuff
            var center = [12.6508, 42.5681];

            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });
            console.log('caricamento dei terremoti in input quakes:');
            console.log(quakes);

            quakeVector = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: quakes,
                    projection: 'EPSG:4326'
                })
            });
            quakeVector.setVisible(true);

            if (mapOL === undefined) {
                mapOL = new ol.Map({
                    // controls: ol.control.defaults({
                    //     attributionOptions: ({
                    //         collapsible: false
                    //     })
                    // }),
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false})}).extend([calculateMousePosition()]).extend([new ol.control.FullScreen()]),
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
                console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                console.log("CLEANUP pulizia del layer Quakes");
                // quakeVector.setVisible(false);
                (quakeVector!== undefined)? mapOL.removeLayer(quakeVector): null; //meglio rimuovere a mano i layers se rimane reference non li toglie
                console.log("CLEANUP pulizia del layer Localita");
                (localityVector!== undefined)? mapOL.removeLayer(localityVector): null;// console.log("CLEANUP pulizia del layer Raster")
                console.log("CLEANUP pulizia del layer Eventi ambientali");
                (EEVector!== undefined)? mapOL.removeLayer(EEVector): null;    // console.log("CLEANUP pulizia del layer Raster")
                // mapOL.removeLayer(rasterLayer);
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
                    console.log("FEATURE ONCLICK popup data:")
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

            ///TODO AUTOPOSIZIONAMENTO AL CARICAMENTO DEL LAYER
            // mapOL.getView().fit(  quakeVector.getSource().getExtent(), mapOL.getSize()); //versione senza padding
            var padding = [500, 50, 500, 50]
            mapOL.getView().fit(
                quakeVector.getSource().getExtent(),
                {
                    size: mapOL.getSize(),
                    padding: padding,
                }
            );
        } catch (e) {
            console.error(e, e.stack);
        }
    });
}

function creazioneMappaQuakesPHP (quakes) {
    console.log("creazioneMappaQuakesPHP - quakesPQMarkers");
    console.log(quakes);
    $(document).ready(function() {
        try {
            // do some crazy stuff
            var center = [12.6508, 42.5681];

            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });
            console.log('caricamento dei terremoti in input quakes:');
            console.log(quakes);

            quakeVector = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: quakes,
                    projection: 'EPSG:4326'
                })
            });
            quakeVector.setVisible(true);

            if (mapOL === undefined) {
                mapOL = new ol.Map({
                    // controls: ol.control.defaults({
                    //     attributionOptions: ({
                    //         collapsible: false
                    //     })
                    // }),
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false})}).extend([calculateMousePosition()]).extend([new ol.control.FullScreen()]),
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
                console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                console.log("CLEANUP pulizia del layer Quakes");
                // quakeVector.setVisible(false);
                (quakeVector!== undefined)? mapOL.removeLayer(quakeVector): null; //meglio rimuovere a mano i layers se rimane reference non li toglie
                console.log("CLEANUP pulizia del layer Localita");
                (localityVector!== undefined)? mapOL.removeLayer(localityVector): null;// console.log("CLEANUP pulizia del layer Raster")
                console.log("CLEANUP pulizia del layer Eventi ambientali");
                (EEVector!== undefined)? mapOL.removeLayer(EEVector): null;    // console.log("CLEANUP pulizia del layer Raster")
                // mapOL.removeLayer(rasterLayer);
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
                    console.log("FEATURE ONCLICK popup data:")
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

            ///TODO AUTOPOSIZIONAMENTO AL CARICAMENTO DEL LAYER
            // mapOL.getView().fit(  quakeVector.getSource().getExtent(), mapOL.getSize()); //versione senza padding
            var padding = [500, 50, 500, 50]
            mapOL.getView().fit(
                quakeVector.getSource().getExtent(),
                {
                    size: mapOL.getSize(),
                    padding: padding,
                }
            );
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
            console.log("LOC Markers" + LOCMarkers.length); //inizializzato nel js index_loc.js
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
                    // controls: ol.control.defaults({
                    //     attributionOptions: ({
                    //         collapsible: false
                    //     })
                    // }),
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false})}).extend([calculateMousePosition()]).extend([new ol.control.FullScreen()]),
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
                console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                console.log("CLEANUP pulizia del layer Quakes");
                    // quakeVector.setVisible(false);
                (quakeVector!== undefined)? mapOL.removeLayer(quakeVector): null; //meglio rimuovere a mano i layers se rimane reference non li toglie
                console.log("CLEANUP pulizia del layer Eventi ambientali");
                (EEVector!== undefined)? mapOL.removeLayer(EEVector): null;    // console.log("CLEANUP pulizia del layer Raster")
                    // mapOL.removeLayer(rasterLayer);
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
                    console.log("FEATURE ONCLICK popup data:")
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

function indexEEAmbiente() {
    $(document).ready(function() {
        try {
            var center =[12.6508, 42.5681];
            console.log(center);

            console.log("carico i dati");
            console.log("EE ambiente:" + EEmarkersArray.length);

            for (var i = 0; i < EEmarkersArray.length; i++) { //inizializzato nel js index_EE.js
                EEmarkers.push(EEmarkersArray[i]['Marker']);  //inizializzato nel js index_EE.js
            }
            console.log("carico i markers EE ambiente");
            console.log(EEmarkers);

            EEVector = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: EEmarkers,
                    projection: 'EPSG:4326'
                })
            });
            EEVector.setVisible(true);

            console.log("EEVector");
            console.log(EEVector);

            rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });


            if (mapOL === undefined) {
                mapOL = new ol.Map({
                    // controls: ol.control.defaults({
                    //     attributionOptions: ({
                    //         collapsible: false
                    //     })
                    // }),
                    controls: ol.control.defaults({
                        attributionOptions: ({
                            collapsible: false})}).extend([calculateMousePosition()]).extend([new ol.control.FullScreen()]),
                    layers: [rasterLayer, EEVector],
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
                // if (quakeVector!== undefined) {
                console.log("CLEANUP inizio pulizia di tutti gli altri layer presenti (tranne raster)");
                console.log("CLEANUP pulizia del layer Quakes");
                // quakeVector.setVisible(false);
                (quakeVector!== undefined)? mapOL.removeLayer(quakeVector): null; //meglio rimuovere a mano i layers se rimane reference non li toglie
                console.log("CLEANUP pulizia del layer Locality");
                (localityVector!== undefined)? mapOL.removeLayer(localityVector): null;
                // console.log("CLEANUP pulizia del layer Raster")
                // mapOL.removeLayer(rasterLayer);
                // }
                ////////////////////////////////////////////////////////////////////////
                /***forza la pulizia dei layer vecchi anche se stesso vecchia versione***/
                ////////////////////////////////////////////////////////////////////////
                mapOL.getLayers().forEach(function (layer) {
                    console.log("CLEANUP DEI LAYERS DAL CLICLO DELLA MAPPA");
                    mapOL.removeLayer(layer);
                });
                console.log("ADDING NEW LAYERS");
                mapOL.addLayer(rasterLayer);
                mapOL.addLayer(EEVector);
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
                    console.log("FEATURE ONCLICK popup data:")
                    console.log(feature.ContentPopupText);
                    var popupContent = feature.ContentPopupText;
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

        } catch (e) {
            console.error(e, e.stack);
        }
    });


}