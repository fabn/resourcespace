<?php

# display collection title if option set.
$search_title = "";
$search_title_links = "";

# Display a title of the search (if there is a title)
$searchcrumbs="";
if ($search_titles_searchcrumbs && $use_refine_searchstring){
$refinements=str_replace(" -",",-",urldecode($search));
$refinements=explode(",",$search);	
if (substr($search,0,1)=="!"){$startsearchcrumbs=1;} else {$startsearchcrumbs=0;}
if ($refinements[0]!=""){
	for ($n=$startsearchcrumbs;$n<count($refinements);$n++){
		$search_title_element=str_replace(";"," OR ",$refinements[$n]);
		if ($n!=0 || $archive!=0){$searchcrumbs.=" > </count> </count> </count> ";}
		$searchcrumbs.="<a href=search.php?search=";
		for ($x=0;$x<=$n;$x++){
			$searchcrumbs.=urlencode($refinements[$x]);
			if ($x!=$n && substr($refinements[$x+1],0)!="-"){$searchcrumbs.=",";}		
		}
		if (!$search_titles_shortnames){
			$search_title_element=explode(":",$refinements[$n]);
			if (isset($search_title_element[1])){
				if (!isset($cattreefields)){$cattreefields=array();}
				if (in_array($search_title_element[0],$cattreefields)){$search_title_element=$lang['fieldtype-category_tree'];}
				else {$search_title_element=str_replace(";"," OR ",$search_title_element[1]);}
				}
			else{
				$search_title_element=$search_title_element[0];
				}
		}
		$searchcrumbs.="&order_by=" . $order_by . "&sort=".$sort."&offset=" . $offset . "&archive=" . $archive."&sort=".$sort.">".$search_title_element."</a>";
	}
}
}

