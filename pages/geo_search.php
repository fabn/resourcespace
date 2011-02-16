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
	<div id="map_canvas" style="width: 100%; height: 500px; display:block; float:none;" class="Picture" ></div>

    
  <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
  <script>
    map = new OpenLayers.Map("map_canvas");
    map.addLayer(new OpenLayers.Layer.OSM());
 
 

    var control = new OpenLayers.Control();
    OpenLayers.Util.extend(control, {
    draw: function () {
        this.point = new OpenLayers.Handler.Box( control,
            {"done": this.notice});
        this.point.activate();
    },

    notice: function (bounds) {
		
		var bl=map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.left,bounds.bottom)).transform
			(
	           map.getProjectionObject(), // from Spherical Mercator Projection}
	      	   new OpenLayers.Projection("EPSG:4326")
	      	)
	
		var tr=map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.right,bounds.top)).transform
			(
	           map.getProjectionObject(), // from Spherical Mercator Projection}
	      	   new OpenLayers.Projection("EPSG:4326")
	      	);
	      	
	    // Specially encoded search string to avoid keyword splitting
		window.location.href="search.php?search=!geo" + (bl.lat + "b" + bl.lon + "t" + tr.lat + "b" + tr.lon).replace(/\-/gi,'m').replace(/\./gi,'p');
    }
        });map.addControl(control);

	var defaultbounds=new OpenLayers.Bounds(<?php echo $geolocation_default_bounds ?>); // A good world view.
	map.zoomToExtent(defaultbounds);


  </script>
</div>

<?php
include "../include/footer.php";
?>
