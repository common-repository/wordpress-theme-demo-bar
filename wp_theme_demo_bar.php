<?php
/*
 * Plugin Name: Wordpress Theme Demo Bar
 * Plugin URI: http://zenverse.net/wordpress-theme-demo-bar-plugin/
 * Description: Allows any wordpress theme to be previewed without activating it. A demo bar would be shown on top of page, allow users to preview another theme. The demo bar is customisable at admin panel. More than 1 extra CSS files can be loaded too.
 * Author: Zen
 * Author URI: http://zenverse.net/
 * Version: 1.6.3
*/

// Pre 2.6 compatibility (BY Stephen Rider)
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	if ( defined( 'WP_SITEURL' ) ) define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
	else define( 'WP_CONTENT_URL', get_option( 'url' ) . '/wp-content' );
}

$zv_wptdb_plugin_name = 'Wordpress Theme Demo Bar';
$zv_wptdb_plugin_dir = WP_CONTENT_URL.'/plugins/wordpress-theme-demo-bar/';
$zv_wptdb_plugin_ver = '1.6.3';
$zv_wptdb_plugin_url = 'http://zenverse.net/wordpress-theme-demo-bar-plugin/';
$zv_wptdb_themedemo = (isset($_GET['themedemo']))?$_GET['themedemo']:null;
$zv_wptdb_cssdemo = $zv_wptdb_themedemo;
$zv_wptdb_in_demo = false;
$wptdb_shortname = 'wptdb_';
$zv_wptdb_siteurl = get_option('siteurl');
$zv_wptdb_sitehome = get_option('home');
$zv_wptdb_show_demobar = true;
$zv_wptdb_persistent = true;
$zv_wptdb_ontop = false;
$zv_wptdb_ischild = false;
$zv_wptdb_showtooltip = true;
$zv_wptdb_default_format = '<a title="{name} has been previewed {hits} times." href="{demo}">Preview {name} ({hits})</a>';
$zv_wptdb_demobar_height = 31;
$zv_wptdb_demobar_pos = 'top';

$zv_wptdb_defaultimages = array('close' => $zv_wptdb_plugin_dir.'images/close.gif',
'info' => $zv_wptdb_plugin_dir.'images/info.gif',
'download' => $zv_wptdb_plugin_dir.'images/download.gif',
'buy' => $zv_wptdb_plugin_dir.'images/download.gif',);

require_once(dirname(__FILE__).'/functions.php');

//if it is admin page don't initiate preview mode, unless it is editing theme options
if (!isset($_GET['editfunctionsphp'])) {
$wptdb_currpage = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
if (strpos($wptdb_currpage,$zv_wptdb_siteurl.'/wp-admin/') !== false) {
$zv_wptdb_themedemo = '';
}
} else {//editing theme options
  if($zv_wptdb_themedemo != '' && file_exists(get_theme_root() . "/".$zv_wptdb_themedemo)) {
    $wptdb_currpage = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
    if (strpos($wptdb_currpage,$zv_wptdb_siteurl.'/wp-admin/themes.php') !== false) {
      if (isset($_GET['page']) && $_GET['page'] == 'functions.php') {
        add_action('admin_footer','wpthemedemobar_functionsphp_foot');
      }
    }
  }
}


if($zv_wptdb_themedemo !='' && file_exists(get_theme_root() . "/$zv_wptdb_themedemo")) {
/*check if it is a child theme*/
$zv_wptdb_currtheme = get_theme_data(ABSPATH . 'wp-content/themes/'.$zv_wptdb_themedemo.'/style.css');
$zv_wptdb_themename = $zv_wptdb_currtheme['Name'];
$zv_wptdb_theme_realdir = $zv_wptdb_themedemo;

if ($zv_wptdb_currtheme['Template'] != '') {//is child theme
$zv_wptdb_themedemo = $zv_wptdb_currtheme['Template'];
$zv_wptdb_ischild = true;
}

add_filter('template','use_preview_theme');
add_filter('stylesheet','use_preview_css');
$zv_wptdb_in_demo = true;

/* update popularity */
//cookie name cannot contain blank space and dotz
$curr_cookiename = wptdb_make_validcookiename($zv_wptdb_theme_realdir);

	$wpthemedemobar_pop = get_option('wpthemedemobar_pop');
	if (!isset($_COOKIE['wptdb_'.$curr_cookiename])) {
	  if ( empty($wpthemedemobar_pop) ) {
	  $temppop = array("$curr_cookiename" => 1);
    add_option('wpthemedemobar_pop',$temppop);
    $wptdb_curr_popularity = 1;
    } else {
      if (isset($wpthemedemobar_pop[$curr_cookiename])) {
        $wpthemedemobar_pop[$curr_cookiename] += 1;
      } else {
        $wpthemedemobar_pop[$curr_cookiename] = 1;
      }
      update_option('wpthemedemobar_pop',$wpthemedemobar_pop);
      $wptdb_curr_popularity = $wpthemedemobar_pop[$curr_cookiename];
    }
    @setcookie('wptdb_'.$curr_cookiename,'1',time()+60*60*24*5);
  } else {
      if (isset($wpthemedemobar_pop[$curr_cookiename])) {
      $wptdb_curr_popularity = $wpthemedemobar_pop[$curr_cookiename];
      } else {
      $wptdb_curr_popularity = 1;
      }
  }
  unset($wpthemedemobar_pop);
  
    if ($wptdb_curr_popularity > 2) { $wptdb_curr_popularity .= ' times'; } 
    else if ($wptdb_curr_popularity == 2) { $wptdb_curr_popularity = ' twice'; }
    else if ($wptdb_curr_popularity == 1) { $wptdb_curr_popularity = ' once'; }
    else { $wptdb_curr_popularity .= ' time'; }
}

function use_preview_theme($themename) {
	global $zv_wptdb_themedemo;
	return $zv_wptdb_themedemo;
}

function use_preview_css($cssname) {
	global $zv_wptdb_cssdemo;
	return $zv_wptdb_cssdemo;
}

if ($zv_wptdb_in_demo) {
//initiate head & footer
$wpthemedemobar_options = get_option('wpthemedemobar_options');
$wpthemedemobar_misc = get_option('wpthemedemobar_misc');

$run_wpthemedemobar = false;
if (!isset($_GET['hidebar'])) {
if (empty($wpthemedemobar_options)) {
  $run_wpthemedemobar = true;
} else {
  if (!in_array('bar',$wpthemedemobar_options['hide'])) {
  $run_wpthemedemobar = true;
  }
}
  if ($run_wpthemedemobar) {
  add_action('wp_head', 'wpthemedemobar_head');
  add_action('wp_footer', 'wpthemedemobar_footer');
  }
}

//initiate persistent preview
if (isset($_GET['hidebar'])) { $zv_wptdb_persistent = false; }
if (!empty($wpthemedemobar_options)) {
  if (in_array('persistent',$wpthemedemobar_options['hide']) || isset($_GET['hidebar'])) {
  $zv_wptdb_persistent = false;
  }
}

//fixed position feature
if (!empty($wpthemedemobar_misc)) {
  if ($wpthemedemobar_misc['demobar_fixedpos']=='1') {
  $zv_wptdb_ontop = true;
  }
}

//height of demo bar feature
if (!empty($wpthemedemobar_misc)) {
  if ($wpthemedemobar_misc['demobar_height']!='' && is_numeric($wpthemedemobar_misc['demobar_height'])) {
  $zv_wptdb_demobar_height = $wpthemedemobar_misc['demobar_height'];
  }
}

// demo bar position
if (!empty($wpthemedemobar_misc)) {
  if ($wpthemedemobar_misc['demobar_pos']=='bottom') {
    $zv_wptdb_demobar_pos = 'bottom';
  }
}

//tooltip feature
if (!empty($wpthemedemobar_options)) {
  if (in_array('tooltip',$wpthemedemobar_options['hide'])) {
  $zv_wptdb_showtooltip = false;
  }
}
  
// show parent theme feature 
if (!empty($wpthemedemobar_options)) {
  if (in_array('donthideparentname',$wpthemedemobar_options['hide'])) {
    if ($zv_wptdb_ischild) {
    // get parent info
    $zv_parenttheme = get_theme_data(ABSPATH . 'wp-content/themes/'.$zv_wptdb_themedemo.'/style.css');
    
    $zv_wptdb_themename = $zv_wptdb_themename.' - Child of <a href="'.$zv_wptdb_sitehome.'/?themedemo='.$zv_wptdb_themedemo.'" target="_blank">'.$zv_parenttheme['Name'].'</a>';
    }
  }
}

unset($wpthemedemobar_options);
}

