<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/header.php";

?>
<div class="BasicsBox"> 
<h1><?php echo $lang["geographicsearch"] ?></h1>
<p><?php echo $lang["geographicsearch_help"] ?></p>


<!-- Drag mode selector -->
<div>
<?php echo $lang["geodragmode"] ?>:&nbsp;
<input type="radio" name="dragmode" id="dragmodearea" checked="true" onClick="control.point.activate()" /><label for="dragmodearea"><?php echo $lang["geodragmodearea"] ?></label>
&nbsp;&nbsp;
<input type="radio" name="dragmode" id="dragmodepan" onClick="control.point.deactivate();" /><label for="dragmodepan"><?php echo $lang["geodragmodepan"] ?></label>
</div>

<?php include "../include/geo_map.php"; ?>
<script>

var control = new OpenLayers.Control();
OpenLayers.Util.extend(control, {
draw: function () {
    this.point = new OpenLayers.Handler.Box( control,
        {"done": this.notice});
    this.point.activate();
},

notice: function (bounds) {
	
	var blpix = new OpenLayers.Pixel(bounds.left,bounds.bottom);
	var bl=map.getLonLatFromPixel(blpix).transform
		(
           map.getProjectionObject(), // from Spherical Mercator Projection}
      	   new OpenLayers.Projection("EPSG:4326")
      	)

	var trpix = new OpenLayers.Pixel(bounds.right,bounds.top);
	var tr=map.getLonLatFromPixel(trpix).transform
		(
           map.getProjectionObject(), // from Spherical Mercator Projection}
      	   new OpenLayers.Projection("EPSG:4326")
      	);
      
    // Store the map window position to make it easier when returning for another search
    SetCookie("geobound",map.getCenter().lon + "," + map.getCenter().lat + "," + map.getZoom()); 
    
    // Specially encoded search string to avoid keyword splitting
	window.location.href="search.php?search=!geo" + (bl.lat + "b" + bl.lon + "t" + tr.lat + "b" + tr.lon).replace(/\-/gi,'m').replace(/\./gi,'p');
}
    });map.addControl(control);

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


	
</script>
</div>

<?php
include "../include/footer.php";
?>
