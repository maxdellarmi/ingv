// $(document).ready(function () {
var markers = [];

var mapOL;

var layerVettore;

function creazioneMappa () {
    $(document).ready(function() {
        try {
            // do some crazy stuff
            var center = [12.6508, 42.5681];


            // const workingIconFeature = new ol.Feature({
            //     geometry: new ol.geom.Point([12.6508, 42.5681])
            // });
            //
            // workingIconFeature.setStyle(new ol.style.Style({
            //     image: new ol.style.Icon(({
            //         color: '#fffb22',
            //         src: '/img/dot.png',
            //         // the real size of your icon
            //         size: [20, 20],
            //         // the scale factor
            //         scale: 0.5
            //     }))
            // }));
            //
            // const vectorSource = new ol.source.Vector({
            //     features: [workingIconFeature] //,notWorkingIconFeature]
            // });
            //
            // const vectorLayer1 = new ol.layer.Vector({
            //     source: vectorSource
            // });

            const rasterLayer = new ol.layer.Tile({
                source: new ol.source.OSM(),
                projection: 'EPSG:4326'
            });


            /*mapOL = new ol.Map({
                layers: [rasterLayer, vectorLayer1],
                target: document.getElementById('mapOL'),
                view: new ol.View({
                    center: ol.proj.fromLonLat(center),
                    zoom: 12
                })
            });*/


            console.log('caricati i terremoti test')

            for (var i = 0; i < markersArray.length; i++) {
                markers.push(markersArray[i]['Marker']);
            }
            console.log("carico i markers");
            console.log(markers);

            layerVettore = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: markers,
                    projection: 'EPSG:4326'
                })
            });

            layerVettore.setVisible(true);


           mapOL = new ol.Map({
            controls: ol.control.defaults({
                attributionOptions: ({
                    collapsible: false
                })
            }),
            //layers: [rasterLayer,layerVettore, vectorLayer1],
            layers: [rasterLayer,layerVettore],
            target: document.getElementById('mapOL'),
            view: new ol.View({
                projection: 'EPSG:4326',
                center: center,
                zoom: 6,
            })
        });

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
            /*map.on('click', function (evt) {
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
            console.log('popover su mapOL.js commentato perche andava in errore ');

            resizeMapIndex();
        } catch (e) {
            console.error(e, e.stack);
        }
    });

}