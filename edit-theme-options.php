<?php
define('WP_ADMIN', true);

require_once('../../../wp-load.php');
require_once(dirname(__FILE__).'/functions.php');

// Pre 2.6 compatibility (BY Stephen Rider)
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	if ( defined( 'WP_SITEURL' ) ) define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
	else define( 'WP_CONTENT_URL', get_option( 'url' ) . '/wp-content' );
}

$zv_wptdb_plugin_dir = WP_CONTENT_URL.'/plugins/wordpress-theme-demo-bar/';
$zv_wptdb_siteurl = get_option('siteurl');

if($_GET['theme'] =='' || !file_exists(get_theme_root() . "/".$_GET['theme'])) {
  die();
} else {
  $editingtheme = get_theme_data(ABSPATH . 'wp-content/themes/'.$_GET['theme'].'/style.css');
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; Wordpress Theme Demo Bar &#8212; Edit Theme Options</title>

<link rel="stylesheet" href="<?php echo $zv_wptdb_plugin_dir; ?>css/style.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $zv_wptdb_plugin_dir; ?>css/style_js.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $zv_wptdb_plugin_dir; ?>default.css" type="text/css" />
<script type="text/javascript" src="<?php echo $zv_wptdb_plugin_dir; ?>js/qtip.js"></script>

</head>
<!-- start output -->
<body style="padding:5px;padding-top:0px;padding-bottom:0px;">

<h1 class="wptdb_css_onetheme_themename" style="padding:0px;margin:0px;margin-bottom:5px;margin-top:5px;">Editing Theme Options of <?php echo $editingtheme['Name']; ?> &nbsp;<span style="color:#cccccc;letter-spacing:-5px;">||</span>&nbsp;&nbsp;

<span style="font-size:11px;font-family:verdana"><a class="wptdb_qtip" title="Usually, after you save the theme options below, it redirects you back to your current theme but in fact the options were changed/saved. <br />You can preview the theme using the link at the right to check for the changes." href="javascript:void(0)">Read This First</a>
 &nbsp;&bull;&nbsp; 
<a class="wptdb_qtip" title="Refresh this page back to <?php echo $editingtheme['Name']; ?> theme options." href="<?php echo $zv_wptdb_siteurl; ?>/wp-content/plugins/wordpress-theme-demo-bar/edit-theme-options.php?theme=<?php echo $_GET['theme']; ?>">Refresh</a>
 &nbsp;&bull;&nbsp; 
<a target="_blank" class="wptdb_qtip" title="Preview this theme in a new window after you save the theme options." href="<?php echo $zv_wptdb_siteurl; ?>/?themedemo=<?php echo $_GET['theme']; ?>">Preview</a>
 &nbsp;&bull;&nbsp; 
<a target="_blank" class="wptdb_qtip" title="Please let me know if this does not work for you. Please also specify your wordpress version. Thank you." href="http://zenverse.net/wordpress-theme-demo-bar-plugin/#respond">Report Problem</a>
 &nbsp;&bull;&nbsp; 
<a class="wptdb_qtip" title="Close this window." href="javascript:window.close()">Close</a></span>

</h1>

<div id="iframe_container">Please enable javascript to use this page.</div>

<script type="text/javascript">
document.getElementById('iframe_container').innerHTML = '<iframe id="option-iframe" width="600" height="400" src="<?php echo $zv_wptdb_siteurl; ?>/wp-admin/themes.php?page=functions.php&themedemo=<?php echo $_GET['theme']; ?>&editfunctionsphp=1" style="margin:0px;padding:0px;border:5px solid #cccccc"></iframe>';

function changeiframesize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  //window.alert( 'Width = ' + myWidth );
  //window.alert( 'Height = ' + myHeight );
var getelemx = document.getElementById('option-iframe');
getelemx.width = myWidth-40;
getelemx.height = myHeight-70;
}
changeiframesize();
</script></body></html>