function wpthemedemobar_head() {
global $zv_wptdb_in_demo,$zv_wptdb_plugin_dir,$zv_wptdb_themedemo,$_GET,$zv_wptdb_siteurl,$zv_wptdb_ontop,$zv_wptdb_theme_realdir,$zv_wptdb_showtooltip,$zv_wptdb_demobar_height,$zv_wptdb_demobar_pos;

  if ($zv_wptdb_in_demo) {
  echo "<!-- Start Wordpress Theme Demo Bar Plugin wp_head -->".'
  
  <script type="text/javascript">
  //<![CDATA[
  // enable the demo bar only if javascript is activated
  document.write(\'<style type="text/css">\n\');
  document.write(\'html { padding-'.$zv_wptdb_demobar_pos.':'.$zv_wptdb_demobar_height.'px; }\n\');
  document.write(\'#wpthemedemobar { position:absolute; '.$zv_wptdb_demobar_pos.':0px !important; }\n\');
  document.write(\'</style>\n\');
  //]]>
  </script>'."\n";
  
  echo '<link rel="stylesheet" href="'.$zv_wptdb_plugin_dir.'default.css" type="text/css" />'."\n";
  
  if ($zv_wptdb_showtooltip) {
  echo '<script type="text/javascript" src="'.$zv_wptdb_plugin_dir.'js/qtip.js"></script>'."\n";
  }
  
  if ($zv_wptdb_ontop) { echo '
  <style type="text/css">#wpthemedemobar { position:fixed !important; }</style>
  <!--[if IE 6]>
  <style type="text/css">#wpthemedemobar { position:absolute !important; }</style>
  <![endif]-->
  '; }
  
  $wpthemedemobar_usercss = get_option('wpthemedemobar_usercss');
  if ($wpthemedemobar_usercss != '') {
  echo '<style type="text/css">
  '.$wpthemedemobar_usercss.'
  </style>'."\n";
  }
  
  // load extra css files
  if (isset($_GET['extracss']) && $_GET['extracss'] != '') {
  $extracss_array = explode(',',$_GET['extracss']);
  $themeroot = get_theme_root();
  
  if (count($extracss_array) > 0) {
    foreach ($extracss_array as $extracss) {
    echo "\n".'<link rel="stylesheet" href="'.$zv_wptdb_siteurl.'/wp-content/themes/'.$zv_wptdb_theme_realdir.'/'.$extracss.'.css" type="text/css" media="screen" />'."\n";
    }
  }
  }

  echo '<!-- End Wordpress Theme Demo Bar Plugin wp_head -->'."\n\n";
  }
}

function wpthemedemobar_footer() {
global $wptdb_curr_popularity,$_GET,$zv_wptdb_in_demo,$zv_wptdb_plugin_dir,$zv_wptdb_themedemo,$wptdb_shortname,$zv_wptdb_siteurl,$zv_wptdb_persistent,$zv_wptdb_themename,$zv_wptdb_theme_realdir,$zv_wptdb_defaultimages,$zv_wptdb_ontop,$zv_wptdb_demobar_height,$zv_wptdb_demobar_pos,$zv_wptdb_sitehome;


  if ($zv_wptdb_in_demo) {
  echo '<!-- Start Wordpress Theme Demo Bar Plugin wp_footer -->'."\n";
    
$wpthemedemobar_options = get_option('wpthemedemobar_options');
$wpthemedemobar_themes = get_option('wpthemedemobar_themes');

$themes = get_themes();
//echo '<pre>';
//var_dump($themes);

$selectmenusettings = null;
if (!empty($wpthemedemobar_options) && !empty($wpthemedemobar_options['options'])) {
  if ($wpthemedemobar_options['options']['themejump_parentchild_rel'] == 0) {
    $selectmenusettings['hierarchical'] = 0;
  }
  if ($wpthemedemobar_options['options']['themejump_sort'] == 'hits') {
    $selectmenusettings['orderby'] = 'hits';
  }
  if ($wpthemedemobar_options['options']['themejump_order'] == 'desc') {
    $selectmenusettings['order'] = 'desc';
  }
  if ($wpthemedemobar_options['options']['themejump_show_hits'] == 1) {
    $selectmenusettings['show_hits'] = 1;
  }
}

$optionstr = wptdb_return_themejump_options($themes,$wpthemedemobar_options,$zv_wptdb_theme_realdir,$selectmenusettings);

$ct = get_theme_data(ABSPATH . 'wp-content/themes/'.$zv_wptdb_themedemo.'/style.css');
echo '<script type="text/javascript">
//<![CDATA[
';

if ($zv_wptdb_persistent) {

$extravariable = '';
if (isset($_GET['extracss']) && $_GET['extracss']!='') {
$extravariable = '&extracss='.$_GET['extracss'];
}

echo '
var wptdb_pageLinks = document.getElementsByTagName("body")[0].getElementsByTagName("a");

for(var i=0;i<wptdb_pageLinks.length;i++) {

//first determine if it is an internal link
if (wptdb_pageLinks[i].href.indexOf("'.$zv_wptdb_sitehome.'") >= 0) { // internal link
if (wptdb_pageLinks[i].href.indexOf("themedemo=") < 0 && wptdb_pageLinks[i].href.indexOf("/wp-admin/") < 0 && wptdb_pageLinks[i].href.indexOf("/wp-login.php") < 0 && wptdb_pageLinks[i].href.indexOf("?feed=") < 0 && wptdb_pageLinks[i].href.indexOf("/feed/") < 0 && wptdb_pageLinks[i].href.charAt(0)!="#") { // valid url to replace
  var wptdb_findanchor = wptdb_pageLinks[i].href.indexOf("#");
  
  if (wptdb_pageLinks[i].href.indexOf("?") >= 0) { // parameter already exist.
    if ( wptdb_findanchor >= 0) { // # is there
      wptdb_pageLinks[i].href = wptdb_pageLinks[i].href.substr(0,wptdb_findanchor)+"&themedemo='.$zv_wptdb_theme_realdir.$extravariable.'"+wptdb_pageLinks[i].href.substr(wptdb_findanchor);
    } else {
      wptdb_pageLinks[i].href += "&themedemo='.$zv_wptdb_theme_realdir.$extravariable.'";
    }
  } else {
    if ( wptdb_findanchor >= 0) { // # is there
      wptdb_pageLinks[i].href = wptdb_pageLinks[i].href.substr(0,wptdb_findanchor)+"?themedemo='.$zv_wptdb_theme_realdir.$extravariable.'"+wptdb_pageLinks[i].href.substr(wptdb_findanchor);
    } else {
      wptdb_pageLinks[i].href += "?themedemo='.$zv_wptdb_theme_realdir.$extravariable.'";
    }
  }

} // end valid url to replace
} // end internal link

} // end for loop

';
}

echo "

function wptdb_isIE() {
return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent);
}

function wptdb_getBrowserWidthHeight(w_or_h) {
var intH = 0;
var intW = 0;
if( typeof window.innerWidth  == 'number' ) {
  intH = window.innerHeight;
  intW = window.innerWidth;
} else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
  intH = document.documentElement.clientHeight;
  intW = document.documentElement.clientWidth;
} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
  intH = document.body.clientHeight;
  intW = document.body.clientWidth;
}
if (w_or_h == 'w') {
return parseInt(intW);
}
if (w_or_h == 'h') {
return parseInt(intH);
}
}

function wptdb_getScrollXY(x_or_y) {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  if (x_or_y == 'x') {
  return scrOfX;
  }
  if (x_or_y == 'y') {
  return scrOfY;
  }
}

function wptdb_resize_demobar_forie() {
var myWidth = wptdb_getBrowserWidthHeight('w');
document.getElementById('wpthemedemobar').style.width = myWidth+'px';
}
";

if ($zv_wptdb_ontop) {
echo "
function wptdb_fixdemobarpos_forie() {
var curr_y_pos = wptdb_getScrollXY('y');
var curr_height = wptdb_getBrowserWidthHeight('h');
var demobar_height = ".$zv_wptdb_demobar_height.";
";

if ($zv_wptdb_demobar_pos == 'top') {
echo "curr_height = 0;
demobar_height = 0;";
}

echo "
document.getElementById('wpthemedemobar').style.top = (curr_height+curr_y_pos-demobar_height)+'px';
}
";
}

echo "
function wptdb_close() {
document.getElementById('wpthemedemobar').style.display = 'none'
document.getElementsByTagName('html')[0].style.padding".ucfirst($zv_wptdb_demobar_pos)." = '0px';
";

if ($zv_wptdb_persistent) {
echo "
//revert all edited links
var pageLinks = document.getElementsByTagName('body')[0].getElementsByTagName('a');
for(var i=0;i<pageLinks.length;i++) {

//first determine if it is an internal link
if (pageLinks[i].href.indexOf('".$zv_wptdb_sitehome."') >= 0) { // internal link

  if (pageLinks[i].href.indexOf('?') >= 0) { // parameter exist, so remove them.
  pageLinks[i].href = pageLinks[i].href.replace(/\?themedemo=".$zv_wptdb_themedemo.$extravariable."/,'');
  pageLinks[i].href = pageLinks[i].href.replace(/&themedemo=".$zv_wptdb_themedemo.$extravariable."/,'');
  }

} // end internal link

} // end for loop
";
}

echo "
}

function createDemoBar() {
";

if ($zv_wptdb_demobar_pos == 'top') {
echo "//check if adminbar exists
if (document.getElementById('admin-bar-css')) {
  var dhead = document.getElementsByTagName('head')[0];
  document.write('<style type=\"text/css\">#wpthemedemobar { position:absolute; top:28px !important; }</style>');
}
";

}

echo "
//create the demobar
var divTag = document.createElement('div');
divTag.setAttribute('id','wpthemedemobar');
  if (wptdb_isIE()) {
  var currwidth = wptdb_getBrowserWidthHeight('w');
  divTag.style.width = currwidth+'px';
  window.onresize = wptdb_resize_demobar_forie;
  }

if (document.body.firstChild){
  document.body.insertBefore(divTag, document.body.firstChild);
} else {
  document.body.appendChild(divTag);
}
";

  if ($zv_wptdb_ontop) {
  echo "if (wptdb_isIE()) {
  //setInterval('wptdb_fixdemobarpos_forie();',10);
  }
  ";
  }
  
echo "
}
createDemoBar();
";

//get custom images
$wpthemedemobar_customimages = get_option('wpthemedemobar_customimages');
$selected_customimages = return_selected_customimages($wpthemedemobar_customimages,$zv_wptdb_defaultimages);



echo 'var wptdb = document.getElementById("wpthemedemobar");
wptdb.innerHTML = \'<div class="wpthemedemobar_wrapper"><div class="wptdb_left">';

//close button
$echothis = true;
if (!empty($wpthemedemobar_options)) {
  if (in_array('closebutton',$wpthemedemobar_options['hide'])) {
  $echothis = false;
  }
}
if ($echothis) {
echo '<a title="Close this Demo Bar and stop previewing this theme" class="wptdb_qtip" href="javascript:void(0)" onclick="wptdb_close();return false;"><img src="'.$selected_customimages['close'].'" /></a>';
}

//theme info/dowload/buy button
$echothis = true;
if (!empty($wpthemedemobar_options)) {
  if (in_array('themeinfo',$wpthemedemobar_options['hide'])) {
  $echothis = false;
  }
}
if ($echothis) {

//individual theme settings
if (is_array($wpthemedemobar_themes) && $wpthemedemobar_themes && count($wpthemedemobar_themes)>0 && isset($wpthemedemobar_themes[$zv_wptdb_theme_realdir])) {

  if ($wpthemedemobar_themes[$zv_wptdb_theme_realdir]['info'] != '') {
  echo '<a class="wptdb_qtip" target="_blank" title="Visit the Theme Info page in a new window" href="'.$wpthemedemobar_themes[$zv_wptdb_theme_realdir]['info'].'"><img src="'.$selected_customimages['info'].'" /></a>';
  }

  if ($wpthemedemobar_themes[$zv_wptdb_theme_realdir]['download'] != '') {
    if ($wpthemedemobar_themes[$zv_wptdb_theme_realdir]['downloadorbuy']=='download'||$wpthemedemobar_themes[$zv_wptdb_theme_realdir]['downloadorbuy']=='buy') {} else { $wpthemedemobar_themes[$zv_wptdb_theme_realdir]['downloadorbuy'] = 'download'; }
  echo '<a class="wptdb_qtip" target="_blank" title="'.ucfirst($wpthemedemobar_themes[$zv_wptdb_theme_realdir]['downloadorbuy']).' this theme" href="'.$wpthemedemobar_themes[$zv_wptdb_theme_realdir]['download'].'"><img src="'.$selected_customimages[$wpthemedemobar_themes[$zv_wptdb_theme_realdir]['downloadorbuy']].'" /></a>';
  }
}//end individual theme settings

}//end if ($echothis)

echo '</div><div class="wptdb_current">Currently Previewing Theme : <span class="wptdb_themename">'.wptdb_adaptjs($zv_wptdb_themename).'</span>';

$echothis = false;
if (empty($wpthemedemobar_options)) { $echothis = true; } else {
  if (!in_array('pop',$wpthemedemobar_options['hide'])) { $echothis = true; }
}
if ($echothis) { echo ' <span class="wptdb_popularity">&nbsp;(Previewed '.$wptdb_curr_popularity.')</span>'; }

echo '</div>';

$echothis = false;
if (empty($wpthemedemobar_options)) { $echothis = true; } else {
  if (!in_array('jump',$wpthemedemobar_options['hide'])) { $echothis = true; }
}
if ($echothis) { echo '<div class="wptdb_jumpbar_wrapper"><form method="get"><select class="wptdb_jumpbar_select" name="themedemo">'.$optionstr.'</select><input class="wptdb_jumpbar_preview_button" type="submit" value="Preview" /></form><div style="display:none;" id="wpthemedemobar_pos_teller">'.$zv_wptdb_demobar_pos.'</div></div>'; }

echo "</div>';";  // end innerHTML

echo '
//]]>
</script>';
  echo "<!-- End Wordpress Theme Demo Bar Plugin wp_footer -->\n\n";
  }
}

######################################################################################################

/* admin menu */
add_action('admin_menu', 'TDB_menu');

function TDB_menu() {
  global $zv_wptdb_plugin_name;
  $pluginpage = add_options_page($zv_wptdb_plugin_name, 'WP Theme Demo Bar', 8, __FILE__, 'TDB_options');
  add_action('admin_head-'.$pluginpage, 'wpthemedemobar_admin_head');
}

function TDB_options() {
global $zv_wptdb_plugin_name,$wptdb_shortname,$zv_wptdb_plugin_ver,$zv_wptdb_plugin_url,$zv_wptdb_plugin_dir,$zv_wptdb_siteurl,$zv_wptdb_defaultimages,$zv_wptdb_default_format,$zv_wptdb_sitehome;

$autorefreshmsg = ' <a href="'.$zv_wptdb_siteurl.'/wp-admin/options-general.php?page=wordpress-theme-demo-bar/wp_theme_demo_bar.php">Auto Refreshing...</a>
<script type="text/javascript">
//<![CDATA[
setTimeout("location.href=\''.$zv_wptdb_siteurl.'/wp-admin/options-general.php?page=wordpress-theme-demo-bar/wp_theme_demo_bar.php\';",1000);
//]]>
</script>';

$themes = get_themes();
$ct = current_theme_info();
ksort($themes);
//$theme_total = count( $themes );

if (isset($_POST['wptdb_form_updateoptions'])) {

if (!isset($_POST[$wptdb_shortname.'hide'])) {
$_POST[$wptdb_shortname.'hide'] = array();
}

if (!isset($_POST[$wptdb_shortname.'private'])) {
$_POST[$wptdb_shortname.'private'] = array();
}

if (in_array($_POST[$wptdb_shortname.'tj_parentchild_rel'],array('0','1'))) {
$tempoption['themejump_parentchild_rel'] = $_POST[$wptdb_shortname.'tj_parentchild_rel'];
} else {
$tempoption['themejump_parentchild_rel'] = '1';
}

if (in_array($_POST[$wptdb_shortname.'tj_sort'],array('default','hits'))) {
$tempoption['themejump_sort'] = $_POST[$wptdb_shortname.'tj_sort'];
} else {
$tempoption['themejump_sort'] = 'default';
}

if (in_array($_POST[$wptdb_shortname.'tj_order'],array('asc','desc'))) {
$tempoption['themejump_order'] = $_POST[$wptdb_shortname.'tj_order'];
} else {
$tempoption['themejump_order'] = 'asc';
}

if (in_array($_POST[$wptdb_shortname.'tj_show_hits'],array('0','1'))) {
$tempoption['themejump_show_hits'] = $_POST[$wptdb_shortname.'tj_show_hits'];
} else {
$tempoption['themejump_show_hits'] = '1';
}


$wpthemedemobar_options['hide'] = $_POST[$wptdb_shortname.'hide'];
$wpthemedemobar_options['private'] = $_POST[$wptdb_shortname.'private'];
$wpthemedemobar_options['options'] = $tempoption;

if (get_option('wpthemedemobar_options')) {
update_option('wpthemedemobar_options',$wpthemedemobar_options);
} else {
add_option('wpthemedemobar_options',$wpthemedemobar_options);
}


echo '<div class="updated" style="padding:5px;"><b>Plugin Options has been updated.</b></div>';
unset($wpthemedemobar_options);
unset($tempoption);
}

if (isset($_POST['wptdb_form_reset_options'])) {
if (get_option('wpthemedemobar_options')) {
delete_option('wpthemedemobar_options');
}
echo '<div class="updated" style="padding:5px;"><b>Plugin Options has been resetted.</b></div>';
}


if (isset($_POST['wptdb_form_updatecolours'])) {

$_POST['looknfeel'] = htmlspecialchars(strip_tags($_POST['looknfeel']));

update_option('wpthemedemobar_usercss',$_POST['looknfeel']);

update_option('wpthemedemobar_customimages',array('close'=>$_POST['customimg_close'],'info'=>$_POST['customimg_info'],'download'=>$_POST['customimg_download'],'buy'=>$_POST['customimg_buy']));

update_option('wpthemedemobar_misc',array('demobar_height'=>$_POST['demobar_height'],'demobar_pos'=>$_POST['demobar_pos'],'demobar_fixedpos'=>$_POST['demobar_fixedpos']));

echo '<div class="updated" style="padding:5px;"><b>Look & Feel Settings has been updated.'.$autorefreshmsg.'</b></div>';
die();
unset($_POST['looknfeel']);
}



if (isset($_POST['wptdb_form_reset_colours'])) {
if (get_option('wpthemedemobar_usercss')) {
delete_option('wpthemedemobar_usercss');
}
if (get_option('wpthemedemobar_customimages')) {
delete_option('wpthemedemobar_customimages');
}
if (get_option('wpthemedemobar_misc')) {
delete_option('wpthemedemobar_misc');
}

echo '<div class="updated" style="padding:5px;"><b>Look & Feel Settings has been resetted.'.$autorefreshmsg.'</b></div>';
die();
}


if (isset($_POST['wptdb_form_reset_pop'])) {
if (get_option('wpthemedemobar_pop')) {
delete_option('wpthemedemobar_pop');
}
echo '<div class="updated" style="padding:5px;"><b>Popularity count has been resetted.</b></div>';
}

if (isset($_POST['wptdb_form_reset_individual_ts'])) {
if (get_option('wpthemedemobar_themes')) {
delete_option('wpthemedemobar_themes');
}
echo '<div class="updated" style="padding:5px;"><b>All Individual Theme Settings have been deleted.</b></div>';
}

if (isset($_POST['wptdb_form_delete_allformat'])) {
if (get_option('wpthemedemobar_formats')) {
delete_option('wpthemedemobar_formats');
}
echo '<div class="updated" style="padding:5px;"><b>All custom output formats have been deleted.</b></div>';
}


if (isset($_POST['wptdb_form_add_individual_themesettings'])) {
if ($_POST['wptdb_themes_theme'] != '') {
  if ($_POST['wptdb_themes_info'] == '' && $_POST['wptdb_themes_download'] == '') {

    echo '<div class="updated" style="padding:5px;"><b>No URL was specified for the theme.</b></div>';
  } else {
    if ($_POST['wptdb_themes_downloadorbuy'] == 'download' || $_POST['wptdb_themes_downloadorbuy'] == 'buy') {
    } else { $_POST['wptdb_themes_downloadorbuy'] = 'download'; }
    
    $wpthemedemobar_themes = get_option('wpthemedemobar_themes');
    if (isset($wpthemedemobar_themes[$_POST['wptdb_themes_theme']])) {
      $_POST['wptdb_form_edit_individual_themesettings'] = 'passed';
    } else {
      $wpthemedemobar_themes[$_POST['wptdb_themes_theme']] = array('info'=>$_POST['wptdb_themes_info'],'download'=>$_POST['wptdb_themes_download'],'downloadorbuy'=>$_POST['wptdb_themes_downloadorbuy']);
      update_option('wpthemedemobar_themes',$wpthemedemobar_themes);
      echo '<div class="updated" style="padding:5px;"><b>The new individual theme settings has been added.</b></div>';
    }

  }
} else {
echo '<div class="updated" style="padding:5px;"><b>No theme was selected.</b></div>';
}
}

if (isset($_POST['wptdb_form_edit_individual_themesettings'])) {
  if ($_POST['wptdb_form_edit_individual_themesettings'] != 'passed') {
  $wpthemedemobar_themes = get_option('wpthemedemobar_themes');
  }
  if ($_POST['wptdb_themes_theme']!='') {
    if (isset($wpthemedemobar_themes[$_POST['wptdb_themes_theme']])) {
      $wpthemedemobar_themes[$_POST['wptdb_themes_theme']] = array('info'=>$_POST['wptdb_themes_info'],'download'=>$_POST['wptdb_themes_download'],'downloadorbuy'=>$_POST['wptdb_themes_downloadorbuy']);
      update_option('wpthemedemobar_themes',$wpthemedemobar_themes);
      echo '<div class="updated" style="padding:5px;"><b>The individual theme settings has been updated.</b></div>';      
    } else {
      echo '<div class="updated" style="padding:5px;"><b>The theme you specified was not found.</b></div>';
    }
  } else {
    echo '<div class="updated" style="padding:5px;"><b>You did not specify which theme to edit.</b></div>';  
  }
}
unset($wpthemedemobar_themes);


if (isset($_POST['wptdb_form_editformat'])) {
$wpthemedemobar_formats = get_option('wpthemedemobar_formats');
  if ($_POST['wptdb_formattags'] != '') {
    if ($_POST['wptdb_formatid'] != '' && is_numeric($_POST['wptdb_formatid'])) {
      if (isset($wpthemedemobar_formats[$_POST['wptdb_formatid']])) {
      $wpthemedemobar_formats[$_POST['wptdb_formatid']]= array('name'=>$_POST['wptdb_formatname'],'format'=>$_POST['wptdb_formattags']);
      //update data
      update_option('wpthemedemobar_formats',$wpthemedemobar_formats);      
      echo '<div class="updated" style="padding:5px;"><b>The format of id '.$_POST['wptdb_formatid'].' has been updated.</b></div>';
      } else {//pass to addformat
      $_POST['wptdb_form_addformat'] = 1;
      }
    } else {
      echo '<div class="updated" style="padding:5px;"><b>Format id was not specified. Please submit the form properly.</b></div>';
    }
  } else {
    echo '<div class="updated" style="padding:5px;"><b>The format cannot be empty.</b></div>';
  }
}


if (isset($_POST['wptdb_form_addformat'])) {
$wpthemedemobar_formats = get_option('wpthemedemobar_formats');
  if ($_POST['wptdb_formattags'] != '') {
    if (empty($wpthemedemobar_formats)) {
      $wpthemedemobar_formats[1] = array('name'=>$_POST['wptdb_formatname'],'format'=>$_POST['wptdb_formattags']);
    } else {
      ksort($wpthemedemobar_formats);
      $allkeys = array_keys($wpthemedemobar_formats);
      $countkeys = count($allkeys);
      $getlastkey = $allkeys[($countkeys-1)];
      $wpthemedemobar_formats[($getlastkey+1)] = array('name'=>$_POST['wptdb_formatname'],'format'=>$_POST['wptdb_formattags']);
    }
      //update data
      update_option('wpthemedemobar_formats',$wpthemedemobar_formats);
      echo '<div class="updated" style="padding:5px;"><b>The new format has been added.'.$autorefreshmsg.'</b></div>';
      die();
  } else {
    echo '<div class="updated" style="padding:5px;"><b>Error. The format is empty.</b></div>';
  }
}


if (isset($_POST['wptdb_form_deleteformat'])) {
$wpthemedemobar_formats = get_option('wpthemedemobar_formats');
  if ($_POST['wptdb_formatid'] != '' && is_numeric($_POST['wptdb_formatid'])) {
    if (isset($wpthemedemobar_formats[$_POST['wptdb_formatid']])) {
    $wpthemedemobar_formats[$_POST['wptdb_formatid']] = array('name'=>'','format'=>'');
    //update data
    update_option('wpthemedemobar_formats',$wpthemedemobar_formats);
    echo '<div class="updated" style="padding:5px;"><b>Format id '.$_POST['wptdb_formatid'].' has been deleted.</b></div>';
    } else {
    echo '<div class="updated" style="padding:5px;"><b>Delete failed. The format does not exist.</b></div>';
    }
  } else {
    echo '<div class="updated" style="padding:5px;"><b>Format id was not specified. Please submit the form properly.</b></div>';
  }
}

?>

<?php
// need upgrade??
if (is_file(dirname(__FILE__).'/upgrades/main.php')) {
  include(dirname(__FILE__).'/upgrades/main.php');
}
?>

<div class="wrap">
<?php screen_icon(); 
$h1style = 'style="background-image:url('.$zv_wptdb_plugin_dir.'images/titleimg.jpg);" class="wptdb_css_optionh1"';
?>
<h2><?php echo wp_specialchars($zv_wptdb_plugin_name); ?></h2>
</div>

<div style="padding:10px;border:1px solid #dddddd;background-color:#fff;-moz-border-radius:10px;margin-top:20px;margin-bottom:20px;">
Version <?php echo $zv_wptdb_plugin_ver; ?> | <a target="_blank" href="<?php echo $zv_wptdb_plugin_url; ?>">Plugin FAQs, Changelog & Info</a> | <a target="_blank" href="http://zenverse.net/support/">Support or Donate via Paypal</a> | <a target="_blank" href="http://zenverse.net/">by ZENVERSE</a>
</div>

<?php
if ($need_upgrade) {
echo '<div class="wptdb_css_notice"><b>We need to perform a simple check on your data and update it if necessary <form action="" method="post" style="display:inline"><input type="submit" class="button-primary" name="wptdb_form_upgrade" value="Proceed" /></form></b></div><br />';
}
?>

<!-- -->

<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_usage')">Usage</a></h1>
<div class="wptdb_css_oneblock" id="wptdb_oneblock_usage">

<h4>To Preview a Theme</h4>
<ul style="list-style:square;margin-top:5px;list-style-position:inside;margin-bottom:20px;">
<li>Add variable 'themedemo' to the URL string. <small style="color:#BF547B">Example: http://myblog.com/?themedemo=the-theme-folder-name</small></li>
</ul>

<h4>To Hide Demo Bar for Individual Preview</h4>
<ul style="list-style:square;margin-top:5px;list-style-position:inside;margin-bottom:20px;">
<li>Add variable 'hidebar' to the URL string. <small style="color:#BF547B">Example: http://myblog.com/?themedemo=mytheme&hidebar</small></li>
</ul>

<h4>To Load Extra CSS Files</h4>
<ul style="list-style:square;margin-top:5px;list-style-position:inside;margin-bottom:20px;">
<li>Add variable 'extracss' to the URL string. <small style="color:#BF547B">Example: http://myblog.com/?themedemo=mytheme&extracss=blue,twocolumn</small></li>
<li>This will load blue.css & twocolumn.css. Seperate the filename using comma, without .css extension.</li>
<li>The variable 'themedemo' must be set in order to load extra css files.</li>
</ul>

<h4>Using shortcode [demobar] and template tag functions</h4>
<ul style="list-style:square;margin-top:5px;list-style-position:inside;margin-bottom:20px;">
<li>Documentation : <a href="http://zenverse.net/using-shortcode-in-wordpress-theme-demo-bar-plugin/" target="_blank">Understanding and using shortcode [demobar]</a></li>
<li>Documentation : <a target="_blank" href="http://zenverse.net/using-template-tag-function-in-wordpress-theme-demo-bar-plugin/">Using Template Tag Functions</a></li>
</ul>
</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_lookfeel')">Look & Feel</a></h1>
<div class="wptdb_css_oneblock" id="wptdb_oneblock_lookfeel">

<?php
$wpthemedemobar_customimages = get_option('wpthemedemobar_customimages');
$selected_customimages = return_selected_customimages($wpthemedemobar_customimages,$zv_wptdb_defaultimages);
?>

<div id="wpthemedemobar" style="top:0px;left:0px;position:static;margin-bottom:20px;position:relative;">
<div class="wpthemedemobar_wrapper">
<div class="wptdb_left">
<a class="wptdb_qtip" title="Close this Demo Bar and stop previewing this theme" href="javascript:void(0)"><img src="<?php echo $selected_customimages['close']; ?>" /></a>
<a class="wptdb_qtip" title="Visit the Theme Info page in a new window" href="javascript:void(0)"><img src="<?php echo $selected_customimages['info']; ?>" /></a>
<a class="wptdb_qtip" title="Download this theme" href="javascript:void(0)"><img src="<?php echo $selected_customimages['download']; ?>" /></a>
</div>
<div class="wptdb_current">
  Currently Previewing Theme : <span class="wptdb_themename">Theme Name</span>
  <span class="wptdb_popularity">&nbsp;(Previewed 1024 times)</span>
</div>
<div class="wptdb_jumpbar_wrapper">
<form method="get">
  <select class="wptdb_jumpbar_select" name="themedemo">
  <option>Theme 1</option><option>Theme 2</option><option>Theme 3</option>
  </select>
  <input class="wptdb_jumpbar_preview_button" type="button" value="Preview" />
  </form>
</div>
</div>
</div>

<?php
$wpthemedemobar_usercss = get_option('wpthemedemobar_usercss');
$wpthemedemobar_misc = get_option('wpthemedemobar_misc');
if ($wpthemedemobar_misc && isset($wpthemedemobar_misc['demobar_height']) && $wpthemedemobar_misc['demobar_height'] != '' && is_numeric($wpthemedemobar_misc['demobar_height'])) {} else {
$wpthemedemobar_misc['demobar_height'] = 31;
}
?>

<form method="post" action="">

<h4>Custom CSS</h4>
Please enter extra css to override the <a href="<?php echo $zv_wptdb_plugin_dir; ?>default.css" target="_blank">default css</a>.<br />
If there are things that cannot be override, please <a target="_blank" href="<?php echo $zv_wptdb_siteurl; ?>/wp-admin/plugin-editor.php?file=wordpress-theme-demo-bar/default.css">edit the default.css file directly</a>. (need wordpress 2.8)
<textarea style="width:100%" rows="10" name="looknfeel"><?php echo stripslashes($wpthemedemobar_usercss); ?></textarea>

<br /><br />
<h4>Custom Image</h4>
<table>
<tr><td>Link to Close Button</td><td><input type="text" name="customimg_close" value="<?php echo $selected_customimages['close']; ?>" style="padding:3px;border:1px solid #cccccc" size="70" /> <img src="<?php echo $selected_customimages['close']; ?>" /></td></tr>
<tr><td>Link to Info Button </td><td><input type="text" name="customimg_info" value="<?php echo $selected_customimages['info']; ?>" style="padding:3px;border:1px solid #cccccc" size="70" /> <img src="<?php echo $selected_customimages['info']; ?>" /></td></tr>
<tr><td>Link to Download Button &nbsp;</td><td><input type="text" name="customimg_download" value="<?php echo $selected_customimages['download']; ?>" style="padding:3px;border:1px solid #cccccc" size="70" /> <img src="<?php echo $selected_customimages['download']; ?>" /></td></tr>
<tr><td>Link to Buy Button</td><td><input type="text" name="customimg_buy" value="<?php echo $selected_customimages['buy']; ?>" style="padding:3px;border:1px solid #cccccc" size="70" /> <img src="<?php echo $selected_customimages['buy']; ?>" /></td></tr>
</table>

<br /><br />

<h4>Misc. Settings</h4>
<table>
<tr><td width="174">
<?php wptdb_show_help_button('We need this to set a padding-top to the &amp;lt;html> element.<br />If your demo bar has different height, you need to tell us here.<br />Default value is 31 (height 30px + border-bottom 1px)'); ?> 
Height of Demo Bar</td><td><input type="text" name="demobar_height" value="<?php echo $wpthemedemobar_misc['demobar_height']; ?>" style="padding:3px;border:1px solid #cccccc" size="4" /> pixels</td></tr>
<tr><td width="174">
<?php wptdb_show_help_button('We need this to set either padding-top or padding-bottom to the &amp;lt;html>.<br />If you choose &quot;bottom&quot;, make sure you activate &quot;Demo Bar has Fixed Position?&quot; below.'); ?> 
Position of Demo Bar</td><td><select name="demobar_pos"><option value="top">Top of page</option><option value="bottom" <?php if (!empty($wpthemedemobar_misc)) { if ($wpthemedemobar_misc['demobar_pos']=='bottom') { echo 'selected="selected"'; } } ?>>Bottom of page</option></select></td></tr>
<tr><td width="174">
<?php wptdb_show_help_button('Check this checkbox to make the demo bar stay on its position even when the user scroll down the page.'); ?> 
Demo Bar has Fixed Position?</td><td><input type="checkbox" value="1" name="demobar_fixedpos" <?php if (!empty($wpthemedemobar_misc)) { if ($wpthemedemobar_misc['demobar_fixedpos']=='1') { echo 'checked="checked"'; } } ?> /></td></tr>
</table>


<p class="submit">
<input type="submit" class="button-primary" name="wptdb_form_updatecolours" value="Save Look & Feel Settings &raquo;" />
<input type="submit" name="wptdb_form_reset_colours" onclick="return confirm('<?php _e('Do you really want to reset your current look & feel settings?'); ?>');" value="Reset to Default" />
</p>

</form>

<div class="wptdb_css_notice"><small>NOTE:<br />
&raquo; Do not include &lt;style> tags in custom css. All html tags will be auto removed.<br />
&raquo; The css you entered above will be loaded after the default.css so that your css can override it. <br />
&raquo; The default unedited css can be found <a target="_blank" href="<?php echo $zv_wptdb_plugin_dir; ?>css/defaultcss.css">here</a>.<br />
&raquo; You can pick colours at <a title="All credits to johndyer.name" target="_blank" href="http://johndyer.name/lab/colorpicker/">here</a>.<br />
&raquo; Leave any input empty to revert to its default value.
</small></div>
</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_options')">Plugin Options</a></h1>

<div class="wptdb_css_oneblock" id="wptdb_oneblock_options">
<form method="post" action="">

<?php
$wpthemedemobar_options = get_option('wpthemedemobar_options');
?>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to hide the demo bar during all theme preview.'); ?> 
<strong>Hide Demo Bar?</strong> <input type="checkbox" value="bar" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('bar',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to hide the number of previews displayed beside the theme name in the demo bar. <br />eg: (previewed 400 times)'); ?> 
<strong>Hide number of previews in Demo Bar?</strong> <input type="checkbox" value="pop" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('pop',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to hide the &quot;Select Theme&quot; drop-down menu in the demo bar.'); ?> 
<strong>Hide "Select Theme" Drop-down in Demo Bar?</strong> <input type="checkbox" value="jump" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('jump',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock">
<div style="float:left">
<?php wptdb_show_help_button('Settings for &quot;Select Theme&quot; drop-down menu in the demo bar'); ?> 
<strong>"Select Theme" Drop-down Settings</strong> &nbsp;</div>
<div style="float:left">
<?php wptdb_show_help_button('If enabled, child themes will be placed below the parent theme.'); ?>
<select name="<?php echo $wptdb_shortname; ?>tj_parentchild_rel"><option value="1">Show parent-child relationship</option><option value="0" <?php if (!empty($wpthemedemobar_options)) { if ($wpthemedemobar_options['options']['themejump_parentchild_rel'] == '0') { echo 'selected="selected"'; } } ?>>Don't show parent-child relationship</option></select> 
<br />
<?php wptdb_show_help_button('If enabled, number of previews will be shown beside theme name in a bracket.'); ?>
<select name="<?php echo $wptdb_shortname; ?>tj_show_hits"><option value="0">Don't show number of previews</option><option value="1" <?php if (!empty($wpthemedemobar_options)) { if ($wpthemedemobar_options['options']['themejump_show_hits'] == '1') { echo 'selected="selected"'; } } ?>>Show number of previews</option></select> 
<br />
<?php wptdb_show_help_button('Sort the themes.'); ?>
<select name="<?php echo $wptdb_shortname; ?>tj_sort"><option value="default">Sort by wordpress default</option><option value="hits" <?php if (!empty($wpthemedemobar_options)) { if ($wpthemedemobar_options['options']['themejump_sort'] == 'hits') { echo 'selected="selected"'; } } ?>>Sort by num. of previews</option></select> 
<select name="<?php echo $wptdb_shortname; ?>tj_order"><option value="asc">Ascendingly</option><option value="desc" <?php if (!empty($wpthemedemobar_options)) { if ($wpthemedemobar_options['options']['themejump_order'] == 'desc') { echo 'selected="selected"'; } } ?>>Descendingly</option></select> 
</div><div style="clear:both"></div>
</div>

<!--div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button(''); ?> 
<strong>Demo Bar has Fixed Position?</strong> <input type="checkbox" value="ontop" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('ontop',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
<br /><small></small></div-->

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to show the name of parent theme if current theme is a child theme. <br />Eg: Currently Previewing Theme : EXULT - Child of Delighted'); ?> 
<strong>Show Parent Theme name? (if exist)</strong> <input type="checkbox" value="donthideparentname" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('donthideparentname',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to hide the close button so the visitors can\'t close the demo bar.'); ?> 
<strong>Hide "Close" Button on Demo Bar?</strong> <input type="checkbox" value="closebutton" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('closebutton',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to hide the link to the Theme Info & Download/Buy Page (if exist) in the demo bar.'); ?> 
<strong>Hide "Theme Info/Download/Buy" Button?</strong> <input type="checkbox" value="themeinfo" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('themeinfo',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('Check this checkbox to disable javascript tooltip on close/buy/theme info button. (if it conflicts with other plugin)'); ?> 
<strong>Disable Javascript Tooltip?</strong> <input type="checkbox" value="tooltip" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('tooltip',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><label>
<?php wptdb_show_help_button('By default, the parameter <em>themedemo?=your-current-theme</em> are automatically added to all internal urls during preview.<br />
This allows visitor to try the theme in other pages. Check this checkbox to disable it.<br />
Note: The links automatically revert back when visitor closes the demo bar.<br />
Note: This feature is off when your hide the demo bar or use \'hidebar\' in url'); ?> 
<strong>Disable Persistent Preview?</strong> <input type="checkbox" value="persistent" name="<?php echo $wptdb_shortname; ?>hide[]" <?php if (!empty($wpthemedemobar_options)) { if (in_array('persistent',$wpthemedemobar_options['hide'])) { echo 'checked="checked"'; } } ?> /></label>
</div>

<div class="wptdb_css_optionblock"><strong>
<?php wptdb_show_help_button('Check whichever theme that you <b>DO NOT</b> want it to be listed in the &quot;Select Theme&quot; menu.'); ?> 
Do you have any Private Themes? Hide them.</strong><br />

<script type="text/javascript">
//<![CDATA[

function checknmarklabel(labelid) {
var xgetelem = document.getElementById('tdb_cbox_'+labelid);
if (xgetelem.checked) {
document.getElementById('tdb_labeldiv_'+labelid).style.backgroundImage = 'url(<?php echo $zv_wptdb_plugin_dir; ?>images/private.gif)';
document.getElementById('tdb_labeldiv_'+labelid).style.backgroundColor = '#fff';
document.getElementById('tdb_labeldiv_'+labelid).style.border = '1px outset #aaaaaa';
} else {
document.getElementById('tdb_labeldiv_'+labelid).style.backgroundImage = '';
document.getElementById('tdb_labeldiv_'+labelid).style.backgroundColor = '#f6f6f6';
document.getElementById('tdb_labeldiv_'+labelid).style.border = '1px solid #d1d1d1';
}
}

//]]>
</script>

<?php
$labelloop = 0;

  foreach($themes as $theme_single) {
    
  if ($theme_single['Parent Theme'] != '') {//child theme
  $themedir = $theme_single['Stylesheet'];
  } else {
  $themedir = $theme_single['Template'];
  }
    
    $isprivate = false;
    if (!empty($wpthemedemobar_options)) { 
    if (in_array($themedir,$wpthemedemobar_options['private'])) { $isprivate = true; }
    }
  
  echo '<div style="';
  
  if ($isprivate) { echo 'background-image:url('.$zv_wptdb_plugin_dir.'images/private.gif);border:1px outset #aaaaaa;'; } else { echo 'border:1px solid #d1d1d1;'; }
  
  echo 'background-color:#f6f6f6;background-position:top right;background-repeat:no-repeat;float:left;width:280px;margin-right:5px;margin-top:5px;padding:0px;" id="tdb_labeldiv_'.$labelloop.'" onclick="checknmarklabel(\''.$labelloop.'\')"><label style="display:block;padding:5px;"><input type="checkbox" id="tdb_cbox_'.$labelloop.'" value="'.$themedir.'" name="'.$wptdb_shortname.'private[]" ';
  
  if ($isprivate) { echo 'checked="checked"'; }
  
  echo '/> '.$theme_single['Name'].' <small><a id="tdb_a_'.$labelloop.'" target="_blank" href="'.get_option('home').'/?themedemo='.$themedir.'">Preview</a></small></label></div>';
  $labelloop++;
  }

?>
<div style="clear:both"></div>
</div>


		<p class="submit">
			<input type="submit" name="wptdb_form_updateoptions" class="button-primary" value="<?php _e('Update Options'); ?> &raquo;" />
			<input type="submit" name="wptdb_form_reset_options" onclick="return confirm('<?php _e('Do you really want to reset your current configuration?'); ?>');" value="<?php _e('Reset Options'); ?>" />
		</p>
</form>
</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_stats')">All Themes & Stats</a></h1>
<div class="wptdb_css_oneblock" style="padding:0px" id="wptdb_oneblock_stats">

<?php
  //$loopid = 1;
  $wpthemedemobar_pop = get_option('wpthemedemobar_pop');
  $wpthemedemobar_themes = get_option('wpthemedemobar_themes');
  //echo '<pre>';var_dump($wpthemedemobar_themes);
  
  foreach($themes as $theme_single) {
  
  /* child theme */
  $childof = '';
  if ($theme_single['Parent Theme'] != '') {//child theme
  $themedir = $theme_single['Stylesheet'];
  
  $childof = '<small class="wptdb_css_onetheme_childthemename"> - Child of '.$theme_single['Parent Theme'].'</small><br />';
  } else {
  $themedir = $theme_single['Template'];
  }  
  
  /* num of previews */
  if (!empty($wpthemedemobar_pop)) {
  $cookied_name = wptdb_make_validcookiename($themedir);
    if (isset($wpthemedemobar_pop[$cookied_name])) {
      $pop = $wpthemedemobar_pop[$cookied_name];
    } else {
      $pop = 0;
    }
  } else {
  $pop = 0;
  }
  
  /* preview URL */
	$preview_link = clean_url( get_option('home') . '/');
	$preview_link = htmlspecialchars( add_query_arg( array('preview' => 1, 'template' => $theme_single['Template'], 'stylesheet' => $theme_single['Stylesheet'], 'TB_iframe' => 'true', 'width' => 600, 'height' => 400 ), $preview_link ) );	
  
  /* private theme */
  $privatetheme = '';
  if (!empty($wpthemedemobar_options)) {
    if (in_array($themedir,$wpthemedemobar_options['private'])) {
      $privatetheme = '<a class="wptdb_qtip" title="This is a Private Theme.<br />Change this at Plugin Options above."><img src="'.$zv_wptdb_plugin_dir.'images/lock.gif" /></a>';
    }
  }
  
  /* individual theme setting */
  $indiv_theme_settings = '';
  if ($wpthemedemobar_themes && is_array($wpthemedemobar_themes) && count($wpthemedemobar_themes)>0) {
    if (isset($wpthemedemobar_themes[$themedir])) {
      if ($wpthemedemobar_themes[$themedir]['info'] != '') {
        $indiv_theme_settings .= '<a class="wptdb_qtip" title="Theme Info Page.<hr />'.$wpthemedemobar_themes[$themedir]['info'].'<hr />Change this at Individual Theme Settings below."><img src="'.$zv_wptdb_plugin_dir.'images/info.gif" /></a> ';
      }
      if ($wpthemedemobar_themes[$themedir]['download'] != '') {
        $indiv_theme_settings .= '<a class="wptdb_qtip" title="'.ucfirst($wpthemedemobar_themes[$themedir]['downloadorbuy']).' Page.<hr />'.$wpthemedemobar_themes[$themedir]['download'].'<hr />Change this at Individual Theme Settings below."><img src="'.$zv_wptdb_plugin_dir.'images/download.gif" /></a> ';
      }
      if ($indiv_theme_settings != '') {
      $indiv_theme_settings = '<li>'.$indiv_theme_settings.'</li>';
      }
    }
  }
  
  /*screenshot url*/
  $screenshoturl = $theme_single['Theme Root URI'].'/'.$theme_single['Template'].'/'.$theme_single['Screenshot'];
  if ($theme_single['Theme Root URI'] == '') {
    $screenshoturl = WP_CONTENT_URL.$theme_single['Template Dir'].'/'.$theme_single['Screenshot'];
  }

    echo '<div class="wptdb_css_onetheme">
    <a href="'.$preview_link.'" target="_blank" class="previewlink thickbox"><img class="wptdb_css_onetheme_ss" src="'.$screenshoturl.'" alt="" /></a>
    <div class="wptdb_css_onetheme_sep"></div>
    <div style="float:left;width:300px;height:125px;border:0px solid red">
    <span class="wptdb_css_onetheme_themename">'.$theme_single['Name'].'</span> 
    <a target="_blank" class="wptdb_qtip" title="Preview this theme in a new window" href="'.get_option('siteurl').'/?themedemo='.str_replace(' ','+',$themedir).'"><img src="'.$zv_wptdb_plugin_dir.'images/see-in-action.gif" /></a>
    '.$privatetheme.'
    <br />'.$childof.'
    <textarea class="wptdb_css_onetheme_textarea" rows="3" onclick="this.select();">'.get_option('siteurl').'/?themedemo='.str_replace(' ','+',$themedir).'</textarea>
    </div>
    <div class="wptdb_css_onetheme_sep"></div>
    <ul>
    <li><span class="wptdb_css_bignum">'.$pop.'</span> unique previews</li>
    <li><a target="_blank" href="'.$zv_wptdb_plugin_dir.'edit-theme-options.php?theme='.$themedir.'">Edit Theme Options &raquo;</a></li>
    '.$indiv_theme_settings.'   
    </ul>
    <div style="clear:both"></div></div>';
    
  //$loopid++;
  }
  
?>
<div style="clear:both"></div>
</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_themeaddon')">Individual Theme Settings</a></h1>
<div class="wptdb_css_oneblock" id="wptdb_oneblock_themeaddon">

<div class="wptdb_css_notice"><small>Here you can add links to the demo bar when users preview your theme. For example, a link to the "Theme Info" page.</small></div><br />

<h4>User-created Settings</h4>
<?php
//$wpthemedemobar_themes = get_option('wpthemedemobar_themes');
//var_dump($wpthemedemobar_themes);

if ($wpthemedemobar_themes && is_array($wpthemedemobar_themes) && count($wpthemedemobar_themes)>0) {
  echo '<table class="widefat"><thead><tr><th>Theme Name</th><th>Individual Settings</th><th>Action</th></tr></thead>';
  foreach ($wpthemedemobar_themes as $themedir => $themesettings) {
  $currthemeinfo = @get_theme_data(ABSPATH . 'wp-content/themes/'.$themedir.'/style.css');
  if ($themesettings['downloadorbuy'] == 'buy'){$sel_b=' selected="selected"';}else{$sel_d=' selected="selected"';}
  
    if ($currthemeinfo) {
    echo '<tr><td width="110">'.$currthemeinfo['Name'].'<p>(<a target="_blank" href="'.$zv_wptdb_sitehome.'/?themedemo='.$themedir.'">'.$themedir.'</a>)</p></td>
    <td>
        <form method="post" action=""><table><tr><td><span class="wptdb_css_standout">Theme Info Page</span></td><td><div id="indiv_settings_original_info_'.$themedir.'">'.$themesettings['info'].'</div><div id="indiv_settings_form_info_'.$themedir.'" style="display:none"><input style="border:1px solid #cccccc;padding:2px" size="50" type="text" name="wptdb_themes_info" value="'.stripslashes($themesettings['info']).'" /></div></td></tr>
        <tr><td>
        <div style="display:inline" id="indiv_settings_original_downloadorbuy_'.$themedir.'"><span class="wptdb_css_standout">'.ucfirst($themesettings['downloadorbuy']).'</span></div><div style="display:none" id="indiv_settings_form_downloadorbuy_'.$themedir.'"><select name="wptdb_themes_downloadorbuy"><option value="download"'.$sel_d.'>Download</option><option value="buy"'.$sel_b.'>Buy</option></select></div>&nbsp;<span class="wptdb_css_standout">Page</span></td><td><div id="indiv_settings_original_download_'.$themedir.'">'.$themesettings['download'].'</div><div style="display:none" id="indiv_settings_form_download_'.$themedir.'"><input style="border:1px solid #cccccc;padding:2px" size="50" type="text" name="wptdb_themes_download" value="'.stripslashes($themesettings['download']).'" /></div></td></tr>
        </table>
    </td><td width="120"><input type="hidden" name="wptdb_themes_theme" value="'.$themedir.'" /><div style="display:none" id="indiv_settings_submitbutton_'.$themedir.'"><input type="submit" class="button-primary" value="Save" name="wptdb_form_edit_individual_themesettings" /></div><input type="button" value="Edit" class="button" onclick="wptdb_jsfunc_toggleform(\''.$themedir.'\'); if (this.value == \'Edit\') { this.value = \'Cancel\' } else { this.value = \'Edit\' }" /><input type="submit" value="Delete" class="button" name="wptdb_form_delete_individual_themesettings" onclick="return confirm(\'Are you sure you want to delete the individual settings for '.$currthemeinfo['Name'].'?\')" /></td></tr></form>';
    }
  }
  echo '</table>';
} else {
  echo '-- None found --';
}
?>


<br /><br />

<h4>Add New Settings</h4>
<form method="post" action="">
<?php
$themes = get_themes();
$themelist = '';
foreach ($themes as $theme_single) {
  if ($theme_single['Parent Theme'] != '') {//child theme
    $themedir = $theme_single['Stylesheet'];
  } else {
    $themedir = $theme_single['Template'];
  }
    $themelist .= '<option value="'.$themedir.'"';
    $themelist .= '>'.$theme_single['Name'].'</option>';
}
?>

<table>
<tr><td>Select a Theme</td><td><select name="wptdb_themes_theme"><option value="">Select a theme</option><?php echo $themelist; ?></select></td></tr>
<tr><td>URL to Theme Info Page</td><td><input type="text" style="border:1px solid #cccccc;padding:2px" size="70" name="wptdb_themes_info" value=""></td></tr>
<tr><td>URL to <select name="wptdb_themes_downloadorbuy"><option value="download">Download</option><option value="buy">Buy</option></select> Page &nbsp;</td><td><input type="text" style="border:1px solid #cccccc;padding:2px" size="70" name="wptdb_themes_download" value=""></td></tr>
</table>
<small><input type="submit" style="margin-top:7px;" class="button-primary" name="wptdb_form_add_individual_themesettings" value="Add" /> Leave any input empty if not in use.</small>
</form>
</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_customformat')">Custom Output Formats</a></h1>
<div class="wptdb_css_oneblock" id="wptdb_oneblock_customformat">
<div class="wptdb_css_notice"><small>Here you can add format to be used by shortcode and template tag. More info at "Plugin Support" below.</small></div>
<br />

<h4>User-created Formats</h4>
<?php
$numofformats = 0;
$wpthemedemobar_formats = get_option('wpthemedemobar_formats');
if (empty($wpthemedemobar_formats)) {
echo 'None found.';
} else {
//var_dump($wpthemedemobar_formats);
  echo '<table class="widefat"><thead><tr><th>Id</th><th>Name</th><th>Format</th><th>Action</th><th>Use this</th></tr></thead>';
  foreach ($wpthemedemobar_formats as $formatid => $format) {
  if ($format['format']!='') {
  $numofformats++;
    echo '<tr><td>'.$formatid.'</td><td><form method="post" action="">
    <div id="wptdb_formatdiv_name_'.$formatid.'">'.htmlspecialchars(stripslashes($format['name'])).'</div>
    <div style="display:none" id="wptdb_formatdiv_editname_'.$formatid.'"><input style="border:1px solid #cccccc;padding:2px" type="text" name="wptdb_formatname" value="'.htmlspecialchars(stripslashes($format['name'])).'" /></div>
    </td><td>
    <div id="wptdb_formatdiv_format_'.$formatid.'">'.htmlspecialchars(stripslashes($format['format'])).'</div>
    <div style="display:none" id="wptdb_formatdiv_editformat_'.$formatid.'"><input style="border:1px solid #cccccc;padding:2px" type="text" name="wptdb_formattags" value="'.htmlspecialchars(stripslashes($format['format'])).'" size="40" /><br />
    <input type="submit" name="wptdb_form_editformat" class="button-primary" value="Save Edit" /></div>
    </td><td width="90">
    <input type="button" name="wptdb_deleteformat" class="button" onclick="wptdb_jsfunc_toggleformatform(\''.$formatid.'\'); if (this.value == \'Edit\') { this.value = \'Cancel Edit\'; } else { this.value = \'Edit\'; }" value="Edit" /><br />
    <input type="submit" onclick="return confirm(\'Are you sure you want to delete this format of id '.$formatid.' ?\')" name="wptdb_form_deleteformat" class="button" value="Delete" />
    <input type="hidden" name="wptdb_formatid" value="'.$formatid.'" />
    </form>
    </td><td width="140"><textarea onclick="this.select()" rows="2" cols="20">[demobar theme="" format="'.$formatid.'"]</textarea></td></tr>';
  } else {//deleted format
    $deletedformats .= '<tr><td>'.$formatid.'</td><td><form method="post" action="">
    <div id="wptdb_formatdiv_name_'.$formatid.'">Deleted</div>
    <div style="display:none" id="wptdb_formatdiv_editname_'.$formatid.'"><input style="border:1px solid #cccccc;padding:2px" type="text" name="wptdb_formatname" value="" /></div></td>
    <td><div id="wptdb_formatdiv_format_'.$formatid.'">Deleted</div>
    <div style="display:none" id="wptdb_formatdiv_editformat_'.$formatid.'"><input style="border:1px solid #cccccc;padding:2px" type="text" name="wptdb_formattags" value="" size="40" /><br />
    <input type="submit" name="wptdb_form_editformat" class="button-primary" value="Save" /></div></td>
    <td width="90"><input type="button" name="wptdb_editformat" class="button" onclick="wptdb_jsfunc_toggleformatform(\''.$formatid.'\'); if (this.value == \'Recover\') { this.value = \'Cancel\'; } else { this.value = \'Recover\'; }" value="Recover" /><br /><input type="hidden" name="wptdb_formatid" value="'.$formatid.'" /></form></td>
    <td width="140">-</td>
    </tr>
    ';
  }
  }//end foreach loop
  
  if ($deletedformats!='') { echo $deletedformats; }
  echo '</table>';
}
//var_dump($wpthemedemobar_formats);
?>

<br /><br />
<h4>Add New Format</h4>
<?php include(dirname(__FILE__).'/listoftags.html'); ?>

<form method="post" action="" onsubmit="if (document.getElementById('wptdb_formattags').value == '') { document.getElementById('wptdb_formattags').focus(); return false; } else { return true; }">
<table><tr>
<td width="60">Name</td><td><input type="text" name="wptdb_formatname" style="border:1px solid #cccccc;padding:2px" size="60" value="" /></td></tr>
<td>Format</td><td><input type="text" name="wptdb_formattags" id="wptdb_formattags" style="border:1px solid #cccccc;padding:2px" size="60" value="" /></td></tr>
</table>
<input type="submit" name="wptdb_form_addformat" value="Add Format" class="button-primary">
</form>


<br /><br />
<h4>Default Format</h4>
If invalid format id was found, default format (below) will be used:
<p><code><?php echo htmlspecialchars($zv_wptdb_default_format); ?></code></p>

</div>


<!-- -->


<h1 <?php echo $h1style; ?>><a onclick="wptdb_toggle('wptdb_oneblock_support')">Plugin Support & Extra</a></h1>
<div class="wptdb_css_oneblock" id="wptdb_oneblock_support">
<h4>General Message</h4>
Wordpress Theme Demo Bar supports child theme since version 1.3. If you have problem displaying child themes, please report it to me at <a href="http://zenverse.net/wordpress-theme-demo-bar-plugin/#respond" target="_blank">here</a>. I will then look into the matter as soon as possible. <br /><br />
If you want version 1.2.1 back, you can get it at <a target="_blank" href="http://wordpress.org/extend/plugins/wordpress-theme-demo-bar/download/">here</a>.
<br /><br /><br />

<h4>What's New in version 1.4 and 1.5?</h4>
Check out the <a href="http://wordpress.org/extend/plugins/wordpress-theme-demo-bar/changelog/" target="_blank">changelog</a>.
<br /><br /><br />

<h4>Documentations you might need</h4>
<ul style="list-style:decimal;list-style-position:inside">
<li><a href="http://zenverse.net/using-shortcode-in-wordpress-theme-demo-bar-plugin/" target="_blank">Understanding and using shortcode [demobar]</a></li>
<li><a target="_blank" href="http://zenverse.net/using-template-tag-function-in-wordpress-theme-demo-bar-plugin/">Using Template Tag Functions</a></li>
</ul>

<br /><br />

<h4>Actions</h4>
<form action="" method="post" style="display:inline"><p class="submit" style="display:inline">
<input type="submit" name="wptdb_form_reset_pop" class="button" onclick="return confirm('Do you really want to reset your themes\' popularity (number of previews)?\nWARNING : This action cannot be undo.');" value="Reset Popularity Count" />
<input type="submit" name="wptdb_form_reset_individual_ts" class="button" onclick="return confirm('Do you really want to delete all your individual theme settings?\nWARNING : This action cannot be undo.');" value="Delete All Individual Theme Settings" />
<input type="submit" name="wptdb_form_delete_allformat" class="button" onclick="return confirm('Do you really want to delete all your custom output formats?\nWARNING : This action cannot be undo.');" value="Delete All Custom Output Formats" />
</form>

<br /><br /><br />

<h4>Author's Message</h4>
The development of this plugin took a lot of time and effort, so please don't forget to <a href="http://zenverse.net/support/">donate via PayPal</a> if you found this plugin useful to ensure continued development.
</div>



<br /><br />
<hr style="border:0px;height:1px;font-size:1px;margin-bottom:5px;background:#dddddd;color:#dddddd" />
<small style="color:#999999">
<a target="_blank" href="http://zenverse.net/category/wordpress-plugins/">More plugins by me</a> &nbsp; | &nbsp; <a target="_blank" href="http://zenverse.net/category/wpthemes/">Free Wordpress Themes</a> &nbsp; | &nbsp; <a target="_blank" href="http://themes.zenverse.net/">Premium Wordpress Themes</a> &nbsp; | &nbsp; Thank you for using my plugin.
</small>


<?php
} // end function TDB_options


function wpthemedemobar_admin_head() {
global $zv_wptdb_plugin_dir;

add_thickbox();
wp_print_styles('thickbox');
wp_print_scripts('thickbox');

echo '<!-- start wordpress theme demo bar admin_head -->
  <link rel="stylesheet" href="'.$zv_wptdb_plugin_dir.'default.css" type="text/css" />
  <link rel="stylesheet" href="'.$zv_wptdb_plugin_dir.'css/style.css" type="text/css" />
  <script type="text/javascript">
  //<![CDATA[
  document.write(\'<link rel="stylesheet" href="'.$zv_wptdb_plugin_dir.'css/style_js.css" type="text/css" />\');  
  //]]>
  </script>
  <script type="text/javascript" src="'.$zv_wptdb_plugin_dir.'js/static.js"></script>
  <script type="text/javascript" src="'.$zv_wptdb_plugin_dir.'js/options.js"></script>
  <script type="text/javascript" src="'.$zv_wptdb_plugin_dir.'js/qtip.js"></script>
  ';
  
  $wpthemedemobar_usercss = get_option('wpthemedemobar_usercss');
  if ($wpthemedemobar_usercss != '') {
  echo '<style type="text/css">
  '.$wpthemedemobar_usercss.'
  </style>';
  }
  
echo '<!-- end wordpress theme demo bar admin_head -->
';
}


/* ################### SHORTCODE ##################### */

function wptdb_shortcode($atts) {
/*
---------------------
more info at http://zenverse.net/using-shortcode-in-wordpress-theme-demo-bar-plugin/
---------------------
*/

global $zv_wptdb_default_format,$zv_wptdb_siteurl;
	extract(shortcode_atts(array(
		'theme' => '',
		'get' => 'preview',
		'format' => '0',
		'autop' => 'true',
	), $atts));

  $output = '';

  if ($atts['theme'] == '') { return '[theme folder name is empty]'; }
  
  if (!file_exists(get_theme_root() . "/".$atts['theme'])) { return '[the theme does not exist]'; }    
  
  $themedata = @get_theme_data(get_theme_root() . "/".$atts['theme'].'/style.css');
  //echo '<pre>';var_dump($themedata);

  $isformattedinfo = true;
  
  if ($atts['format'] == '') {
    if ($atts['get'] != '') {
      $isformattedinfo = false;
    }
  }
  
  //screenshot
  $ss_filename = get_theme_root() . "/".$atts['theme'].'/screenshot';
  $ss_file_ext = array('.png','.gif','.jpg','.jpeg');
  foreach ($ss_file_ext as $ext) {
    if (is_file($ss_filename.$ext)) {
    $ss_filename = $zv_wptdb_siteurl.'/wp-content/themes/'.$atts['theme'].'/screenshot'.$ext;
    break;
    }
  }
  
  //individual theme settings
  $wpthemedemobar_themes = get_option('wpthemedemobar_themes');
  //echo '<pre>';var_dump($wpthemedemobar_themes);
  $infopage = $downloadpage = $buypage = '';
  if ($wpthemedemobar_themes && is_array($wpthemedemobar_themes) && count($wpthemedemobar_themes)>0) {
    if (isset($wpthemedemobar_themes[$atts['theme']])) {
      $infopage = $wpthemedemobar_themes[$atts['theme']]['info'];
      $downloadorbuy_str = $wpthemedemobar_themes[$atts['theme']]['downloadorbuy'].'page';
      $$downloadorbuy_str = $wpthemedemobar_themes[$atts['theme']]['download'];
    }
  }
  
  //number of previews
  $wpthemedemobar_pop = get_option('wpthemedemobar_pop');
  //echo '<pre>';var_dump($wpthemedemobar_pop);
  $hits = 1;
  if ($wpthemedemobar_pop && is_array($wpthemedemobar_pop) && count($wpthemedemobar_pop)>0) {
    if (isset($wpthemedemobar_pop[wptdb_make_validcookiename($atts['theme'])])) {
      $hits = $wpthemedemobar_pop[wptdb_make_validcookiename($atts['theme'])];
    }
  }
  
  //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  $tobereplaced = array('{name}','{folder}','{demo}','{author}','{version}','{screenshot}','{infopage}','{downloadpage}','{buypage}','{hits}','{themeurl}','{desc}');
  $tobereplaced_single = array('name','folder','demo','author','version','screenshot','infopage','downloadpage','buypage','hits','themeurl','desc');
  $replacement = array($themedata['Name'],$atts['theme'],$zv_wptdb_sitehome.'/?themedemo='.str_replace(' ','+',$atts['theme']),$themedata['Author'],$themedata['Version'],$ss_filename,$infopage,$downloadpage,$buypage,$hits,$themedata['URI'],$themedata['Description']);
  //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  
  
  if (!$isformattedinfo) {// single info
    //$allowed_value = array('total','today','yesterday','lastweek','url','version','type','lastupdate',);
    //if (!in_array($atts['get'],$allowed_value)) { $atts['get'] = 'total'; }
    //$output = $getallstat[$atts['get']];
    $formatused = $atts['get'];
    $output = str_replace($tobereplaced_single,$replacement,$formatused);
    
    if ($output == $formatused) {//not replaced, not in allowed array
      $output = '[invalid tag for attribute &quot;get&quot;]';
    }
    
  } else {
  //formatted info

    if (!is_numeric($atts['format']) || $atts['format'] <= 0 ) {
    $formatused = $zv_wptdb_default_format;
    } else {
      $wpthemedemobar_formats = get_option('wpthemedemobar_formats');
      if ($wpthemedemobar_formats == '' || empty($wpthemedemobar_formats) || !isset($wpthemedemobar_formats[$atts['format']]) || $wpthemedemobar_formats[$atts['format']]['format'] == '') {
      $formatused = $zv_wptdb_default_format;
      } else {
      $formatused = $wpthemedemobar_formats[$atts['format']]['format'];
      }
    }
    $output = str_replace($tobereplaced,$replacement,$formatused);
  }

  if ($atts['autop'] != 'false' && $atts['autop'] != '0') {
    $output = wpautop($output);
  }
 
	return stripslashes($output);
}

add_shortcode('demobar', 'wptdb_shortcode');
add_filter('the_content', 'do_shortcode', 11);
add_filter('the_excerpt', 'do_shortcode', 11);


/* ################### TEMPLATE TAG ##################### */

function wptdb_output($args) {
/*
---------------------
more info at http://zenverse.net/using-template-tag-function-in-wordpress-theme-demo-bar-plugin/
---------------------
*/

$allowedvariable = array('theme','format','get','echo');

if (!is_array($args)) { // the argument is string
  if ($args == '' || !$args || $args==null) { echo '[invalid argument]'; return; }
  $queryarray = wptdb_tt_parse_args($args,$allowedvariable);
} else {
  $queryarray = wptdb_tt_remove_invalid_args($args,$allowedvariable);
}

if (empty($queryarray)) { echo '[invalid arguments]'; return; }
$queryarray['autop'] = 'false';

//echo '<pre>';
//var_dump($queryarray);

if (isset($queryarray['echo']) && $queryarray['echo'] == '0') {
return wptdb_shortcode($queryarray);
} else {
echo wptdb_shortcode($queryarray);
}

}


function wptdb_list_themes($args='') {
/*
---------------------
more info at http://zenverse.net/using-template-tag-function-in-wordpress-theme-demo-bar-plugin/#wptdb_list_themes
---------------------
*/

$allowedvariable = array('style','hierarchical','orderby','order','show_hits','echo');

if (!is_array($args)) {// the argument is string
  $queryarray = wptdb_tt_parse_args($args,$allowedvariable);
} else {// the argument is array
  $queryarray = wptdb_tt_remove_invalid_args($args,$allowedvariable);
}

//default value is option
if (empty($queryarray)) { $queryarray['style'] = 'option'; }
if ($queryarray['style'] == '') { $queryarray['style'] = 'option'; }

$output = '';

//load data
$wpthemedemobar_options = get_option('wpthemedemobar_options');
$themes = get_themes();

//determine output
switch ($queryarray['style']) {
case 'option':
  $output = wptdb_return_themejump_options($themes,$wpthemedemobar_options,'',$queryarray);
break;
//case 'list':
  //$output = wptdb_return_themejump_lists($themes,$wpthemedemobar_options);
//break;
default:
  echo '[invalid value for attribute "style"]'; return;
break;
}



if (isset($queryarray['echo']) && $queryarray['echo'] == '0') {
return $output;
} else {
echo $output;
}

}

/* ################### MEDIA BUTTON ##################### */

add_action('media_buttons', 'wpthemedemobar_add_media_button', 20);
		
function wpthemedemobar_add_media_button() {
global $zv_wptdb_plugin_dir;
	echo '<a href="'.$zv_wptdb_plugin_dir.'media.php?tab=add&TB_iframe=true&amp;height=500&amp;width=640" class="thickbox" title="Add Wordpress Theme Demo Bar Output"><img src="'.$zv_wptdb_plugin_dir.'images/media.gif" alt="Add Wordpress Theme Demo Bar Output"></a>';
}

/* ################### EDIT FUNCTIONS.PHP ##################### */

function wpthemedemobar_functionsphp_foot() {
global $zv_wptdb_plugin_dir,$zv_wptdb_themedemo,$zv_wptdb_siteurl;
echo '<script type="text/javascript">
//<![CDATA[
try {

var gettheforms = document.getElementsByTagName("form");
for(var i=0;i<gettheforms.length;i++) {
gettheforms[i].action = "'.$zv_wptdb_siteurl.'/wp-admin/themes.php?page=functions.php&editfunctionsphp=1&themedemo='.$zv_wptdb_themedemo.'";
} // end for loop

} catch(e) {}
//]]>
</script>';
}

/* ################### SIDEBAR WIDGET ##################### */

if (is_file(dirname(__FILE__).'/widget.php')) {
include(dirname(__FILE__).'/widget.php');
}


/* delete saved options during deactivation of this plugin 
register_deactivation_hook(__FILE__,'zv_wptdb_unset_options');

function zv_wptdb_unset_options() {
if (get_option('wpthemedemobar_options')) {
delete_option('wpthemedemobar_options');
}
}
*/
?>
