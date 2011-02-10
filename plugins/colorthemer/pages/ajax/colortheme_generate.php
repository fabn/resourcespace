<?php

// This is where the gfx and css files are created.
// The greyblu and whitegry themes (which should always be left alone anyway) are used as the base files to do transformations upon.
// The CSS portion here will have to be updated to accomodate any css changes in the base code.

// switch between base styles
switch($style){
	case "greyblu":
	$path = "../../../../gfx/greyblu/interface";
	break;
	case "whitegry":
	$path = "../../../../gfx/whitegry/interface";
	break;
}


$dir_handle = @opendir($path) or die("Unable to open $path");

$n=1;
while ($file = readdir($dir_handle)) {
	if($file == "." || $file == ".." || $file == "_notes" || $file == ".DS_Store")
	continue;
	$files[$n]=$file;
	$n++;
}

foreach ($files as $file){
	$oldfile = $storagedir."/colorthemes/$ref/$file";
	if (file_exists($oldfile)){unlink($oldfile);}
		
	$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ".$path."/".$file." ".$storagedir."/colorthemes/$ref/$file";
	#echo $command;
	shell_exec($command);
}


closedir($dir_handle);

// a few special cases:

$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ../../../../gfx/interface/IcReorder.gif ".$storagedir."/colorthemes/$ref/ColIcReorder.gif";
shell_exec($command);

$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ../../../../gfx/interface/IcRemove.gif ".$storagedir."/colorthemes/$ref/ColIcRemove.gif";
shell_exec($command);

$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ../../../../gfx/interface/IcComment.gif ".$storagedir."/colorthemes/$ref/ColIcComment_anim.gif";
shell_exec($command);

$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ../../../../gfx/interface/IcComment.gif ".$storagedir."/colorthemes/$ref/ColIcComment.gif";
shell_exec($command);


	# also do title.gif
	switch($style){
		
		case "greyblu":
		$titlepath= $storagedir."/../gfx/greyblu/titles/title.gif";
		break;
		
		case "whitegry":
		$titlepath=$storagedir."/../gfx/whitegry/titles/title.gif";
		break;
	}
# convert title colors
$command = $imagemagick_path."/convert -modulate 100,$sat,$hue ".$titlepath." ".$storagedir."/colorthemes/$ref/title.gif";
shell_exec($command);



// create CSS

$myFile = $storagedir."/colorthemes/$ref/Col-".$ref.".css";
$fh = fopen($myFile, 'w');

$textcolor=convert_html_color("".convert_html_color('B5C3D4')."");

// get bg color from bottom of back.gif image
$bgcolor=get_bottom_color_from_image($storagedir."/colorthemes/$ref/back.gif");
$panelback=get_bottom_color_from_image($storagedir."/colorthemes/$ref/resourcepanel.gif");
$colbg=get_bottom_color_from_image($storagedir."/colorthemes/$ref/CollectBack.gif");
$dlbuttonbg=get_bottom_color_from_image($storagedir."/colorthemes/$ref/DownloadButton.gif");
$recordbg=get_bottom_color_from_image($storagedir."/colorthemes/$ref/recordpanel.gif");