if ($search_titles)
    {
		
	$parameters_string='&order_by=' . $order_by . '&sort='.$sort.'&offset=' . $offset . '&archive=' . $archive.'&sort='.$sort . '&k=' . $k;
	
	 if (substr($search,0,11)=="!collection"){
        if (!isset($collectiondata['savedsearch'])||(isset($collectiondata['savedsearch'])&&$collectiondata['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
           if ($collection_dropdown_user_access_mode){    
                $colusername=$collectiondata['fullname'];
                
                # Work out the correct access mode to display
                if (!hook('collectionaccessmode')) {
                    if ($collectiondata["public"]==0){
                        $colaccessmode= $lang["private"];
                    }
                    else{
                        if (strlen($collectiondata["theme"])>0){
                            $colaccessmode= $lang["theme"];
                        }
                    else{
                            $colaccessmode= $lang["public"];
                        }
                    }
                $collectiondata['name']=$collectiondata['name']." (". $colusername."/".$colaccessmode.")";    
                }
            }
            
        // add a tooltip to Smart Collection titles (which provides a more detailed view of the searchstring.    
        $alt_text='';
        if ($pagename=="search" && isset($collectiondata['savedsearch']) && $collectiondata['savedsearch']!=''){
			$smartsearch=sql_query("select * from collection_savedsearch where ref=".$collectiondata['savedsearch']);
			if (isset($smartsearch[0])){
				$alt_text="title='search=".$smartsearch[0]['search']."&restypes=".$smartsearch[0]['restypes']."&archive=".$smartsearch[0]['archive']."&starsearch=".$smartsearch[0]['starsearch']."'";
			}
		} 
		hook("collectionsearchtitlemod");
        $search_title.= '<div align="left"><h1><div class="searchcrumbs"><span id="coltitle'.$collection.'"><a '.$alt_text.' href=search.php?search=!collection'.$collection.$parameters_string.'>'.i18n_get_translated($collection_tag.$collectiondata["name"]).'</a></span>'.$searchcrumbs.'</div></h1> ';
		}	
    if (substr($search,0,5)=="!last")
        {
		$searchq=substr($search,5);
		$searchq=explode(",",$searchq);
		$searchq=$searchq[0];
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!last'.$searchq.$parameters_string.'>'.str_replace('%qty',$searchq,$lang["n_recent"]).'</a>'.$searchcrumbs.'</h1> ';
        }
    elseif (substr($search,0,8)=="!related")
        {
        $resource=substr($search,8);
		$resource=explode(",",$resource);
		$resource=$resource[0];
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!related'.$resource.$parameters_string.'>'.$lang["relatedresources"].' - '.$lang['id'].$resource.'</a>'.$searchcrumbs.'</h1> ';
        }
    elseif (substr($search,0,7)=="!unused")
        {
		$refinements=str_replace(","," / ",substr($search,7,strlen($search)));	
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!unused'.$parameters_string.'>'.$lang["uncollectedresources"].'</a>'.$searchcrumbs.'</h1>';
        }
    elseif (substr($search,0,11)=="!duplicates")
        {
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!duplicates'.$parameters_string.'>'.$lang["duplicateresources"].'</a>'.$searchcrumbs.'</h1> ';
        }
    elseif (substr($search,0,5)=="!list")
        {
		$resources=substr($search,5);
		$resources=explode(",",$resources);
		$resources=$resources[0];	
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!list'.$resources.$parameters_string.'>'.$lang["listresources"].$resources.'</a>'.$searchcrumbs.'</h1> ';
        }    
    elseif (substr($search,0,15)=="!archivepending")
        {
        $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!archivepending'.$parameters_string.'>'.$lang["resourcespendingarchive"].'</a>'.$searchcrumbs.'</h1> ';
        }
    elseif (substr($search,0,12)=="!userpending")
		{
		$search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!userpending'.$parameters_string.'>'.$lang["userpending"].'</a>'.$searchcrumbs.'</h1> ';
		}
    elseif (substr($search,0,14)=="!contributions")
        {
		$cuser=substr($search,14);
		$cuser=explode(",",$cuser);
		$cuser=$cuser[0];	

        if ($cuser==$userref)
            {
            switch ($archive)
                {
                case -2:
                    $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!contributions'.$cuser.$parameters_string.'>'.$lang["contributedps"].'</a>'.$searchcrumbs.'</h1> ';
                    break;
                case -1:
                    $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!contributions'.$cuser.$parameters_string.'>'.$lang["contributedpr"].'</a>'.$searchcrumbs.'</h1> ';
                    break;
                case -0:
                    $search_title = '<h1 class="searchcrumbs"><a href=search.php?search=!contributions'.$cuser.$parameters_string.'>'.$lang["contributedsubittedl"].'</a>'.$searchcrumbs.'</h1> ';
                    break;
                }
            }
        }
    else if ($archive!=0)
        {
        switch ($archive)
            {
            case -2:
                $search_title = '<h1 class="searchcrumbs"><a href=search.php?search='.$parameters_string.'>'.$lang["userpendingsubmission"].'</a>'.$searchcrumbs.'</h1> ';
                break;
            case -1:
                $search_title = '<h1 class="searchcrumbs"><a href=search.php?search='.$parameters_string.'>'.$lang["userpending"].'</a>'.$searchcrumbs.'</h1> ';
                break;
            case 2:
                $search_title = '<h1 class="searchcrumbs"><a href=search.php?search='.$parameters_string.'>'.$lang["archiveonlysearch"].'</a>'.$searchcrumbs.'</h1> ';
                break;
            case 3:
                $search_title = '<h1 class="searchcrumbs"><a href=search.php?search='.$parameters_string.'>'.$lang["deletedresources"].'</a>'.$searchcrumbs.'</h1> ';
                break;
            }
        }
    else if (substr($search,0,1)!="!")
		{ 
		$search_title = '<h1 class="searchcrumbs"><a href=search.php?search='.$parameters_string.'></a>'.$searchcrumbs.'</h1> '; 
		}   

	// extra collection title links
	if (substr($search,0,11)=="!collection"){
		if ($k=="" && !checkperm("b")){$search_title_links='<a href="collections.php?collection='.$collectiondata["ref"].'" target="collections">&gt;&nbsp;'.$lang["selectcollection"].'</a>&nbsp;&nbsp;';}
		if (count($result)!=0 && $k==""&&$preview_all){$search_title_links.='<a href="preview_all.php?ref='.$collectiondata["ref"].'&order_by='.$order_by.'&sort='.$sort.'&archive='.$archive.'&k='.$k.'">&gt;&nbsp;'.$lang['preview_all'].'</a>';}
		$search_title.='</div>';
		if ($display!="list"){$search_title_links.= '<br /><br />';}
	}
}  
