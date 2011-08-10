<?php /* -------- Check box list ---------------- */ 

# Translate all options
$options=trim_array(explode(",",$fields[$n]["options"]));
$option_trans=array();
$option_trans_simple=array();
for ($m=0;$m<count($options);$m++)
	{
	$trans=i18n_get_translated($options[$m]);
	$option_trans[$options[$m]]=$trans;
	$option_trans_simple[]=$trans;
	}

if ($auto_order_checkbox) {asort($option_trans);}
$options=array_keys($option_trans); # Set the options array to the keys, so it is now effectively sorted by translated string	
	
$set=trim_array(explode(",",$value));
$wrap=0;
$l=average_length($option_trans_simple);
$cols=10;
if ($l>5)  {$cols=6;}
if ($l>10) {$cols=4;}
if ($l>15) {$cols=3;}
if ($l>25) {$cols=2;}

$height=ceil(count($options)/$cols);

global $checkbox_ordered_vertically;
if ($checkbox_ordered_vertically)
	{
	if(!hook('rendereditchkboxes')):
	# ---------------- Vertical Ordering (only if configured) -----------
	?><table cellpadding=2 cellspacing=0><tr><?php
	for ($y=0;$y<$height;$y++)
		{
		for ($x=0;$x<$cols;$x++)
			{
			# Work out which option to fetch.
			$o=($x*$height)+$y;
			if ($o<count($options))
				{
				$option=$options[$o];
				$trans=$option_trans[$option];

				$name=$fields[$n]["ref"] . "_" . base64_encode($option);
				if ($option!="")
					{
					?>
					<td width="1"><input type="checkbox" name="<?php echo $name?>" value="yes" <?php if (in_array($option,$set)) {?>checked<?php } ?> /></td><td><?php echo htmlspecialchars($trans)?>&nbsp;</td>
					<?php
					}
				}
			}
		?></tr><tr><?php
		}
	?></tr></table><?php
	endif;
	}
else
	{				
	# ---------------- Horizontal Ordering (Standard) ---------------------				
	?>
	<table cellpadding=2 cellspacing=0><tr>
	<?php

	foreach ($option_trans as $option=>$trans)
		{
		$name=$fields[$n]["ref"] . "_" . base64_encode($option);
		$wrap++;if ($wrap>$cols) {$wrap=1;?></tr><tr><?php }
		?>
		<td width="1"><input type="checkbox" name="<?php echo $name?>" value="yes" <?php if (in_array($option,$set)) {?>checked<?php } ?> /></td><td><?php echo htmlspecialchars($trans)?>&nbsp;</td>
		<?php
		}
	?></tr></table><?php
	}
	
