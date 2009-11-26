<?php
/**
 * Helper and rendering function for the configuration pages in the team center
 * 
 * @package ResourceSpace
 * @subpackage Includes
 */

/**
 * Validate the given field.
 * 
 * If the field validates, this function will store it in the provided conifguration
 * module and key.
 * 
 * @param string $fieldname Name of field (provided to the render functions)
 * @param string $modulename Module name to store the field in.
 * @param string $modulekey Module key
 * @param string $type Validation patthern: (bool,safe,float,int,email,regex)
 * @param string $required Optional required flag.  Defaults to true.
 * @param string $pattern If $type is 'regex' the regex pattern to use.
 * @return bool Returns true if the field was stored in the config database.
 */
function validate_field($fieldname, $modulename, $modulekey, $type, $required=true, $pattern=''){
    global $errorfields, $lang;
    $value = getvalescaped($fieldname, '');
    if ($value=='' && $required==true){
        $errorfields[$fieldname]=$lang['cfg-err-fieldrequired'];
        return false;
    }
    elseif ($value=='' && $required==false){
        set_module_config_key($modulename, $modulekey, $value); 
    }
    else {
        switch ($type){
            case 'safe':
                if (!preg_match('/^.+$/', $value)){
                    $errorfields[$fieldname] = $lang['cfg-err-fieldsafe'];
                    return false;
                }
                break;
            case 'float':
                if (!preg_match('/^[\d]+(\.[\d]*)?$/', $value)){
                    $errorfields[$fieldname] = $lang['cfg-err-fieldnumeric'];
                    return false;
                }
                break;
            case 'int':
                if (!preg_match('/^[\d]+$/', $value)){
                    $errorfields[$fieldname] = $lang['cfg-err-fieldnumeric'];
                    return false;
                }
                break;
            case 'email':
                if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $value)){
                    $errorfields[$fieldname] = $lang['cfg-err-fieldemail'];
                    return false;
                }
                break;
            case 'regex':
                if (!preg_match($pattern, $value)){
                    $errorfields[$fieldname] = $lang['cfg-err-fieldsafe'];
                    return false;
                }
                break;
            case 'bool':
                if (strtolower($value)=='true')
                    $value=true;
                elseif (strtolower($value)=='false')
                    $value=false;
                else {
                    $errorfields[$fieldname] = $lang['cfg-err-fieldsafe'];
                    return false;
                }
                break;
        }
       set_module_config_key($modulename, $modulekey, $value);
       return true;             
    }
}
/**
 * Renders a select element.
 * 
 * Takes an array of options (as returned from sql_query and returns a valid 
 * select element.  The query must have a column aliased as value and label.  
 * Option groups can be created as well with the optional $groupby parameter.
 * This function retrieves a language field in the form of 
 * $lang['cfg-<fieldname>'] to use for the element label.
 * 
 * <code>
 * $options = sql_select("SELECT name as label, ref as value FROM resource_types");
 * 
 * render_select_option('myfield', $options, 18);
 * </code>
 * 
 * @param string $fieldname Name to use for the field.
 * @param string $opt_array Array of options to fill the select with
 * @param mixed $selected If matches value the option is marked as selected
 * @param string $groupby Column to group by
 * @return string HTML output.
 */
function render_select_option($fieldname, $opt_array, $selected, $groupby=''){
    global $errorfields, $lang;
    $output = '';
    $output .= "<tr><th><label for=\"$fieldname\">".$lang['cfg-'.$fieldname]."</label></th>";
    $output .= "<td><select name=\"$fieldname\">";
    if ($groupby!=''){
        $cur_group = $opt_array[0][$groupby];
        $output .= "<optgroup label=\"$cur_group\">";
    }
    foreach ($opt_array as $option){
        if ($groupby!='' && $cur_group!=$option[$groupby]){
          $cur_group = $option[$groupby];
          $output .= "</optgroup><optgroup label=\"$cur_group\">";            
        }
        $output .= "<option ";
        $output .= $option['value']==$selected?'selected="selected" ':'';
        $output .= "value=\"{$option['value']}\">{$option['label']}</option>";
    }
    $output .= '</optgroup>';
    $output .= isset($errorfields[$fieldname])?'<span class="error">* '.$errorfields[$fieldname].'</span>':'';
    $output .= '</td></tr>';
    return $output;
}

/**
 * Render a yes/no field with the given fieldname.
 * 
 * This function will use $lang['cfg-<fieldname>'] as the text label for the
 * element.
 * @param string $fieldname Name of field.
 * @param bool $value Current field value
 * @return string HTML Output
 */
function render_bool_option($fieldname, $value){
    global $errorfields, $lang;
    $output = '';
    $output .= "<tr><th><label for=\"$fieldname\">".$lang['cfg-'.$fieldname]."</label></th>";
    $output .= "<td><select name=\"$fieldname\">";
    $output .= "<option value='true' ";
    $output .= $value?'selected':'';
    $output .= ">Yes</option>";
    $output .= "<option value='false' ";
    $output .= !$value?'selected':'';
    $output .= ">No</option></select>";
    $output .= isset($errorfields[$fieldname])?'<span class="error">* '.$errorfields[$fieldname].'</span>':'';
    $output .= "</td></tr>";
    return $output;
}

/**
 * Renders a text field for a given field name.
 * 
 * Uses $lang['cfg-<fieldname>'] as the field label.
 * 
 * @param string $fieldname Name of field
 * @param string $value Current field value
 * @param int $size Size of text field, optional, defaults to 20
 * @param string $units Optional units parameter. Displays to right of text field.
 * @return string HTML Output
 */
function render_text_option($fieldname, $value, $size=20, $units=''){
    global $errorfields, $lang;
    if (isset($errorfields[$fieldname]) && isset($_POST[$fieldname]))
        $value = $_POST[$fieldname];
    $output = '';
    $output .= "<tr><th><label for=\"$fieldname\">".$lang['cfg-'.$fieldname]."</label></th>";
    $output .= "<td><input type=\"text\" value=\"$value\" size=\"$size\" name=\"$fieldname\"/> $units ";
    $output .= isset($errorfields[$fieldname])?'<span class="error">* '.$errorfields[$fieldname].'</span>':'';
    $output .= "</td></tr>";
    return $output;
}
