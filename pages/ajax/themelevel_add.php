<?php
include_once "../../include/db.php";
include_once "../../include/authenticate.php"; 
include_once "../../include/general.php"; 
include_once "../../include/collections_functions.php"; 

$max_theme_levels = get_max_theme_levels(); // max number of theme columns currently in table


function getThemeList($parents=array()){
	if (count($parents) == 0){
		// just retrieve all the top level themes
		$sql = "select distinct theme as value from collection where theme is not null and theme <> '' order by theme";
	} else {
		// we were passed an array of parents, so we need to narrow our search
		for ($i = 1; $i < count($parents)+1; $i++){
			if ($i == 1){
				$searchfield = 'theme';
			} else {
				$searchfield = "theme$i";
			}
			
			$whereclause = "$searchfield = '" . mysql_real_escape_string($parents[$i-1]) . "' ";
		}
		$sql = "select distinct theme$i as value from collection where $whereclause and theme$i is not null and theme$i <> '' order by theme$i";
		//echo $sql;
	}	
	$result = sql_array($sql);
	return $result;
}



$themestring = $_REQUEST['themestring'];
if ($themestring <> ''){
	$themearr = explode("||", $themestring);
} else {
	$themearr = array();
}
?>
<!-- Beginning of theme level list -->
<div id="themelevellist" class="themelevellist">
<?php
$i = 0;
$parents = array();

do { 

if (isset($themearr[$i])){
	$thisval = $themearr[$i];
} else {
		$thisval = '';
}
?>
<div class='themelevelinstance' id="themelevel<?php echo $i ?>">
	<div class="Question">
		<label for="theme<?php echo $i ?>">Theme Category </label>
		<select class="stdwidth" name="theme<?php echo $i ?>" id="theme<?php echo $i ?>" onchange="updateThemeLevels(<?php echo $i ?>);"><option value="">Select...</option>
			<?php
				if ($thisval == ''){	
					$printedval = true;
				} else {
					$printedval = false;
				}
				
				foreach (getThemeList($parents) as $theoption){
					if ($theoption == $thisval){
						echo "<option selected>" . htmlspecialchars($theoption) . "</option>";
						$printedval = true;
					} else {
						echo "<option>" . htmlspecialchars($theoption) . "</option>";
					}
				}
				
				if (!$printedval) {
					// we never found the currently selected value, so we'll add it at the bottom.
					echo "<option selected>" . htmlspecialchars($thisval) . "</option>";
				}
			?>
		</select>
		<div class="clearerleft"> </div>
		<label>OR: Enter a new theme category name...</label>
		<input type=text class="medwidth" name="newtheme<?php echo $i ?>" id="newtheme<?php echo $i ?>" value="" maxlength="100">
		<input type=button class="medcomplementwidth" value="Save" style="display:inline;" onclick="updateThemeLevels(<?php echo $i ?>);"/>
		<div class="clearerleft"> </div>
	</div>
</div>

<?php
$parents[] = $thisval;
if ($thisval == '' && $i > 0) {
	break;
}
$i++;
} while ( $i <= count($themearr) );


?>
</div>
<!-- end of themelevellist -->
