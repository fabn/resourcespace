<?php if (!hook("renderresultlargethumb")) { ?>

<!--Resource Panel-->
	<div class="ResourcePanelShellLarge" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanelLarge">
	
<?php if (!hook("renderimagelargethumb")) { ?>			
	<?php $access=get_resource_access($result[$n]);
	$use_watermark=check_use_watermark();?>
	<table border="0" class="ResourceAlignLarge<?php if(!hook("replaceresourcetypeicon")){?><?php if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideoLarge<?php } ?><?php } //end hook replaceresoucetypeicon?>">
	<?php hook("resourcetop")?>
	<tr><td>
    <?php
    
    // get mp3 paths if necessary and set $use_mp3_player switch
	if (!(isset($resource['is_transcoding']) && $resource['is_transcoding']==1) && (in_array($result[$n]["file_extension"],$ffmpeg_audio_extensions)|| $result[$n]["file_extension"]=="mp3")&& $mp3_player && $mp3_player_xlarge_view){
		$use_mp3_player=true;
	} 
	else {
		$use_mp3_player=false;
	}
	if ($use_mp3_player){	
		$mp3realpath=get_resource_path($ref,true,"",false,"mp3");
		if (file_exists($mp3realpath)){
			$mp3path=get_resource_path($ref,false,"",false,"mp3");
		}
	}
	
    $show_flv=false;
    if ((in_array($result[$n]["file_extension"],$ffmpeg_supported_extensions) || $result[$n]["file_extension"]=="flv") && $flv_player_xlarge_view){
    $flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
    if (!file_exists($flvfile)) {$flvfile=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension);}
    if (!(isset($result[$n]['is_transcoding']) && $result[$n]['is_transcoding']==1) && file_exists($flvfile) && (strpos(strtolower($flvfile),".".$ffmpeg_preview_extension)!==false)) { $show_flv=true;}
    }
    if ($show_flv)
        {
        # Include the Flash player if an FLV file exists for this resource.
        if(!hook("customflvplay"))
            {
            include "flv_play.php";
            }
        }
    elseif ($use_mp3_player && file_exists($mp3realpath) && hook("custommp3player")){
		// leave preview to the custom mp3 player
	}	    
    elseif ($result[$n]['file_extension']=="swf" && $display_swf && $display_swf_xlarge_view){
        $swffile=get_resource_path($ref,true,"",false,"swf");
        if (file_exists($swffile)) { include "swf_play.php";}	
	}

    else {

    $pre_url=get_resource_path($ref,false,"pre",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"]);
	if (isset($result[$n]["pre_url"])) {$pre_url=$result[$n]["pre_url"];}
    ?>
	<a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img <?php 
	if ($result[$n]["thumb_width"]!="" && $result[$n]["thumb_width"]!=0 && $result[$n]["thumb_height"]!="") { 
		$ratio=$result[$n]["thumb_width"]/$result[$n]["thumb_height"];
		if ($result[$n]["thumb_width"]>$result[$n]['thumb_height']){
			$xlwidth=350;
			$xlheight=350/$ratio;
		} else {
			$xlheight=350;
			$xlwidth=350*$ratio;
		}
		?> width="<?php echo $xlwidth?>" height="<?php echo $xlheight?>" <?php 
	} ?>src="<?php echo $pre_url ?>" class="ImageBorder"
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false) ?>" 
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	/><?php } ?></a>

    <?php } ?>

    </td>
    </tr></table>

<?php if ($use_mp3_player && file_exists($mp3realpath)){
		include "mp3_play.php";
}?>

        
<?php } ?> <!-- END HOOK Renderimagelargethumb-->	
<?php if ($display_user_rating_stars && $k==""){ ?>
		<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
		<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php
		for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
		<?php } ?>
<?php if (!hook("replaceicons")) { ?>
<?php hook("icons");?>
<?php } //end hook replaceicons ?>
<?php if (!hook("rendertitlelargethumb")) { ?>	

<?php } ?> <!-- END HOOK Rendertitlelargethumb -->			
		
		<?php
		# thumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=@$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!=""){
				eval($df[$x]['value_filter']);
			}
			else if (file_exists($plugin)) {include $plugin;}
			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}
			?>		
			<?php 
			// extended css behavior 
			if ( in_array($df[$x]['ref'],$xl_thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfolarge")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo format_display_field($value);			
			if ($show_extension_in_search) { ?><?php echo " " . str_replace_formatted_placeholder("%extension", $result[$n]["file_extension"], $lang["fileextension"])?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfolarge */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),28),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		<div class="ResourcePanelIcons"><?php if ($display_resource_id_in_thumbnail && $ref>0) { echo $ref; } else { ?>&nbsp;<?php } ?></div>	
	    <?php if (!hook("replaceresourcetoolsxl")){?>
		<?php if (!hook("replacefullscreenpreviewicon")){?>
		<span class="IconPreview"><a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		<?php } /* end hook replacefullscreenpreviewicon */?>
		
		<?php if(!hook("iconcollect")){?>
		<?php if (!checkperm("b") && $k=="" && !$use_checkboxes_for_selection) { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12"/></a></span>
		<?php } ?>
		<?php } # end hook iconcollect ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="" && !$use_checkboxes_for_selection) { ?>
		<?php if ($search=="!collection".$usercollection){?><span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		
		<?php if ($allow_share && $k=="") { ?><span class="IconEmail"><a href="resource_email.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"   onClick="return CentralSpaceLoad(this,true);" title="<?php echo $lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><?php } ?>
		<?php if (isset($result[$n][$rating]) && $result[$n][$rating]>0) { ?><div class="IconStar"></div><?php } ?>
		<?php if ($k==""){?><?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<span class="IconComment"><a href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo substr($search,11)?>"  onClick="return CentralSpaceLoad(this,true);" title="<?php echo $lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>		
		<?php if ($order_by=="relevance"){?><div class="IconReorder" onmousedown="InfoBoxWaiting=false;"> </div><?php } ?><?php } ?>	
		<?php } hook("xlargesearchicon");?>
		<div class="clearer"></div>
		<?php if(!hook("thumbscheckboxes")){?>
		<?php if ($use_checkboxes_for_selection){?><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onclick="if (jQuery('#check<?php echo $ref?>').attr('checked')=='checked') { <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if (jQuery('#check<?php echo $ref?>').attr('checked')!='checked'){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&sort=<?php echo $revsort?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"><?php } ?>
		<?php } # end hook thumbscheckboxes?>
		<?php } // end hook replaceresourcetoolsxl ?>
	</div>
<div class="PanelShadow"></div>
</div>
<?php if ($allow_reorder && $display!="list") { 
# Javascript drag/drop enabling.
?>
<script type="text/javascript">
new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'ResourcePanelShellLarge', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
</script>
<?php } ?> 
<?php } ?>

		<?php 