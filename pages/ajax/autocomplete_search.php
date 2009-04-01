<?php
# Feeder page for AJAX search auto-completion.

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$search=getvalescaped("search","");

# Find last keyword user is searching for
$s=explode(" ",$search);
$last=$s[count($s)-1];

# Merge the words back together so existing words can be added to the results.
array_pop($s);$otherwords=join(" ",$s);

?>
<ul>
<?php
if (strlen($last)>=2) # Activate when last entered keyword >=3 chars long
	{
	?>
	<?php
	$keywords=get_suggested_keywords($last);
	for ($n=0;$n<count($keywords);$n++)
		{
		?>
		<li><?php echo (($otherwords!="")?$otherwords . " ":"") . $keywords[$n]?></li>
		<?php
		}
	?>
	<?php
	}
?>
</ul>
