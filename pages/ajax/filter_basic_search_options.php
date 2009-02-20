<?php

# Filters the dropdown options on the basic search page.
# The output from this page is Javascript.

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$filter=getvalescaped("filter","");

# Build up a SQL query based on the current filtering options
$s=explode(";",$filter);
$sql="";
for ($n=0;$n<count($s);$n++)
	{
	$e=explode(":",$s[$n]);
	if (count($e)==2)
		{
		# Fetch field details
		$field[$n]=sql_query("select * from resource_type_field where name='" . escape_check($e[0]) . "'");
		if (count($field[$n])==0) {exit();}
		$field[$n]=$field[$n][0];
		
		if ($e[1]!="")
			{
			$k=resolve_keyword(cleanse_string($e[1],true));
			if ($k===false) {$k=-1;}
			
			# Filter using this value
			$sql.=" join resource_keyword rk" . $n . " on rk" . $n . ".resource=r.ref and rk" . $n . ".keyword='" . $k . "' and rk" . $n . ".resource_type_field='" . $field[$n]["ref"] . "'";
			}
		}
	}

# For each field, fetch all values and limit those that do not exist
for ($n=0;$n<count($s);$n++)
	{
	$e=explode(":",$s[$n]);
	if (count($e)==2)
		{
		$values=sql_array("select k.keyword value from resource r join resource_keyword rk on rk.resource=r.ref  and rk.resource_type_field='" . $field[$n]["ref"] . "' join keyword k on rk.keyword=k.ref " . $sql);
		#print_r($values);
		
		# Fetch the full list of available options
		$options=trim_array(explode(",",$field[$n]["options"]));
		sort($options);
		#print_r($options);

		# Remove existing options for this field
		#echo "<h2>" . $field[$n]["title"] . "</h2><ul>";
		$select="<option value=''>&nbsp;</option>";
		for ($m=0;$m<count($options);$m++)
			{
			if (in_array(cleanse_string(i18n_get_translated($options[$m]),true),$values))
				{
				#echo "<li>" . i18n_get_translated($options[$m]);
				$select.="<option";
				if ($e[1]==i18n_get_translated($options[$m])) {$select.=" selected";}
				$select.=">" . i18n_get_translated($options[$m]) . "</option>";
				}
			}
		?>
		$('field_<?php echo $field[$n]["name"]?>').innerHTML="<?php echo $select ?>";
		<?php
		}
	}


#$sql.=" join keyword k";
#$sql.=" join resource_keyword rk on rk.resource=r.ref and k.ref=rk.keyword and ";

?>
