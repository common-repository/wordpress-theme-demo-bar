<?php

function return_selected_customimages($wpthemedemobar_customimages,$zv_wptdb_defaultimages) {

$customimages_db = array('close','info','download','buy');

if ($wpthemedemobar_customimages && is_array($wpthemedemobar_customimages) && count($wpthemedemobar_customimages)>0) {

foreach ($customimages_db as $customimagename) {
  if ($wpthemedemobar_customimages[$customimagename]!='') {
  $selected_customimages[$customimagename] = $wpthemedemobar_customimages[$customimagename]; 
  } else {
  $selected_customimages[$customimagename] = $zv_wptdb_defaultimages[$customimagename]; 
  }
}  
  
} else { 
$selected_customimages = $zv_wptdb_defaultimages; 
}

return $selected_customimages;
}

function wptdb_tt_parse_args($args,$allowedvariable) {
$temp_queryarray = explode('&',$args);
$queryarray = array();

foreach ($temp_queryarray as $single_query) {
  $thepairs = explode('=',$single_query);
    if (!empty($thepairs) && count($thepairs) == 2) {
      if (in_array($thepairs[0],$allowedvariable)) {
        $queryarray[$thepairs[0]] = $thepairs[1];
      }
    }
}

return $queryarray;
}


function wptdb_tt_remove_invalid_args($args,$allowedvariable) {
  foreach ($args as $name => $value) {
    if (!in_array($name,$allowedvariable)) {
      unset($args[$name]);
    }
  }
return $args;
}


function wptdb_show_help_button($msg) {
global $zv_wptdb_plugin_dir;
echo '<a class="wptdb_qtip" title="'.$msg.'"><img style="vertical-align:middle;cursor:pointer;cursor:help;" src="'.$zv_wptdb_plugin_dir.'images/help.gif" /></a>';
}


function wptdb_adaptjs($str) {
$str = str_replace('\'','&#39;',$str);
return $str;
}
function wptdb_addslashtoquote($str) {
$str = str_replace('\'','\\\'',$str);
return $str;
}

function wptdb_make_validcookiename_old($name) {
$curr_cookiename = str_replace(" ",'_blank_',$name);
$curr_cookiename = str_replace(".",'_dot_',$curr_cookiename);
$curr_cookiename = str_replace(",",'_comma_',$curr_cookiename);
return $curr_cookiename;
}

function wptdb_make_validcookiename($name) {
$curr_cookiename = str_replace(" ",'_tdbblank_',$name);
$curr_cookiename = str_replace(".",'_tdbdot_',$curr_cookiename);
$curr_cookiename = str_replace(",",'_tdbcomma_',$curr_cookiename);
return $curr_cookiename;
}

function wptdb_reverse_cookied_name($name) {
$curr_cookiename = str_replace('_tdbblank_'," ",$name);
$curr_cookiename = str_replace('_tdbdot_',".",$curr_cookiename);
$curr_cookiename = str_replace('_tdbcomma_',",",$curr_cookiename);
return $curr_cookiename;
}

