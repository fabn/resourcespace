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
 
if ($disable_geocoding || !isset($gmaps_apikey) || $gps_field==''){die;}
$ll_field = get_data_by_field($ref, $gps_field);
        if ($ll_field!=''){
            $lat_long = explode(',', get_data_by_field($ref,$gps_field));
        } else {
            $lat_long = false;
        }    
 ?>
<script src="../lib/js/jquery-1.3.1.min.js" type="text/javascript"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $gmaps_apikey; ?>&sensor=false"
        type="text/javascript">
</script>

<div class="RecordBox">
    <div class="RecordPanel">
    <div class="Title"><?php echo $lang['location-title']; ?></div>
            <script type="text/javascript">
                function geo_loc_initialize() {
                  if (GBrowserIsCompatible()) {
                    var map = new GMap2(document.getElementById("map_canvas"));
                    <?php if ($lat_long!==false) {?>
                    map.setCenter(new GLatLng(<?php echo $lat_long[0];?>, <?php echo $lat_long[1]; ?>), 8);
                    geo_mark = new GMarker(map.getCenter(), {draggable: true})
                    map.addOverlay(geo_mark);
                    <?php } else { ?>
                    geo_mark = false;
                    map.setCenter(new GLatLng(0,0),1);
                    <?php } ?>
                    map.setUIToDefault();
                    GEvent.addListener(map, "dblclick", function(overlay, latlng) {
                        if (geo_mark==false){
                            geo_mark = new GMarker(latlng, {draggable: true});
                            map.addOverlay(geo_mark);
                        } else {
                            geo_mark.setLatLng(latlng);
                        }
                            return false;
                            
                    });
                    
                  }
                }
                $(document).ready(function() {
                    geo_loc_initialize();
                    $("form#map-form").submit(function(e){
                        if (geo_mark==false){
                            alert("No Location Selected");
                            e.preventDefault();
                            return false;
                        }
                        else {
                            $("input#map-input").attr('value', geo_mark.getLatLng().toUrlValue());
                            return true;
                        }
                    });
                });
                $(document).unload(GUnload);
            </script>
            <ul class="HorizontalNav"><li><a href="view.php?ref=<?php echo $ref?>"><?php echo $lang['backtoview']; ?></a></li></ul>
            <div id="map_canvas" style="width: *; height: 300px; display:block; float:none;" class="Picture" ></div>
            <p><?php echo $lang['location-details']; ?></p>
            <form id="map-form" method="post">
            <input name="ref" type="hidden" value="<?php echo $ref; ?>" />
            <input name="geo-loc" type="hidden" value="" id="map-input" />
            <input name="submit" type="submit" value="<?php echo $lang['save']; ?>" />
            </form>
    </div>
    <div class="PanelShadow"></div>
    </div>
<?php
include "../include/footer.php";
?>
