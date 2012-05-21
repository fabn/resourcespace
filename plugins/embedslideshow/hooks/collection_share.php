<?php

function HookEmbedslideshowCollection_shareExtra_share_options()
	{
	global $ref,$lang,$baseurl,$embedslideshow_min_size,$embedslideshow_max_size;
	?>
	<li><a href="collection_share.php?ref=<?php echo $ref?>&embedslideshow=true"><?php echo $lang["embedslideshow"]?></a></li>	
	<?php
	
	if (getval("embedslideshow","")!="" )
		{
		?>
		<div class="Question">		
		<label><?php echo $lang["embedslideshow_size"] ?></label>
		<select name="size" class="stdwidth">
		<?php
		$sizes=get_all_image_sizes(true);
		foreach ($sizes as $size)
			{
			if ($size["width"]<=$embedslideshow_max_size && $size["width"]>=$embedslideshow_min_size) # Include only sensible sizes
				{
				# Slideshow size is max of height/width so that all images will fit within the slideshow area (for default installs height/width is the same anyway though)
				?>
				<option value="<?php echo $size["id"] ?>" <?php if ($size["id"]==getval("size","pre")) { ?>selected<?php } ?>><?php echo $size["name"] ?> (<?php echo max($size["width"],$size["height"]) ?> pixels)</option>		
				<?php
				}
			}
		?>
		</select>
		<div class="clearerleft"></div>
		</div>		

		<div class="Question">		
		<label><?php echo $lang["embedslideshow_transitiontime"] ?></label>
		<select name="transition" class="stdwidth">
		<option value="0"><?php echo $lang["embedslideshow_notransition"] ?></option>
		<?php for ($n=1;$n<20;$n++) { ?>
		<option value="<?php echo $n ?>" <?php if ($n==getval("transition","4")) { ?>selected<?php } ?>><?php echo str_replace("?",$n,$lang["embedslideshow_seconds"]) ?></option>
		<?php } ?>
		</select>
		<div class="clearerleft"></div>
		</div>	

		<div class="Question">		
		<label><?php echo $lang["embedslideshow_maximise_option"] ?></label>
		<input type="checkbox" value="1" name="maximise" <?php if (!isset($_POST["size"]) || (isset($_POST["maximise"]) && $_POST["maximise"]=="1")) { ?>checked<?php } ?>>
		<div class="clearerleft"></div>
		</div>		
		
		<p><?php echo $lang["embedslideshow_action_description"] ?></p>
		<div class="QuestionSubmit" style="padding-top:0;margin-top:0;">
		<label for="buttons"> </label>
		<input name="generateslideshow" type="submit" value="&nbsp;&nbsp;<?php echo $lang["generateslideshowhtml"]?>&nbsp;&nbsp;" />
		</div>
		<?php
		}
	
	if (getval("generateslideshow","")!="")
		{
		# Create a new external access key
		$key=generate_collection_access_key($ref,0,$lang["slideshow"],1,'');

		# Find image size
		$sizes=get_all_image_sizes(true);
		foreach ($sizes as $size)
			{
			if ($size["id"]==getval("size","")) {break;}
			}
		
		# Slideshow size is max of height/width so that all images will fit within the slideshow area (for default installs height/width is the same anyway though)	
		$width=max($size["width"],$size["height"]);
		$height=$width;

		$width_w_border=$width+8; //expands width to display border

		$height+=48; // Enough space for controls
	
		# Create embed code
		$embed="";
		if ($width<850 && getval("maximise","")==1) { 
			# Maxmimise function only necessary for < screen size slideshows
			$embed.="
			<div id=\"embedslideshow_back_" . $ref . "\" style=\"display:none;position:absolute;top:0;left:0;width:100%;height:100%;min-height: 100%;background-color:#000;opacity: .5;filter: alpha(opacity=50);\"></div>
			<div id=\"embedslideshow_minimise_" . $ref . "\" style=\"position:absolute;top:5px;left:20px;background-color:white;border:1px solid black;display:none;color:black\"><a style=\"color:#000\" href=\"#\" onClick=\"
			var ed=document.getElementById('embedslideshow_" . $ref . "');
			ed.width='" . $width_w_border . "';
			ed.height='" . $height . "';		
			ed.style.position='relative';
			ed.style.top='0';
			ed.style.left='0';
			ed.src='" . $baseurl . "/plugins/embedslideshow/pages/viewer.php?ref=$ref&key=$key&size=" . getval("size","") . "&transition=" . getval("transition","") . "&width=" . $width . "&height=" . $height . "';
			document.getElementById('embedslideshow_minimise_" . $ref . "').style.display='none';
			document.getElementById('embedslideshow_maximise_" . $ref . "').style.display='block';	
			document.getElementById('embedslideshow_back_" . $ref . "').style.display='none';
			\">" . $lang["embedslideshow_minimise"] . "</a></div>
			<div id=\"embedslideshow_maximise_" . $ref . "\" class=\"embedslideshow_maximise\"><a href=\"#\" onClick=\"
			var ed=document.getElementById('embedslideshow_" . $ref . "');
			ed.width='858';
			ed.height='898';
			ed.style.position='absolute';
			ed.style.top='20px';
			ed.style.left='20px';
			ed.src='" . $baseurl . "/plugins/embedslideshow/pages/viewer.php?ref=$ref&key=$key&size=scr&width=850&transition=" . getval("transition","") . "';
			ed.style.zIndex=999;
			document.getElementById('embedslideshow_minimise_" . $ref . "').style.display='block';
			document.getElementById('embedslideshow_maximise_" . $ref . "').style.display='none';	
			document.getElementById('embedslideshow_back_" . $ref . "').style.display='block';	
			\">" . $lang["embedslideshow_maximise"] . "</a></div>";
			}
		$embed.="<iframe id=\"embedslideshow_" . $ref . "\" Style=\"background-color:#fff;cursor: pointer;\" width=\"$width_w_border\" height=\"$height\" src=\"" . $baseurl . "/plugins/embedslideshow/pages/viewer.php?ref=$ref&key=$key&size=" . getval("size","") . "&transition=" . getval("transition","") . "&width=$width&height=$height\" frameborder=0 scrolling=no>Your browser does not support frames.</iframe>";
		
		# Compress embed HTML.
		$embed=str_replace("\n"," ",$embed);
		$embed=str_replace("\t"," ",$embed);
		while (strpos($embed,"  ")!==false) {$embed=str_replace("  "," ",$embed);}
		?>
		<div class="Question">		
		<label><?php echo $lang["slideshowhtml"] ?></label>
		<textarea style="width:535px;height:120px;"><?php echo htmlspecialchars($embed); ?></textarea>
		<div class="clearerleft"></div>
		</div>
		<div class="Question">		
		<label><?php echo $lang["slideshowpreview"] ?></label>
			<div class="Fixed">
			<?php echo $embed ?>
			</div>
		<div class="clearerleft"></div>
		</div>
		
		<?php
		}
	
	return true;
	}

?>
