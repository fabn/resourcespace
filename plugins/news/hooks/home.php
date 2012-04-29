<?php
function HookNewsHomeSearchbarbottomtoolbar()
	{
	global $lang,$site_text,$baseurl;
	include_once dirname(__FILE__)."/../inc/news_functions.php";
	$recent = 3;
	$findtext = "";
	$news = get_news_headlines("",$recent,"");
	$results=count($news);
	?>
	<div id="NewsBox" class="HomePanelIN">
	<div class="NewsHeader"></div>	
		<div id="NewsBodyDisplay">
		<?php
		if ($results>0)
			{
			 for ($n=0;($n<$results);$n++)
				{
				echo "<p><a href=\"" . $baseurl . "/plugins/news/pages/news.php?ref=" . $news[$n]["ref"] . "\">" . $news[$n]["title"] ."</a></p>";
				}
			}
		else {echo $lang["news_nonewmessages"];}
		?>
		</div>
	</div>
		
	<?php  
	}
