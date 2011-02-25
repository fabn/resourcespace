

<div id="map_canvas" style="width: *; height: <?php echo isset($mapheight)?$mapheight:"500" ?>px; display:block; float:none;" class="Picture" ></div>

<script src="../lib/OpenLayers/OpenLayers.js"></script>
<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>
<script>
map = new OpenLayers.Map("map_canvas");

var osm = new OpenLayers.Layer.OSM();
var gphy = new OpenLayers.Layer.Google(
"Google Physical",
{type: google.maps.MapTypeId.TERRAIN}
// used to be {type: G_PHYSICAL_MAP}
);
var gmap = new OpenLayers.Layer.Google(
"Google Streets", // the default
{numZoomLevels: 20}
// default type, no change needed here
);
var gsat = new OpenLayers.Layer.Google(
"Google Satellite",
{type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
// used to be {type: G_SATELLITE_MAP, numZoomLevels: 22}
);


map.addLayers([<?php echo $geo_layers ?>]);
map.addControl(new OpenLayers.Control.LayerSwitcher());
    
</script>