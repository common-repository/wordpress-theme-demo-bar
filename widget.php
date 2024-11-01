<?php

function wpthemedemobar_widget($args) {
$zv_wptdb_siteurl = get_option('siteurl');
$wpthemedemobar_widget_options = get_option('wpthemedemobar_widget_options');
$wpthemedemobar_options = get_option('wpthemedemobar_options');

if (empty($wpthemedemobar_widget_options)) { return; }

$selectmenusettings = null;
if (!empty($wpthemedemobar_widget_options)) {
  if ($wpthemedemobar_widget_options['parentchild_rel'] == 0) {
    $selectmenusettings['hierarchical'] = 0;
  }
  if ($wpthemedemobar_widget_options['sort'] == 'hits') {
    $selectmenusettings['orderby'] = 'hits';
  }
  if ($wpthemedemobar_widget_options['order'] == 'desc') {
    $selectmenusettings['order'] = 'desc';
  }
  if ($wpthemedemobar_widget_options['show_hits'] == 1) {
    $selectmenusettings['show_hits'] = 1;
  }
}

$themes = get_themes();
$optionstr = wptdb_return_themejump_options($themes,$wpthemedemobar_options,'',$selectmenusettings);

/* start output */
echo $args['before_widget'];
echo $args['before_title'].$wpthemedemobar_widget_options['title'].$args['after_title'];
if ($wpthemedemobar_widget_options['center'] == 1) {echo '<center>';}
echo '<select name="demobar-themes-dropdown" class="demobar-themes-dropdown" onchange="if (this.options[this.selectedIndex].value!=\'\') { document.location.href=\'?themedemo=\'+this.options[this.selectedIndex].value; }"><option value="">Select Theme</option>'."\n";
echo $optionstr;
echo '</select>';
if ($wpthemedemobar_widget_options['center'] == 1) {echo '</center>';}
echo $args['after_widget'];
}

function wpthemedemobar_widget_control() {

if (isset($_POST['wptdb_widget_submit'])) {
$widgetoptions['title'] = $_POST['wptdb_widget_title'];
$widgetoptions['sort'] = $_POST['wptdb_widget_sort'];
$widgetoptions['order'] = $_POST['wptdb_widget_order'];
$widgetoptions['parentchild_rel'] = $_POST['wptdb_widget_parentchild_rel'];
$widgetoptions['show_hits'] = $_POST['wptdb_widget_show_hits'];
$widgetoptions['center'] = $_POST['wptdb_widget_center'];
update_option('wpthemedemobar_widget_options',$widgetoptions);
}

$wpthemedemobar_widget_options = get_option('wpthemedemobar_widget_options');

?>
Widget Title:<br />
<input type="text" class="widefat" name="wptdb_widget_title" value="<?php if (!empty($wpthemedemobar_widget_options)) { echo $wpthemedemobar_widget_options['title']; } ?>" />
<br /><br />

Sort the themes by:<br />
<select name="wptdb_widget_sort" class="widefat">
<option value="default">Wordpress default</option>
<option value="hits" <?php if (!empty($wpthemedemobar_widget_options)) { if ($wpthemedemobar_widget_options['sort']=='hits') { echo 'selected="selected"'; } } ?>>Num. of previews</option>
</select>
<br />

Order:<br />
<select name="wptdb_widget_order" class="widefat">
<option value="asc">Ascending</option>
<option value="desc" <?php if (!empty($wpthemedemobar_widget_options)) { if ($wpthemedemobar_widget_options['order']=='desc') { echo 'selected="selected"'; } } ?>>Descending</option>
</select>
<br />

Show parent-child relationship? <input type="checkbox" name="wptdb_widget_parentchild_rel" value="1" <?php if (!empty($wpthemedemobar_widget_options)) { if ($wpthemedemobar_widget_options['parentchild_rel']=='1') { echo 'checked="checked"'; } } else { echo 'checked="checked"'; }?> />
<br />

Show number of previews? <input type="checkbox" name="wptdb_widget_show_hits" value="1" <?php if (!empty($wpthemedemobar_widget_options)) { if ($wpthemedemobar_widget_options['show_hits']=='1') { echo 'checked="checked"'; } } ?> />
<br />

Wrap with &lt;center> tag? <input type="checkbox" name="wptdb_widget_center" value="1" <?php if (!empty($wpthemedemobar_widget_options)) { if ($wpthemedemobar_widget_options['center']=='1') { echo 'checked="checked"'; } } ?> />
<br />

<input type="hidden" name="wptdb_widget_submit" value="1" />
<br /><br />
<?php
}

function wpthemedemobar_widget_init() {
wp_register_sidebar_widget('wpthemedemobar_widget', __('Themes Drop-down Menu'), 'wpthemedemobar_widget', array('classname' => 'wpthemedemobar_widget', 'description' => __('A drop-down menu for users to preview themes using Wordpress Theme Demo Bar plugin'))); 
register_widget_control('wpthemedemobar_widget', 'wpthemedemobar_widget_control');
}
add_action("plugins_loaded", "wpthemedemobar_widget_init");

?>