<?php

function parse_xml_into_array($text)
	{
	# For a given XML string, returns an easy to parse array of all elements.

	$parents=array();
	
	$parser=xml_parser_create();
	xml_parse_into_struct($parser,$text,$vals,$index);
	#xml_parser_free($parser);
	
	$error=xml_get_error_code($parser);
	if ($error!==0)
		{
		$line=xml_get_current_line_number($parser);
		echo "XML parse error: " .xml_error_string($error) . "<br>Line: " . $line . "<br><br>";
		
		$s=explode("\n",$text);
		echo "<pre>" . trim(htmlspecialchars(@$s[$line-2])) . "<br>";
		echo "<b>" . trim(htmlspecialchars(@$s[$line-1])) . "</b><br>";
		echo trim(htmlspecialchars(@$s[$line])) . "<br></pre>";		
		exit();
		}
		
	# For each element, attach the attributes from the separate $vals array, plus the ID and the ID of the parent node.
	# This makes it much more useful.
	foreach ($index as $tag=>$instances)	
		{
		foreach ($instances as $id=>$instance)
			{
			# Copy the $vals node into the $index tree at this point, replacing the reference number (this is more useful).
			$index[$tag][$id]=$vals[$instance];
			$index[$tag][$id]["id"]=$instance;
			
			# Find the parent for this tree.
			# Traverse back up until the level changes. This is the parent element.
			$level=$vals[$instance]["level"];
			for ($n=$instance;$n>0;$n--)
				{
				if ($level>$vals[$n]["level"] && $vals[$n]["type"]!="cdata")
					{
					# Level changed
					break;
					}
				}
			$index[$tag][$id]["parent"]=$n;
			
			# Store to handy parents index, used for quickly establishing node ancestors
			$parents[$instance]=$n;
			
			# Remove CDATA and CLOSE tag types as they are not needed.
			if ($index[$tag][$id]["type"]=="cdata") {unset($index[$tag][$id]);}
			elseif ($index[$tag][$id]["type"]=="close") {unset($index[$tag][$id]);}
			}
		}

	# Append parents index as a tree
	$index["parents"]=$parents;
	
	return $index;
	}

function get_nodes_by_tag($tag,$parent=-1,$search="",$recurse=false)
	{
	global $xml;
	if (!array_key_exists($tag,$xml)) {return array();}
	
	if ($parent==-1 && $search=="")
		{
		# No parent or search specified, return all rows.
		return $xml[$tag];
		}
	else
		{
		# Parent or search specified. Match rows
		$return=array();
		foreach ($xml[$tag] as $node)
			{
			if (
			($parent==-1 || $node["parent"]==$parent || ($recurse && node_is_ancestor_of($node["id"],$parent)))
			&&
			($search=="" || (isset($node["attributes"]["NAME"]) && $node["attributes"]["NAME"]==$search))
			)
			 {
			 $return[]=$node;
			 }
			}

		return $return;
		}
	}

function node_is_ancestor_of($node,$parent,$depth=0)
	{
	# Establish if node is child/grandchild/greatgrandchild etc. of parent
	# Used when searching for nodes using the recurse mode (e.g. all alerts nested within a table however deep - such as field level alerts)
	global $xml;
	if ($xml["parents"][$node]==$parent) {return true;} # Found a match
	
#	echo "[$depth " . $xml["parents"][$node] . "]";
	
	if ($depth>100) {return false;} # Get out of recursion failsafe
	if ($xml["parents"][$node]==0) {return false;} # Hit the root.
	return node_is_ancestor_of($xml["parents"][$node],$parent,$depth+1);
	}


function get_node_by_id($tag,$id)
	{
	global $xml;
	if (!isset($xml[$tag])) {return false;}
	foreach ($xml[$tag] as $instance)
		{
		if ($instance["id"]==$id) {return $instance;}
		}
	return false;
	}	
	


?>