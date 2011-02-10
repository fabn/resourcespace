<?php

class RSSFeed
{
  var $Articles = array();

  // Channel info
  var $title = '';
  var $link = '';
  var $description = '';
  var $optional = array();
  var $image = array('url' => '', 'title' => '', 'link' => '', 'description' => '', 'w' => 0, 'h' => 0);

  function RSSFeed($title, $link, $description, $optional = '')
  {
    $this->title = $title;
    $this->link = $link;
    $this->description = $description;

    if( is_array($optional) and count($optional) ){
      $this->optional = $optional;
    }
  }

  function AddOptional($key, $value)
  {
    $this->optional[$key] = $value;
  }

  function AddImage($title, $url, $link, $description = '')
  {
    $this->image['title'] = $title;
    $this->image['url'] = $url;
    $this->image['link'] = $link;
    $this->image['description'] = $description;

    if( $tmp = @getimagesize($url) ){
      $this->image['w'] = ($tmp[0] > 144) ? 144 : $tmp[0];
      $this->image['h'] = ($tmp[1] > 400) ? 400 : $tmp[1];
    }
  }

  function AddArticle($title, $link, $description, $optional = '')
  {
    // inserisce un nuovo articolo
    $i = array_push($this->Articles, array('title' => $title, 'link' => $link, 'description' => $description));

    // aggiunge le coppie chiave valore opzionali
    // all'articolo appena inserito
    if( is_array($optional) and count($optional) )
    {
      --$i;
      while( list($k, $v) = each($optional) ){
        $this->Articles[$i][$k] = $v;
      }
    }
  }

  function Output($save = false, $path = '')
  {
    $out = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n" . 
        '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n" . 
        '<channel>' . "\n";
   	global $rss_ttl,$baseurl;
   	$parameters=array();
   	foreach ($_GET as $key=>$value){
		$parameters[$key]=$value;
	}
	$parameters=http_build_query($parameters,'','&amp;');
	global $baseurl;
	
    $out .= "<title>$this->title</title>\n" .
        "<link>$this->link</link>\n" .
        "<description>$this->description</description>\n".
        "<ttl>".$rss_ttl."</ttl>\n".
        '<atom:link href="'.$baseurl.'/plugins/rss2/pages/rssfilter.php?'.$parameters.'" rel="self" type="application/rss+xml" />';

    // Channel optionals
    if( is_array($this->optional) and count($this->optional) )
    {
      while( list($k, $v) = each($this->optional) ){
        $out .= "<$k>$v</$k>\n";
      }
    }

    // Image
    if( $this->image['title'] and $this->image['url'] and $this->image['link'] )
    {
      $out .= "<image>\n" .
      "<title>" . $this->image['title'] . "</title>\n" .
      "<url>" . $this->image['url'] . "</url>\n" .
      "<link>" . $this->image['link'] . "</link>\n";

      if( $this->image['description'] ) {
        $out .= "<description>" . $this->image['description'] . "</description>\n";
      }

      if( $this->image['w'] and $this->image['h'] ) {
        $out .= "<width>" . $this->image['w'] . "</width>\n" .
            "<height>" . $this->image['h'] . "</height>\n";
      }
      $out .= "</image>\n";
    }

    // per ogni item stampa tutte le coppie chiave valore
    for( $i = 0, $c = count($this->Articles); $i < $c; $i++ ){
      $out .= "<item>\n";
      
      while(list($k, $v) = each($this->Articles[$i]))
      {
		$k1=$k;
		if ($k=="guid"){$k1='guid isPermaLink="false"';}
        $out .= "<".$k1.">".$v."</".$k.">\n";
      }
         
      $out .= "</item>\n";
    }

    $out .= "</channel>\n</rss>";

    // True output
    if( !$save or !$path ){
      header("Content-type: application/xml");
      echo $out;
      return true;
    }
    else
    {
      $fh = fopen($path, 'w');
      if($fh) {
        fwrite($fh, $out);
        fclose($fh); 
        return true;
      }
      return false;
    }
  }
}
?>
