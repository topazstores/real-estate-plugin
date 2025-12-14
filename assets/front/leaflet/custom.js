jQuery(document).ready(function($) {
    $('.rem-leaflet-map-area').each(function() {
        const $mapContainer = $(this);
        const mapId = $mapContainer.attr('id');
        const mapsData = window['mapsData' + mapId];

        if (!mapsData || typeof L === 'undefined') {
            console.warn(`Missing mapsData or Leaflet not loaded for map: ${mapId}`);
            return;
        }

        const isTouch = 'ontouchstart' in document.documentElement;
        const dragging = !isTouch;

        const mapCenter = L.latLng(mapsData.def_lat, mapsData.def_long);

        const map = L.map(mapId, {
            scrollWheelZoom: false,
            dragging: dragging,
            center: mapCenter,
            zoom: parseInt(mapsData.zoom_level, 10),
            layers: [
                L.tileLayer(mapsData.leaflet_styles.provider, {
                    maxZoom: 21,
                    attribution: mapsData.leaflet_styles.attribution
                })
            ]
        });

        const markers = L.markerClusterGroup();
        const bounds = L.latLngBounds(); // Track marker bounds

        const createMarker = function(lat, lon, property) {
            const propertyIcon = L.icon({
                iconUrl: property.icon_url,
                iconSize: mapsData.icons_size,
                iconAnchor: mapsData.icons_anchor
            });

            const marker = L.marker([lat, lon], {
                icon: propertyIcon,
                title: rem_html_special_chars_decode(property.title)
            });

            if (property.property_box) {
                marker.bindPopup(property.property_box, { maxWidth: 320 });
            }

            markers.addLayer(marker);
            bounds.extend([lat, lon]); // Extend bounds
        };

        let pendingGeocodeRequests = 0;

        mapsData.properties.forEach(property => {
            if (property.lat && property.lon) {
                createMarker(property.lat, property.lon, property);
            } else if (property.address) {
                pendingGeocodeRequests++;
                $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(property.address)}`)
                    .done(data => {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            createMarker(lat, lon, property);
                        } else {
                            console.warn(`No results found for address: ${property.address}`);
                        }
                    })
                    .fail(() => {
                        console.error(`Failed to fetch location for: ${property.address}`);
                    })
                    .always(() => {
                        pendingGeocodeRequests--;
                    });
            } else {
                console.warn(`No coordinates or address provided for property: ${property.title}`);
            }
        });

        map.addLayer(markers);

        const fitMapToBounds = () => {
            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 16
                });
            }
        };

        // Retry fitting bounds until all geocode requests are complete
        const checkBoundsReady = () => {
            if (pendingGeocodeRequests === 0) {
                fitMapToBounds();
            } else {
                setTimeout(checkBoundsReady, 300);
            }
        };

        checkBoundsReady();

        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });

    // Resize map when UI is toggled
    $('.toggle a').on('click', function () {
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 100);
    });
});

// HTML special characters decode helper
function rem_html_special_chars_decode(text) {
    const map = {
        '&amp;': '&',
        '&#038;': "&",
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'",
        '&#8217;': "’",
        '&#8216;': "‘",
        '&#8211;': "–",
        '&#8212;': "—",
        '&#8230;': "…",
        '&#8221;': '”'
    };

    return text.replace(/\&[\w\d\#]{2,6}\;/g, m => map[m] || m);
}