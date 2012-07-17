<?php
# Language functions
# Functions for the translation of the application

if (!function_exists("lang_or_i18n_get_translated")) {
function lang_or_i18n_get_translated($text, $mixedprefix)
    {
    # Translates field names / values using two methods:
    # First it checks if $text exists in the current $lang (after $text is sanitized and $mixedprefix - one by one if an array - is added).
    # If not found in the $lang, it tries to translate $text using the i18n_get_translated function.

    $text=trim($text);
    global $lang;

    if (is_array($mixedprefix)) {$prefix = $mixedprefix;}
    else {$prefix = array($mixedprefix);}
    for ($n = 0;$n<count($prefix);$n++) {
        $langindex = $prefix[$n] . strip_tags(strtolower(str_replace(array(", ", " ", "\t", "/", "(", ")"), array("-", "_", "_", "and", "", ""), $text)));

        # Checks if there is a $lang (should be defined for all standard field names / values).
        if (isset($lang[$langindex])) {
            $return = $lang[$langindex];
            break;
        }
    }    
        if (isset($return)) {return $return;}
        else {return i18n_get_translated($text);} # Performs an i18n translation (of probably a custom field name / value).
    }
}

if (!function_exists("i18n_get_translated")) {
function i18n_get_translated($text)
    {
    # For field names / values using the i18n syntax, return the version in the current user's language
    # Format is ~en:Somename~es:Someothername
    $text=trim($text);
    
    # For multiple keywords, parse each keyword.
    if ((strpos($text,",")!==false) && (strpos($text,"~")!==false)) {$s=explode(",",$text);$out="";for ($n=0;$n<count($s);$n++) {if ($n>0) {$out.=",";}; $out.=i18n_get_translated(trim($s[$n]));};return $out;}
    
    global $language,$defaultlanguage;
	$asdefaultlanguage=$defaultlanguage;
	if (!isset($asdefaultlanguage))
		$asdefaultlanguage='en';
    
    # Split
    $s=explode("~",$text);

    # Not a translatable field?
    if (count($s)<2) {return $text;}

    # Find the current language and return it
    $default="";
    for ($n=1;$n<count($s);$n++)
        {
        # Not a translated string, return as-is
        if (substr($s[$n],2,1)!=":" && substr($s[$n],5,1)!=":" && substr($s[$n],0,1)!=":") {return $text;}
        
        # Support both 2 character and 5 character language codes (for example en, en-US).
        $p=strpos($s[$n],':');
		$textLanguage=substr($s[$n],0,$p);
        if ($textLanguage==$language) {return substr($s[$n],$p+1);}
        
        if ($textLanguage==$asdefaultlanguage || $p==0) {$default=substr($s[$n],$p+1);}
        }    
    
    # Translation not found? Return default language
    # No default language entry? Then consider this a broken language string and return the string unprocessed.
    if ($default!="") {return $default;} else {return $text;}
    }
}

if (!function_exists("i18n_get_indexable")) {
function i18n_get_indexable($text)
    {
    # For field names / values using the i18n syntax, return all language versions, as necessary for indexing.
    $text=trim($text);
    $text=str_replace("<br />"," ",$text); // make sure keywords don't get squashed together
    $text=strip_tags($text);
    $text=preg_replace('/~(.*?):/',',',$text);// remove i18n strings, which shouldn't be in the keywords
    //echo $text;die();
    # For multiple keywords, parse each keyword.
    if (substr($text,0,1)!="," && (strpos($text,",")!==false) && (strpos($text,"~")!==false)) {
        $s=explode(",",$text);
        $out="";
        for ($n=0;$n<count($s);$n++) {
        if ($n>0) {$out.=",";} 
        $out.=i18n_get_indexable(trim($s[$n]));
        }
        return $out;
    }

    # Split
    $s=explode("~",$text);

    # Not a translatable field?
    if (count($s)<2) {return $text;}

    $out="";
    for ($n=1;$n<count($s);$n++)
        {
        if (substr($s[$n],2,1)!=":") {return $text;}
        if ($out!="") {$out.=",";}
        $out.=substr($s[$n],3);
        }    
    return $out;
    }
}

