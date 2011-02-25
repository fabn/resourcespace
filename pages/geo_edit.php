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

<?php include "../include/geo_map.php"; ?>
<script>
 	
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
				    
		<?php if (isset($_COOKIE["geobound"]))
			{
			$bounds=$_COOKIE["geobound"];
			}
		else
			{
			$bounds=$geolocation_default_bounds;
			}
		$bounds=explode(",",$bounds);
		?>
		map.setCenter(new OpenLayers.LonLat(<?php echo $bounds[0] ?>,<?php echo $bounds[1] ?>),<?php echo $bounds[2] ?>);

    <?php } ?>


  </script>

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
