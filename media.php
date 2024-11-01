<?php
define('WP_ADMIN', true);

require_once('../../../wp-load.php');
require_once('functions.php');

// Pre 2.6 compatibility (BY Stephen Rider)
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	if ( defined( 'WP_SITEURL' ) ) define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
	else define( 'WP_CONTENT_URL', get_option( 'url' ) . '/wp-content' );
}
 
// REPLACE ADMIN URL
if (function_exists('admin_url')) {
	wp_admin_css_color('classic', __('Blue'), admin_url("css/colors-classic.css"), array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), admin_url("css/colors-fresh.css"), array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
} else {
	wp_admin_css_color('classic', __('Blue'), get_bloginfo('wpurl').'/wp-admin/css/colors-classic.css', array('#073447', '#21759B', '#EAF3FA', '#BBD8E7'));
	wp_admin_css_color('fresh', __('Gray'), get_bloginfo('wpurl').'/wp-admin/css/colors-fresh.css', array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
}

wp_enqueue_script( 'common' );
wp_enqueue_script( 'jquery-color' );
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
wp_enqueue_style( 'media' );

$zv_wptdb_plugin_dir = WP_CONTENT_URL.'/plugins/wordpress-theme-demo-bar/';
$zv_wptdb_siteurl = get_option('siteurl');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; Wordpress Theme Demo Bar &#8212; <?php _e('WordPress'); ?></title>
	
<script type="text/javascript" src="<?php echo $zv_wptdb_plugin_dir; ?>js/static.js"></script>

<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
?>

<link rel="stylesheet" href="<?php echo $zv_wptdb_plugin_dir; ?>css/style.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $zv_wptdb_plugin_dir; ?>css/style_js.css" type="text/css" />

</head>
<!-- start output -->
<body style="padding:10px">

<?php
$wpthemedemobar_formats = get_option('wpthemedemobar_formats');
$format_select_string = '
<option value="">[Use default format]</option>
<optgroup label="User-created Formats">
';
if (!empty($wpthemedemobar_formats) && $wpthemedemobar_formats) {
  foreach ($wpthemedemobar_formats as $formatid => $format) {
    if ($format['format'] != '') {
    $format_select_string .= '<option value="'.$formatid.'">'.htmlspecialchars(stripslashes($format['name'])).'</option>';
    $formatsdb[$formatid] = stripslashes($format['format']);
    }
  }
}
$format_select_string .= '</optgroup>
<optgroup label="Other Actions">
<option value="[new]">[Create a new format]</option>
<option value="[single]">[I want to display a single info only.]</option>
</optgroup>';
?>


<?php
if (isset($_POST['wptdb_form_insert'])) {
$wptdb_error = false;
$wptdb_addhtml = '';

    if ($_POST['wptdb_theme'] != '') {
      if (file_exists(get_theme_root() . "/".$_POST['wptdb_theme'])) {
        $wptdb_addhtml .= '[demobar theme="'.$_POST['wptdb_theme'].'"';
        
        if ($_POST['wptdb_format']!='') {
          if ($_POST['wptdb_format']=='[new]') {
            if ($_POST['wptdb_formattags'] == '') {
              $wptdb_error = true;
              $wptdb_errormsg = 'Error. The format cannot be empty.';
            } else {//format tags not empty
              if (empty($wpthemedemobar_formats)) {
                $wpthemedemobar_formats[1] = array('name'=>$_POST['wptdb_formatname'],'format'=>$_POST['wptdb_formattags']);
                $formatidused = 1;
              } else {
                ksort($wpthemedemobar_formats);
                $allkeys = array_keys($wpthemedemobar_formats);
                $countkeys = count($allkeys);
                $getlastkey = $allkeys[($countkeys-1)];
                $wpthemedemobar_formats[($getlastkey+1)] = array('name'=>$_POST['wptdb_formatname'],'format'=>$_POST['wptdb_formattags']);
                $formatidused = $getlastkey+1;
              }
                //update data
                update_option('wpthemedemobar_formats',$wpthemedemobar_formats);
                $wptdb_addhtml .= ' format="'.$formatidused.'"';
            }
            
          } elseif ($_POST['wptdb_format']=='[single]') {
            $wptdb_addhtml .= ' get="'.$_POST['wptdb_single'].'"';
          } else {
            if (isset($wpthemedemobar_formats[$_POST['wptdb_format']])) {
              $wptdb_addhtml .= ' format="'.$_POST['wptdb_format'].'"';
            }
          }
        }
        
        if (!isset($_POST['wptdb_autop'])) {
          $wptdb_addhtml .= ' autop="false"';
        }
        
        if (!$wptdb_error) {
          echo '<script type="text/javascript">
					/* <![CDATA[ */
					function wptdb_js_addtopost() {
					var win = window.dialogArguments || opener || parent || top;
					win.send_to_editor(\''.$wptdb_addhtml.']\');
					}
					/* ]]> */
				  </script>';
          //if (!isset($_POST['wptdb_form_letmesee'])) {
            echo '<script type="text/javascript">
					  /* <![CDATA[ */
            wptdb_js_addtopost();
            /* ]]> */
            </script>';
				  //} else {
            
          //}
				}
        
      } else {
        $wptdb_error = true;
        $wptdb_errormsg = 'Error. The theme does not exist.';
      }
    } else {
      $wptdb_error = true;
      $wptdb_errormsg = 'Error. No theme was selected.';
    }

}
?>


<?php
if (!isset($_POST['wptdb_form_insert']) || $wptdb_error) {

if ($wptdb_errormsg != '') {
 echo '<div class="wptdb_css_notice">'.$wptdb_errormsg.'</div>';
}

?>

<div class="wptdb_css_oneblock" style="display:block">
<form method="post" action="">

<div class="wptdb_css_optionblock">
<table><tr><td width="100" style="font-weight:bold">Theme</td><td>
<select name="wptdb_theme">
<?php
  $themes = get_themes();
  foreach ($themes as $theme_single) {
    if ($theme_single['Parent Theme'] != '') {//child theme
      $themedir = $theme_single['Stylesheet'];
    } else {
      $themedir = $theme_single['Template'];
    }
  
    echo '<option value="'.$themedir.'">'.htmlspecialchars(stripslashes($theme_single['Name'])).'</option>';
  }
?>
</select><br />
</td></tr></table>
</div>

<div class="wptdb_css_optionblock">
<table><tr><td width="100" style="font-weight:bold">Output Format</td><td>
<select name="wptdb_format" onchange="formatonchange(this.options[selectedIndex].value,'2')">
<?php echo $format_select_string; ?></select><div class="wptdb_format_tags_div" id="format_tags_div"></div>
</td></tr></table>
</div>

<script type="text/javascript">
  function formatonchange(value,extradivid) {
  if (extradivid == '' || !extradivid || extradivid == null) {
  extradivid = '';
  }
  
    if (value == '') {
     hideunuseddivs(extradivid);
     return;
    } else {
      if (value == '[new]') {
        hideunuseddivs(extradivid);
        document.getElementById("addnewformatdiv"+extradivid).style.display = 'block'
      } else if (value == '[single]') {
        hideunuseddivs(extradivid);
        document.getElementById("singlecountdiv"+extradivid).style.display = 'block'
      } else if (value == '') {
        hideunuseddivs(extradivid);
        return;
      } else {
        hideunuseddivs(extradivid);
        showformattags(value);
        return;
      }
    }
  }
  
function hideunuseddivs(extradivid) {
  if (extradivid == '' || !extradivid || extradivid == null) {
  extradivid = '';
  }
document.getElementById("singlecountdiv"+extradivid).style.display = 'none'
document.getElementById("addnewformatdiv"+extradivid).style.display = 'none'
document.getElementById('format_tags_div').innerHTML = '';
document.getElementById('format_tags_div').style.display = 'none'
}

function showformattags(fid) {
var formats_array = new Array();
<?php
  if (is_array($formatsdb) && count($formatsdb)>0) {
    foreach ($formatsdb as $formatid => $formattags) {
      echo 'formats_array['.$formatid.'] = \''.htmlspecialchars(wptdb_addslashtoquote($formattags)).'\';'."\n";
    }
  }
?>
//if (typeof(formats_array[fid])!='undefined') {
  document.getElementById('format_tags_div').style.display = 'block'
  document.getElementById('format_tags_div').innerHTML = formats_array[fid];
//}

}

</script>

<div style="display:none" class="wptdb_css_optionblock" id="singlecountdiv2">
<table><tr><td width="100" style="font-weight:bold">Display Single Info</td><td>
<select name="wptdb_single">

<optgroup label="Info Based On style.css">
<option value="name">Theme Name</option>
<option value="desc">Description of the theme</option>
<option value="themeurl">Theme URI </option>
<option value="version">Version number</option>
<option value="author">Author name</option>
</optgroup>
<optgroup label="Info From Plugin">
<option value="demo">URL to preview the theme (?themedemo=xxx)</option>
<option value="hits">Total no. of previews</option>
<option value="infopage">URL to the theme's info page (if exist)</option>
<option value="downloadpage">URL to the theme's download page (if exist)</option>
<option value="buypage">URL to the page to buy the theme (if exist)</option>
</optgroup>
<optgroup label="Misc.">
<option value="folder">Folder name of the theme</option>
<option value="screenshot">URL to theme's screenshot file (if exist)</option>
</optgroup>
</select><br />
<small>Please note that only numbers will be displayed.</small>
</td></tr></table>
</div>


<div style="display:none" class="wptdb_css_optionblock" id="addnewformatdiv2">
<table><tr><td width="100" style="font-weight:bold">Add New Format</td><td>
  <table><tr>
  <td width="60">Name</td><td><input type="text" name="wptdb_formatname" style="border:1px solid #cccccc;padding:2px" size="60" value="" /></td></tr>
  <td>Format</td><td><input type="text" name="wptdb_formattags" id="wptdb_formattags" style="border:1px solid #cccccc;padding:2px" size="60" value="" /></td></tr>
  </table>
<small>You might need the <a href="<?php echo $zv_wptdb_plugin_dir; ?>listoftags.html" target="_blank">list of tags</a>.</small>
</td></tr></table>
</div>

<div class="wptdb_css_optionblock">
<table><tr><td width="100" style="font-weight:bold">Auto P?</td><td>
<input type="checkbox" name="wptdb_autop" checked="checked" value="1" /><br />
<small>Tick the checkbox if you want to wrap the content with HTML paragraph &lt;p> tag.</small>
</td></tr></table>
</div>

<p><input type="submit" class="button-primary" name="wptdb_form_insert" value="Insert into post" /> 
<!--input type="submit" class="button-primary wptdb_css_button" style="" name="wptdb_form_letmesee" value="Let me see the output first" /-->
<br />
</form></div>


<?php
}
?>



<br /><br />
<hr style="border:0px;height:1px;font-size:1px;background:#cccccc;color:#cccccc" />
<small>
&raquo; <a target="_blank" href="<?php echo $zv_wptdb_siteurl; ?>/wp-admin/options-general.php?page=wordpress-theme-demo-bar/wp_theme_demo_bar.php">Visit the plugin option page</a>
</small>
</body></html>