switch($style){
	
	case "greyblu":
	$data="body,html {color:".convert_html_color('B5C3D4').";background: ".$bgcolor." url(back.gif) repeat-x fixed;}
h2 {color: #FFFFFF;}
h1 {color: #FFFFFF;}
.CollectDivide {color:".convert_html_color('B5C3D4').";background: #000000 url(CollectDivide.gif) repeat-x;}
.CollectBack {color:".convert_html_color('B6BBC1').";background: ".$colbg." url(CollectBack.gif) repeat-x fixed;}

a:link {color:".convert_html_color('B5C3D4').";}
a:visited {color:".convert_html_color('B5C3D4').";}
a:hover {color:#FFFFFF;}
a:active {color:".convert_html_color('B5C3D4').";}

#Header {border-bottom: 1px solid ".convert_html_color('5A7599').";background: url(title.gif) no-repeat;}
#Footer {border-top: 1px solid ".convert_html_color('94A7C0').";}

.TopInpageNav {border-bottom: 1px solid ".convert_html_color('5A7599').";}


.OxColourPale, .ListviewTitleStyle {color:".convert_html_color('B5C3D4').";}


.Listview td {border-bottom: 1px solid ".convert_html_color('5A7599').";}
.FormError {color: ".convert_html_color('C2E066').";}
.FormIncorrect {color: ".convert_html_color('C2E066').";border: 1px solid #6C8963;background: ".convert_html_color('203B5D').";}
.PageInformal {color: ".convert_html_color('C2E066').";border: 1px solid #6C8963;background: ".convert_html_color('203B5D').";}

.NoFind {border: 1px solid ".convert_html_color('94A7C0').";background: ".convert_html_color('203B5D').";}
.NoFind .highlight {color:#FFFFFF;}
.NoFind a:link, .NoFind a:visited, .NoFind a:hover, .NoFind a:active {color:#FFFFFF;text-decoration:underline;}

.HorizontalWhiteNav a:link, .HorizontalWhiteNav a:visited, .HorizontalWhiteNav a:active, .HorizontalWhiteNav a:hover, .BasicsBox .VerticalNav a:link, .BasicsBox .VerticalNav a:visited, .BasicsBox .VerticalNav a:active, .BasicsBox .VerticalNav a:hover, .ListTitle a:link, .ListTitle a:visited, .ListTitle a:hover, .ListTitle a:active, .HomePanel a:link, .HomePanel a:visited, .HomePanel a:active, .HomePanel a:hover {color:#FFFFFF;}
.HorizontalWhiteNav li, .HorizontalNav li {border-left:1px solid ".convert_html_color('B5C3D4').";}

#CollectionMinRightNav li {border-left:1px solid ".convert_html_color('B6BBC1').";}

.CollectBack a:link, .CollectBack a:visited, .CollectBack a:active {color:".convert_html_color('B6BBC1').";}
.CollectBack a:hover  {color:#FFFFFF;}

#CollectionMenu {border-right: 1px solid ".convert_html_color('79899C').";}

#ThemeBoxPanel, #SearchBoxPanel, #ResearchBoxPanel, .ResourcePanel, .ResourcePanelSmall, .ResourcePanelLarge,  .HomePanelIN, .HomePicturePanelIN {border-top: 1px solid #FFFFFF;border-right: 1px solid ".convert_html_color('94A7C0').";border-bottom: 1px solid ".convert_html_color('94A7C0').";border-left: 1px solid ".convert_html_color('94A7C0').";}

#ThemeBoxPanel , #SearchBoxPanel {background: ".convert_html_color('5A7599')." url(SearchBox.gif) repeat-x;}
#ResearchBoxPanel, .HomePanelIN {background: ".convert_html_color('5A7599')." url(researchpanel.jpg) repeat-x;}
.ResourcePanel {background: ".$panelback." url(resourcepanel.gif) repeat-x;}
.ResourcePanelSmall {background: ".$panelback." url(resourcepanel.gif) repeat-x;}
.ResourcePanelLarge {background: ".$panelback." url(resourcepanel.gif) repeat-x;}
.IconVideo {background: url(IcVideo.gif) no-repeat 140px 5px;}

.ImageBorder, .RecordPanel .Picture {border: 1px solid #FFFFFF;}
.VideoBorder {border: 1px solid #000000;}	
.CollectImageBorder {border: 1px solid ".convert_html_color('79899C').";}	

.PanelShadow {background: url(panelshadow.gif) repeat-x;height: 5px;}
.IconCollect, .KeyCollect	{background: url(IcCol.gif) no-repeat;}
.IconCollectOut, .KeyCollectOut	{background: url(IcColOut.gif) no-repeat;}
.IconEmail, .KeyEmail	{background: url(IcEml.gif) no-repeat;}
.IconPreview, .KeyPreview	{background: url(IcPre.gif) no-repeat;}
.IconStar, .KeyStar	{	background: url(IcStar.gif) no-repeat;}
.IconReorder, .KeyReorder	{	background: url(IcReorder.gif) no-repeat;}
.IconComment, .KeyComment	{	background: url(IcComment.gif) no-repeat;}
.ASC	{background: url(ASC.gif) no-repeat;background-position:center;}
.DESC	{background: url(DESC.gif) no-repeat; background-position:center;}

.NavUnderline {border-bottom: 1px solid ".convert_html_color('5A7599').";}

.SearchSpace .tick, .SearchSpace .SearchItem, .ListviewStyle, .RecordPanel .SearchSimilar, .RecordPanel .item p, .RecordPanel .itemNarrow p, .BasicsBox h1, .BasicsBox h2, .BasicsBox p, .OxColourWhite, .Selected, .Question, .RecordPanel .Title {color: #FFFFFF;} 

.Question{border-top: 1px solid ".convert_html_color('5A7599').";}

.RecordPanel {background: ".$recordbg." url(recordpanel.gif) repeat-x;border-top: 1px solid #FFFFFF;border-right: 1px solid ".convert_html_color('94A7C0').";border-bottom: 1px solid ".convert_html_color('94A7C0').";border-left: 1px solid ".convert_html_color('94A7C0').";}
.RecordPanel .Title {	border-bottom: 1px solid ".convert_html_color('94A7C0').";}
.RecordPanel .RecordDownload {border-top: 1px solid #FFFFFF;border-right: 1px solid ".convert_html_color('94A7C0').";border-bottom: 1px solid ".convert_html_color('94A7C0').";	border-left: 1px solid ".convert_html_color('94A7C0').";background: ".convert_html_color('708DB4')." url(Download.gif) repeat-x;}
.RecordPanel .RecordDownload .DownloadDBlend td {border: 1px solid ".convert_html_color('6280a7').";	color: #FFFFFF;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend .DownloadButton {background: ".$dlbuttonbg." url(DownloadButton.gif?nc=1) repeat-x;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend p {color: ".convert_html_color('758FB1').";text-align: left;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend a {color: #FFFFFF;text-align: left;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend {background: ".convert_html_color('1A447C')." url(DownloadCell.gif) repeat-x;}

.ArchiveResourceTitle {color: ".convert_html_color('C2E066').";}
.RecordStory{color: #000000;background: #FFFFFF url(StoryShadow.gif) repeat-x;}
.RecordStory h1{color: #000000;}
.RecordStory a:hover{color: #000000;}

.BasicsBox .HorizontalNav li {border-right:1px solid ".convert_html_color('B5C3D4').";border-top-style: none;border-bottom-style: none;border-left-style: none;}
.BasicsBox .VerticalNav li, .ThemeBox li {list-style: url(bullet.gif) none inside;}
.ThemeBox {border-bottom: 1px solid ".convert_html_color('5A7599').";}

.HomePanel h2 {background: url(HomeArrow.gif) no-repeat 1px 5px;}

.BasicsBox ul, .BasicsBox ol {color:#FFFFFF;}

.highlight {color: ".convert_html_color('FFFF88').";}

.Tab a {border: 1px solid ".convert_html_color('B5C3D4').";border-bottom-style: none;color: ".convert_html_color('95a3b4').";background-color:".convert_html_color('52719B').";}
.StyledTabbedPanel {border: 1px solid ".convert_html_color('B5C3D4').";border-top: 1px solid white;background-image: url(Download.gif);}
.TabSelected a {color: white;background-color:".convert_html_color('6985A9').";border-top-color: white;}

.InfoTable {border-collapse:collapse;}
.InfoTable tr {background-image: url(SearchBox.gif);background-repeat: repeat-x;background-color: ".convert_html_color('597499').";}
.InfoTable td {border:1px solid ".convert_html_color('94A7C0').";border-top:1px solid #fff;padding:10px;}

.NewFlag {background-color:".convert_html_color('C2E066').";color:".$recordbg.";}

/* Mouseover effect on list views */
.ListviewStyle tr:hover td {background-color: ".convert_html_color('5982bb').";} 
.ListviewStyle tr.ListviewTitleStyle:hover td, .ListviewStyle tr.ListviewBoxedTitleStyle:hover td {background:none;} 


.CollectionPanelInfo .IconReorder	{	background: url(ColIcReorder.gif) no-repeat;}
.CollectionPanelInfo .IconComment	{	background: url(ColIcComment.gif) no-repeat;}
.CollectionPanelInfo .IconCommentAnim	{	background: url(ColIcComment_anim.gif) no-repeat;}
.CollectionPanelInfo .IconRemove	{	background: url(ColIcRemove.gif) no-repeat;}

";
break;

	case "whitegry":
	$data="
 body,html {color:#737373;background: #F3F3F3 url(back.gif) repeat-x fixed;}
h2 {color: #000000;}
h1 {color: #000000;}
.CollectDivide {color:#FFFFFF;background: #000000 url(CollectDivide.gif) repeat-x;}
.CollectBack {color:#FFFFFF;background: ".$colbg." url(CollectBack.gif) repeat-x fixed;}

a:link {color:#737373;}
a:visited {color:#737373;}
a:hover {color:#000000;}
a:active {color:#737373;}

#Header {border-bottom: 1px solid #BBBBBB;background: url(title.gif) no-repeat;}
#Footer {border-top: 1px solid #BBBBBB;}

.TopInpageNav {border-bottom: 1px solid #BBBBBB;}

.Listview tr {background: #FFFFFF url(listblend.gif) repeat-x;}
.OxColourPale, .ListviewTitleStyle {color:#A1A1A1;}

.Listview .ListviewTitleStyle td {background: #F3F3F3 none;}

.Listview td {border-bottom: 1px solid #BBBBBB;color: #737373;}

.FormError {color: #FF0000;}
.FormIncorrect {color: #FF0000;border: 1px solid #BBBBBB;background: #FFFFFF;}
.PageInformal {color: #FF0000;border: 1px solid #BBBBBB;background: #FFFFFF;}

.HorizontalWhiteNav a:link, .HorizontalWhiteNav a:visited, .HorizontalWhiteNav a:active, .BasicsBox .VerticalNav a:link, .BasicsBox .VerticalNav a:visited, .BasicsBox .VerticalNav a:active,  .ListTitle a:link, .ListTitle a:visited, .ListTitle a:active, .HomePanel a:link, .HomePanel a:visited, .HomePanel a:active, .HomePanel a:hover {color:#737373;}

.HorizontalWhiteNav a:hover, .BasicsBox .VerticalNav a:hover, .ListTitle a:hover {color:#000000;}

.HorizontalWhiteNav li, .HorizontalNav li {border-left:1px solid #737373;}

#CollectionMinRightNav li {border-left:1px solid ".convert_html_color('B6BBC1').";}

.CollectBack a:link, .CollectBack a:visited, .CollectBack a:active {color:".convert_html_color('B6BBC1').";}
.CollectBack a:hover, .CollectBack h2  {color:#FFFFFF;}

#CollectionMenu {border-right: 1px solid ".convert_html_color('79899C').";}

#ThemeBoxPanel, #SearchBoxPanel, #ResearchBoxPanel, .ResourcePanel, .ResourcePanelSmall, .ResourcePanelLarge, .HomePanelIN, .HomePicturePanelIN {border: 1px solid #BBBBBB;}

#ThemeBoxPanel, #SearchBoxPanel {background: #DBDBDB url(SearchBox.gif) repeat-x;}
#ResearchBoxPanel, .HomePanelIN {background: #DCDCDC url(researchpanel.jpg) repeat-x;}
.ResourcePanel {background: #FFFFFF;}
.ResourcePanelSmall {background: #FFFFFF url(resourcepanel.gif) repeat-x;}
.ResourcePanelLarge {background: #FFFFFF  repeat-x;}
.IconVideo {background: url(IcVideo.gif) no-repeat 140px 5px;}

.ImageBorder, .RecordPanel .Picture, .VideoBorder {border: 1px solid #000000;}	
.CollectImageBorder {border: 1px solid #000000;}	

.PanelShadow {background: url(panelshadow.gif) repeat-x;height: 5px;}
.IconCollect, .KeyCollect	{background: url(IcCol.gif) no-repeat;}
.IconCollectOut, .KeyCollectOut	{background: url(IcColOut.gif) no-repeat;}
.IconEmail, .KeyEmail	{background: url(IcEml.gif) no-repeat;}
.IconPreview, .KeyPreview	{background: url(IcPre.gif) no-repeat;}
.IconStar, .KeyStar	{	background: url(IcStar.gif) no-repeat;}
.IconReorder, .KeyReorder	{	background: url(IcReorder.gif) no-repeat;}
.IconComment, .KeyComment	{	background: url(IcComment.gif) no-repeat;}
.ASC	{background: url(ASC.gif) no-repeat;background-position:center;}
.DESC	{background: url(DESC.gif) no-repeat; background-position:center;}

.NavUnderline {border-bottom: 1px solid #5A7599;}
.SearchSpace .tick, .SearchSpace .SearchItem, .ListviewStyle, .RecordPanel .SearchSimilar, .RecordPanel .item p, .RecordPanel .itemNarrow p, .BasicsBox h1, .BasicsBox h2, .BasicsBox p, .OxColourWhite, .Selected, .Question, .RecordPanel .Title {color: #3C3C3C}

.Question{border-top: 1px solid #BBBBBB;}
.RecordPanel {background: #F0F0F0 url(recordpanel.gif) repeat-x;	border: 1px solid #BBBBBB;}
.RecordPanel .RecordDownloadSpace h2 {color: #FFFFFF;}
.RecordDownloadSpace .HorizontalWhiteNav a:active, .RecordPanel .RecordDownloadSpace .HorizontalWhiteNav a:hover {color:#FFFFFF;} 
.NoFind {border: 1px solid #BBBBBB;background: #FFFFFF;}
.NoFind .highlight {color:#000000;font-weight: bold;}
.NoFind a:link, .NoFind a:visited, .NoFind a:hover, .NoFind a:active {color:#000000;text-decoration:underline;}
.RecordPanel .Title,  {	border-bottom: 1px solid #BBBBBB;}
.RecordPanel .RecordDownload {background: #DCDCDC url(Download.gif) repeat-x;	border: 1px solid #BBBBBB;}
.RecordPanel .RecordDownload .DownloadDBlend td {border: 1px solid #E2E2E2;color: #FFFFFF;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend .DownloadButton {background: #8ab132 url(CollectBack.gif?nc=1);background-position: 0 -8px;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend p {color: #CCCCCC;text-align: left;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend a {color: #FFFFFF;text-align: left;}
.RecordPanel .RecordDownloadSpace .DownloadDBlend {background: #979797 url(DownloadCell.gif) repeat-x;	border-color: #6D6D6D;}
.ArchiveResourceTitle {color: #C2E066;}
.RecordStory{color: #000000;background: #FFFFFF url(StoryShadow.gif) repeat-x;border-top: none;border-right: 1px solid #BBBBBB;border-bottom: 1px solid #BBBBBB;border-left: 1px solid #BBBBBB;}
.RecordStory h1{color: #000000;}
.RecordStory a:hover{color: #000000;}

.BasicsBox .HorizontalNav li {border-right:1px solid #B5C3D4;border-top-style: none;border-bottom-style: none;border-left-style: none;}
.BasicsBox .VerticalNav li, .ThemeBox li {list-style: url(bullet.gif) none inside;}
.ThemeBox {border-bottom: 1px solid #BBBBBB;}
.HomePanel h2 {background: url(HomeArrow.gif) no-repeat 1px 5px;}

.highlight {color: #990000;}

.Tab a {border: 1px solid #BBBBBB;border-bottom-style: none;color: #666666;background-color:#C3C3C3;}
.StyledTabbedPanel {border: 1px solid #BBBBBB;background-image: url(Download.gif);}
.TabSelected a {color: white;background-color:#B2B2B2;}

.InfoTable {border-collapse:collapse;}
.InfoTable tr {background-image: url(SearchBox.gif);background-repeat: repeat-x;background-color: #597499;}
.InfoTable td {border:1px solid #BBBBBB;border-top:1px solid #BBBBBB;padding:10px;}

.ListviewBoxedTitleStyle td {background-color:#fff;}

.NewFlag {background-color:#737373;color:#fff;}

/* Mouseover effect on list views */
.ListviewStyle tr:hover td {background-color: #eeeeee;} 
.ListviewStyle tr.ListviewTitleStyle:hover td, .ListviewStyle tr.ListviewBoxedTitleStyle:hover td {background:none;} 


.CollectionPanelInfo .IconReorder	{	background: url(ColIcReorder.gif) no-repeat;}
.CollectionPanelInfo .IconComment	{	background: url(ColIcComment.gif) no-repeat;}
.CollectionPanelInfo .IconCommentAnim	{	background: url(ColIcComment_anim.gif) no-repeat;}
.CollectionPanelInfo .IconRemove	{	background: url(ColIcRemove.gif) no-repeat;}

";


 break;
}

if ($rounded=="true"){
$data.="
.PanelShadow
	{
	background: none;
	}

.TabSelected a 
	{	
	border-top-right-radius:5px;border-top-left-radius:5px;	
	-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px; 
	-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;
	}
	
.Tab a 
	{	
	border-top-right-radius:5px;border-top-left-radius:5px;	
	-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px; 
	-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;
	}
	
.StyledTabbedPanel 
	{	
	border-bottom-right-radius:10px;border-bottom-left-radius:10px;	border-top-right-radius:10px;
	-moz-border-radius-bottomleft: 10px;-moz-border-radius-bottomright: 10px; -moz-border-radius-topright: 10px; 
	-webkit-border-bottom-left-radius: 10px;-webkit-border-bottom-right-radius: 10px;-moz-border-radius-topright: 10px; 
	}
		

#ThemeBoxPanel, #SearchBoxPanel, #ResearchBoxPanel, .HomePanelIN, .RecordPanel .RecordDownload
	{
	border-radius:10px;	-moz-border-radius: 10px; -webkit-border-radius: 10px;
	}
	
	
.ResourcePanel, .ResourcePanelSmall, .ResourcePanelLarge
	{
	border-radius:10px;	-moz-border-radius: 10px; -webkit-border-radius: 10px;
	}
.RecordPanel
	{
	border-radius:10px;	-moz-border-radius: 10px; -webkit-border-radius: 10px;
	}
	
.RecordDownloadSpace	
	{
	border-radius:10px;	-moz-border-radius: 10px; -webkit-border-radius: 10px;
	}
.RecordStory{border-radius:10px;	-moz-border-radius: 10px; -webkit-border-radius: 10px;}

";

    }

fwrite($fh, $data);
fclose($fh);

copy($storagedir."/tmp/compositepreview.jpg",$storagedir."/colorthemes/$ref/preview.jpg");

redirect("plugins/colorthemer/pages/colortheme_creator.php");



