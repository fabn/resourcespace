<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/header.php";
# Fetch resource data.
$ref = getvalescaped('ref','');
if ($ref=='') {die;}
$resource=get_resource_data($ref);
if ($resource==false) {die;}

# Not allowed to edit this resource?
if (!get_edit_access($ref,$resource["archive"])) {exit ("Permission denied.");}

?>
<?php
$gps_field = sql_value('SELECT ref as value from resource_type_field '. 
                       'where name="geolocation" AND (resource_type="'.$resource['resource_type'].'" OR resource_type="0")','');

if (isset($_POST['submit'])){
    update_field($ref,$gps_field,getvalescaped('geo-loc',''));
}

if ($disable_geocoding || $gps_field==''){exit("Geomapping disabled.");}
$ll_field = get_data_by_field($ref, $gps_field);
        if ($ll_field!=''){
            $lat_long = explode(',', $ll_field);
        } else {
            $lat_long = false;
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
                    <?php if ($lat_long!==false) {?>
                    map.setCenter(new GLatLng(<?php echo $lat_long[0];?>, <?php echo $lat_long[1]; ?>), 8);
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
			  <script>
			    map = new OpenLayers.Map("map_canvas");
			    map.addLayer(new OpenLayers.Layer.OSM());
			 
                <?php if ($lat_long!==false) {?>
			    var lonLat = new OpenLayers.LonLat( <?php echo $lat_long[1] ?> , <?php echo $lat_long[0] ?> )
			          .transform(
			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			            map.getProjectionObject() // to Spherical Mercator Projection
			          );
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

                <?php if ($lat_long!==false) {?>			 
			    map.setCenter (lonLat, zoom);
			    <?php } else { ?>
			    map.zoomToMaxExtent();
			    <?php } ?>
				
				function onCompleteMove(feature)
					{
					alert('11');
					}			    
			    
			  </script>
			 <?php } ?>
            <p><?php echo $lang['location-details']; ?></p>
            <form id="map-form" method="post">
            <input name="ref" type="hidden" value="<?php echo $ref; ?>" />
            <?php echo $lang['latlong']; ?>: <input name="geo-loc" type="text" size="50" value="<?php echo $ll_field ?>" id="map-input" />
            <input name="submit" type="submit" value="<?php echo $lang['save']; ?>" />
            </form>
    </div>
    <div class="PanelShadow"></div>
    </div>
<?php
include "../include/footer.php";
?>
