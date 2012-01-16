<?php

include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid $
include "../include/research_functions.php";
include "../include/authenticate.php";
include "../include/search_functions.php";

$user=get_user($userref);$usercollection=$user['current_collection'];

if (!(isset($_REQUEST['collection'])&&is_numeric($_REQUEST['collection']))){
	exit ("Error: missing or invalid collection identifier");
} else {
	$collection = $_REQUEST['collection'];
}


# Fetch collection data
$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found.");}
if (($userref!=$cinfo["user"]) && ($cinfo["allow_changes"]!=1) && (!checkperm("h"))) {exit("Access denied.");}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'doreorder'){
	// code here to save reordered resources
	$items = $_POST['resourcelist'];
	$i = 1; # will represent the actual order (starting at 1).
	foreach($items as $itemid) {
		if (is_numeric($itemid)){
			$query = "UPDATE collection_resource SET sortorder = $i WHERE resource = $itemid and collection = '$collection'";
			sql_query($query);
			$i++;  # add 1 to the current value of i (the next order in the list).
		}
	}


} else {


include "../include/header.php";

?>


<!--
Based on example by Thomas Fuchs at 
http://www.java2s.com/Code/JavaScriptDemo/Sortabledlistwithhandlers.htm
released under following license:
Copyright (c) 2005-2008 Thomas Fuchs (http://script.aculo.us, http://mir.aculo.us)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->

<!-- revised from scriptaculous-js-1.8.2 demo code  -->

  <script src="../lib/js/prototype.js" type="text/javascript"></script>
  <script src="../lib/js/scriptaculous.js" type="text/javascript"></script>

<h1><?php echo $lang['sortcollection']; ?></h1>

<!-- FIXME: Add instructions to site content if desired. -->
<?php echo text("introtext")?>


<?php

$sql = "select collection, resource, date_added, comment, collection_resource.rating, sortorder, preview_extension, has_image, resource_type, file_extension from collection_resource left join resource on
	collection_resource.resource = resource.ref
	where collection_resource.collection = '$collection'
	order by sortorder asc, date_added desc";


$results=sql_query($sql);

echo "<ul id='resourcelist' style='display:inline;'>\n";
for ($i=0; $i<count($results); $i++){
  if ($results[$i]["has_image"]==1) { 
	  $thumburl = get_resource_path($results[$i]['resource'],false,'col',false,$results[$i]['preview_extension']);
	}
	else {
	  $thumburl = '../gfx/'. get_nopreview_icon($results[$i]["resource_type"],$results[$i]["file_extension"],true);
	}
	echo "<li id=\"resourcelist_" . $results[$i]['resource'] . "\" class='ResourcePanelInfo' style='float:left;width:100px;height:100px;list-style-type:none;border-width:1px;border-style:solid;margin:5px 5px 5px 5px;padding:5px 5px 5px 5px;text-align:center;'>";
	echo "<img src='$thumburl' /><br />";
	echo $results[$i]['resource'];
	echo "</li>\n";

}

echo "</ul>";


?>


 <script type="text/javascript">
 
   Sortable.create("resourcelist",
     {dropOnEmpty:true,containment:["resourcelist"],constraint:false,
     onUpdate:saveOrder});

function saveOrder() {
   //$('resourcelist_debug').innerHTML = Sortable.serialize('resourcelist') 
   listorder = Sortable.serialize('resourcelist');
   var url = '<?php echo $_SERVER['PHP_SELF']; ?>';
    var pars = listorder
        + '&rnd='
        + new Date().getTime()
	+ '&collection=<?php echo $collection; ?>'
	+ '&action=doreorder';
    var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            postBody: pars,
	    onComplete:  afterResort
        });
}

function afterResort(){
	// fixme  - need to make sure this works with frameless collections as well
	curcol = top.collections.document.getElementById('colselect').collection.value;
	<?php  if (!$frameless_collections){	?>
		if (curcol == '<?php echo $collection; ?>'){
			top.collections.location = "collections.php?collection=" + curcol;
		}
	<?php } // end if not frameless collections ?>
}



 
 </script>


<?php

include "../include/footer.php";

} // end of if action is not reorder

?>
