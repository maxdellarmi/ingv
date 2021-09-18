$(document).ready(function () {
    var center = [12.6508, 42.5681];
    /*--41.7660824!12.4746484 --SinergieIT S.r.l.
    --41.77023!12.4698199 --Policlinico Universitario Campus Bio-Medico*/
    var markers = [];
    //var markersCoords = <?php echo $markers; ?>;
    markersCoords = [
        {
            name: "SinergieIT S.r.l.'",
            description: "indirizzo di lavoro",
            coordinates: [12.4746484, 41.7660824],
            url: "https://sinergieit.com/"
        },
        {
            name: "Policlinico Universitario Campus Bio-Medico",
            description: "indirizzo campus biomedico",
            coordinates: [12.4698199, 41.77023],
            url: "https://www.policlinicocampusbiomedico.it/"
        },
    ];
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

    var vectorLayer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: markers,
        })
    });

    /****cluster dal vector source ****/
    /*****problema del cluster popup e single feature popup verificare come gestirlo al meglio okkio al popup non e' sempice da gestire
     https://www.kreidefossilien.de/webgis/dokumentation/beispiele/show-popups-on-clustered-features***/
    /*var clusterSource = new ol.source.Cluster({
        distance: 40,
        source:  new ol.source.Vector({
            features: markers,
        })
    });

    var styleCache = {};
    var clusters = new ol.layer.Vector({
        source: clusterSource,
        style: function(feature) {
            var size = feature.get('features').length;
            var style = styleCache[size];
            if (!style) {
                style = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 12,
                        stroke: new ol.style.Stroke({
                            color: '#fff'
                        }),
                        fill: new ol.style.Fill({
                            color: '#3399CC'
                        })
                    }),
                    text: new ol.style.Text({
                        text: size.toString(),
                        fill: new ol.style.Fill({
                            color: '#fff'
                        })
                    })
                });
                styleCache[size] = style;
            }
            return style;
        }
    });*/


    var map = new ol.Map({
        controls: ol.control.defaults({
            attributionOptions: ({
                collapsible: false
            })
        }),
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            vectorLayer //,clusters
        ],
        target: document.getElementById('mapOL'),
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
                'content': '<div class = "tooltip-inner" style="min-width:200px"><h4>' + feature.get('name') + '</h3>' + '<p>' + feature.get('description') + '</p>' + '<a href="' + feature.get('url') + '">Details</a>'

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
    ///TODO: PRENDI UN ESEMPIO KML E CARICARE COME VECTOR LAYER.
});