if (!function_exists("i18n_get_translations")) {
function i18n_get_translations($value)
    {
    # For a string in the language format, return all translations as an associative array
    # E.g. "en"->"English translation";
    # "fr"->"French translation"
    global $defaultlanguage;
    if (strpos($value,"~")===false) {return array($defaultlanguage=>$value);}
    $s=explode("~",$value);
    $return=array();
    for ($n=1;$n<count($s);$n++)
    {
    $e=explode(":",$s[$n]);
    if (count($e)==2) {$return[$e[0]]=$e[1];}
    }
    return $return;
    }
}

function str_replace_formatted_placeholder($mixedplaceholder, $mixedreplace, $subject, $question_mark = false)
    {
    # Returns a string with all occurrences of the placeholders (array) in subject replaced with the given replace values (array). The replace values are formatted according to the formatting of the placeholders.
    # The placeholders may be written in UPPERCASE, lowercase or Uppercasefirst.
    # Each placeholder will be replaced by the replace value,
    # written with the same case as the placeholder.
    # It's possible to also include "?" as a placeholder for legacy reasons.

    # E.g.
    # str_replace_formatted_placeholder("%extension", $resource["file_extension"], $lang["originalfileoftype"], true)
    # will search for the three words "%EXTENSION", "%extension" and "%Extension" and also the char "?"
    # in the string $lang["originalfileoftype"]. If the found placeholder is %extension
    # it will be replaced by the value of $resource["file_extension"],
    # written in lowercase. If the found placeholder instead would have been "?" the value
    # would have been written in UPPERCASE.

    # Creates a multi-dimensional array of the placeholders written in different case styles.
    if (is_array($mixedplaceholder)) {$placeholder = $mixedplaceholder;}
    else {$placeholder = array($mixedplaceholder);}
    for ($n = 0;$n<count($placeholder);$n++)
        {
        $array_placeholder[$n] = array(strtoupper($placeholder[$n]), strtolower($placeholder[$n]), ucfirstletter($placeholder[$n]));
        }

    # Creates a multi-dimensional array of the replace values written in different case styles.
    if (is_array($mixedreplace)) {$replace = $mixedreplace;}
    else {$replace = array($mixedreplace);}
    for ($n = 0;$n<count($replace);$n++)
        {
        $array_replace[$n] = array(strtoupper($replace[$n]), strtolower($replace[$n]), ucfirst($replace[$n]));
        }

    # Adds "?" to the arrays if required.
    if ($question_mark)
        {
        $array_placeholder[] = "?";
        $array_replace[] = strtoupper($replace[0]);
        }

    # Replaces the placeholders with the replace values and returns the new string.

    $result = $subject;
    for ($n = 0;$n<count($placeholder);$n++)
        {
        if (!isset($array_replace[$n][0])) {break;}
        else
            {
            $result = str_replace($array_placeholder[$n], $array_replace[$n], $result);
            }
        }
    return $result;
    }

function ucfirstletter($string)
    {
    # Returns a string with the first LETTER of $string capitalized.
    # Compare with ucfirst($string) which returns a string with first CHAR of $string capitalized:
    # ucfirstletter("abc") / ucfirstletter("%abc") returns "Abc" / "%Abc"
    # ucfirst("abc") / ucfirst("%abc") returns "Abc" / "%abc"

    # Search for the first letter ([a-zA-Z]), which may or may not be followed by other characters (.*).
    # Replaces the found substring ('$0') with the same substring but now with the first character capitalized, using ucfirst().
    # Note the /e modifier: If this modifier is set, preg_replace() does normal substitution of backreferences in the replacement string, evaluates it as PHP code, and uses the result for replacing the search string.  
    return preg_replace("/[a-zA-Z].*/e", "ucfirst('$0')", $string);

    }