function wptdb_return_themejump_options($themes,$wpthemedemobar_options,$selected_theme=null,$extrasettings=null) {

if (!$themes || !is_array($themes)) { return false; }

$tempchildthemearray = array();
$tempthemearray = array();

// define default settings ~~~~~~~~~~~~~~
$hierarchical = true;
$showhits = false;
$descending = false;
$orderbyhits = false;
if (is_array($extrasettings) && count($extrasettings)>0) {
  if ($extrasettings['hierarchical']=='0' || $extrasettings['hierarchical']=='false') { $hierarchical = false; }
  if ($extrasettings['show_hits']=='1' || $extrasettings['show_hits']=='true') { $showhits = true; }
  if ($extrasettings['order']=='desc') { $descending = true; }
  if ($extrasettings['orderby']=='hits') { $orderbyhits = true; }
}

$child_seperator = ' &nbsp;-- ';
// end define ~~~~~~~~~~~~~~

if ($showhits || $orderbyhits) { $wpthemedemobar_pop = get_option('wpthemedemobar_pop'); }

foreach ($themes as $theme_single) {

  if ($theme_single['Parent Theme'] != '') {//child theme
    $themedir = $theme_single['Stylesheet'];
  } else {
    $themedir = $theme_single['Template'];
  }

$echoable = false;
if (empty($wpthemedemobar_options)) {
  $echoable = true;
} else {
  if (!in_array($themedir,$wpthemedemobar_options['private'])) {
  $echoable = true;
  }
}

if ($echoable) {
  
  // num of previews
  $pop = '';
  if ($showhits) {
    $pop = ' (0)';
    if (!empty($wpthemedemobar_pop)) {
      $curr_cookiename = wptdb_make_validcookiename($themedir);
      if (isset($wpthemedemobar_pop[$curr_cookiename])) {
        $pop = ' ('.$wpthemedemobar_pop[$curr_cookiename].')';
      }
    }
  }

  // child theme or not
  if ($theme_single['Parent Theme'] != '') {//child theme
    $childthemelist = '<option value="'.$themedir.'"';
    if ($themedir == $selected_theme) { $childthemelist .= ' selected="selected"'; /*if($pop==' (0)'){$pop=' (1)';}*/}
    
    if ($hierarchical) {
      $childthemelist .= '>'.$child_seperator.wptdb_adaptjs($theme_single['Name']).$pop.'</option>';
      $tempchildthemearray[$theme_single['Template']][] = $childthemelist;
    } else {
      $childthemelist .= '>'.wptdb_adaptjs($theme_single['Name']).$pop.'</option>';
      $tempthemearray[$themedir] = $childthemelist;
    }

  } else {//not child theme
    $themelist = '<option value="'.$themedir.'"';
    if ($themedir == $selected_theme) { $themelist .= ' selected="selected"'; /*if($pop==' (0)'){$pop=' (1)';}*/}
    $themelist .= '>'.wptdb_adaptjs($theme_single['Name']).$pop.'</option>';
    
    $tempthemearray[$themedir] = $themelist;
  }
}//end $echoable

}//end foreach

if ($orderbyhits) {
  if (!empty($wpthemedemobar_pop)) {
    arsort($wpthemedemobar_pop);
    //return var_dump($wpthemedemobar_pop);
    foreach ($wpthemedemobar_pop as $cookied_name => $hits) {
      $foldername = wptdb_reverse_cookied_name($cookied_name);
      $temp_sortby_pop_array[$foldername] = $tempthemearray[$foldername];
      unset($tempthemearray[$foldername]);
    }
    
    // some themes dont have hits yet
    foreach ($tempthemearray as $onethemefolder => $htmlcodes) {
      $temp_sortby_pop_array[$onethemefolder] = $htmlcodes;
      unset($tempthemearray[$onethemefolder]);
    }
    
    unset($tempthemearray);
    $sortby_pop_array = array();
    foreach ($temp_sortby_pop_array as $folder => $codes) {
      $sortby_pop_array[$folder] = $codes;
    }
    $tempthemearray = $sortby_pop_array;  
    $tempthemearray = array_reverse($tempthemearray,true);  
  }
}

if ($descending) {
  if (!$orderbyhits) { rsort($tempthemearray); }
  else { $tempthemearray = array_reverse($tempthemearray,true); }
}

$optionstr = '';
foreach ($tempthemearray as $themefolder => $htmlcode) {
  $optionstr .= $htmlcode;
      if (count($tempchildthemearray)>0) {
        if (is_array($tempchildthemearray[$themefolder])) {
          foreach ($tempchildthemearray[$themefolder] as $htmlcode2) {
            $optionstr .= $htmlcode2;
            unset($tempchildthemearray[$themefolder]);
          }
        }
      }
}

// check for left-over (parent is private theme)
if (count($tempchildthemearray) > 0) {
  foreach ($tempchildthemearray as $parentthemefolder => $arrayofchild) {
    foreach ($arrayofchild as $htmlcode3) {
      $htmlcode3 = str_replace($child_seperator,'',$htmlcode3);
      $optionstr .= $htmlcode3;
    }
  }
}

return $optionstr;
}

?>