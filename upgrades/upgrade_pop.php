<?php
$wpthemedemobar_pop = get_option('wpthemedemobar_pop');
if (!empty($wpthemedemobar_pop)) {
$newarray = array();
$themes = get_themes();
  foreach ($themes as $themesingle) {
    $oldcookiename = wptdb_make_validcookiename_old($themesingle['Stylesheet']);
    $newcookiename = wptdb_make_validcookiename($themesingle['Stylesheet']);
    if (isset($wpthemedemobar_pop[$oldcookiename])) {
      $newarray[$newcookiename] = $wpthemedemobar_pop[$oldcookiename];
      unset($wpthemedemobar_pop[$oldcookiename]);
    }
  }
update_option('wpthemedemobar_pop',$newarray);
}

$updatedversion = 150;

?>