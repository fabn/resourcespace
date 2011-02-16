<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/header.php";

if ($disable_geocoding){exit("Geomapping disabled.");}

# Fetch resource data.
$ref = getvalescaped('ref','');
if ($ref=='') {die;}
$resource=get_resource_data($ref);
if ($resource==false) {die;}

# Not allowed to edit this resource?
if (!get_edit_access($ref,$resource["archive"])) {exit ("Permission denied.");}

?>
<?php

if (isset($_POST['submit']))
	{
    $s=explode(",",getvalescaped('geo-loc',''));
    if (count($s)==2)
    	{
    	sql_query("update resource set geo_lat='" . escape_check($s[0]) . "',geo_long='" . escape_check($s[1]) . "' where ref='$ref'");
    	
    	#Reload resource data
		$resource=get_resource_data($ref,false);
    	}
	}


 ?>

<div class="RecordBox">
    <div class="RecordPanel">
    <div class="Title"><?php echo $lang['location-title']; ?></div>
	<p>&gt;&nbsp;<a href="view.php?ref=<?php echo $ref?>"><?php echo $lang['backtoview']; ?></a></p>
	<div id="map_canvas" style="width: *; height: 500px; display:block; float:none;" class="Picture" ></div>

    
    		<?php if (isset($gmaps_apikey)) { 
    		# ----------------------------- Google Maps version -----------------------------
    		?>
			<script src="../lib/js/jquery-1.3.1.min.js" type="text/javascript"></script>
			<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $gmaps_apikey; ?>&sensor=false"
			        type="text/javascript">
			</script>

            <script type="text/javascript">
                function geo_loc_initialize() {
                  if (GBrowserIsCompatible()) {
                    var mapOptions = {
                    		   googleBarOptions : {
                                    style: 'new',
                                    }
                    }
                    map = new GMap2(document.getElementById("map_canvas"),mapOptions);
                    <?php if ($resource["geo_long"]!=="") {?>
                    map.setCenter(new GLatLng(<?php echo $resource["geo_long"];?>, <?php echo $resource["geo_long"]; ?>), 8);
                    geo_mark = new GMarker(map.getCenter(), {draggable: true})
                    map.addOverlay(geo_mark);
                    <?php } else { ?>
                    geo_mark = false;
                    map.setCenter(new GLatLng(0,0),1);
                    <?php } ?>
                    map.setUIToDefault();
                    map.enableGoogleBar();
                    GEvent.addListener(map, "dblclick", function(overlay, latlng) {
                        if (geo_mark==false){
                            geo_mark = new GMarker(latlng, {draggable: true});
                            map.addOverlay(geo_mark);
                        } else {
                            geo_mark.setLatLng(latlng);
                            $("input#map-input").attr('value', geo_mark.getLatLng().toUrlValue());
                        }
                            return false;
                            
                    });
                   GEvent.addListener(geo_mark, "dragend", function() {
                            $("input#map-input").attr('value', geo_mark.getLatLng().toUrlValue());
                            return false;
                            
                    });
                  }
                }
                $(document).ready(function() {
                    geo_loc_initialize();
                    $("form#map-form").submit(function(e){
                        if (geo_mark==false){
                            alert('<?php echo $lang['location-noneselected'] ?>');
                            e.preventDefault();
                            return false;
                        }
                        else {
                            return true;
                        }
                    });
                });
                $(document).unload(GUnload);
            </script>
            <?php } else { 
            # ---------------- OpenStreetMap version -----------------
            ?>
			  <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
			  <script src="http://maps.google.com/maps/api/js?sensor=false"></script> 
			  <script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1"></script> 
			  <script>
			    map = new OpenLayers.Map("map_canvas");
			    
				var osm = new OpenLayers.Layer.OSM();
		        var gmap = new OpenLayers.Layer.Google("Google Streets", {visibility: false});
			    map.addLayers([osm, gmap]);
			 	
                <?php if ($resource["geo_long"]!=="") {?>
			    var lonLat = new OpenLayers.LonLat( <?php echo $resource["geo_long"] ?> , <?php echo $resource["geo_lat"] ?> )
			          .transform(
			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			            map.getProjectionObject() // to Spherical Mercator Projection
			          );
				<?php } else { ?>			 
				var lonLat = new OpenLayers.LonLat(0,0);
				<?php } ?>
				var zoom=13;
			 
			    var markers = new OpenLayers.Layer.Markers( "Markers" );
			    map.addLayer(markers);
			 
			 	var marker = new OpenLayers.Marker(lonLat);
			    markers.addMarker(marker);

				//dragfeature = new OpenLayers.Control.DragFeature(markers,{'onComplete': onCompleteMove});
				//map.addControl(dragfeature);
				//dragfeature.activate();

	            var control = new OpenLayers.Control();
	            OpenLayers.Util.extend(control, {
                draw: function () {
                    this.point = new OpenLayers.Handler.Point( control,
                        {"done": this.notice});
                    this.point.activate();
                },
 
                notice: function (bounds) {
                    marker.lonlat.lon=(bounds.x);
                    marker.lonlat.lat=(bounds.y);
                    
                    //marker.lonlat=new OpenLayers.LonLat(bounds.x,bounds.y);
				    markers.addMarker(marker);
				    
				    // Update control
				    var translonlat=new OpenLayers.LonLat(bounds.x,bounds.y).transform
				    	(
			            map.getProjectionObject(), // from Spherical Mercator Projection}
				    	new OpenLayers.Projection("EPSG:4326") // to WGS 1984
			            );
				    
				    document.getElementById('map-input').value=translonlat.lat + ',' + translonlat.lon;
				    
                }
   		        });map.addControl(control);

                <?php if ($resource["geo_long"]!=="") {?>			 
			    map.setCenter (lonLat, zoom);
			    <?php } else { ?>
				var defaultbounds=new OpenLayers.Bounds(<?php echo $geolocation_default_bounds ?>); // A good world view.
				map.zoomToExtent(defaultbounds);

			    <?php } ?>

		        map.addControl(new OpenLayers.Control.LayerSwitcher());
			  </script>
			 <?php } ?>
            <p><?php echo $lang['location-details']; ?></p>
            <form id="map-form" method="post">
            <input name="ref" type="hidden" value="<?php echo $ref; ?>" />
            <?php echo $lang['latlong']; ?>: <input name="geo-loc" type="text" size="50" value="<?php echo $resource["geo_long"]==""?"":($resource["geo_lat"] . "," . $resource["geo_long"]) ?>" id="map-input" />
            <input name="submit" type="submit" value="<?php echo $lang['save']; ?>" />
            </form>
    </div>
    <div class="PanelShadow"></div>
    </div>
<?php
include "../include/footer.php";
?>
