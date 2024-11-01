<?php

/* upgrade */
$need_upgrade = false;
$need_upgrade_pop = false;
$need_upgrade_fixedpos = false;
$wpthemedemobar_version = get_option('wpthemedemobar_version');
if (!$wpthemedemobar_version || $wpthemedemobar_version == ''){ $wpthemedemobar_version = 100; }
$wpthemedemobar_version=(int)$wpthemedemobar_version;
if (strlen($wpthemedemobar_version)==2) { $wpthemedemobar_version *= 10; }

//determine which upgrade are needed
if ($wpthemedemobar_version < 150 ) {
  $need_upgrade_pop = true;  $need_upgrade = true;  
}
if ($wpthemedemobar_version < 151 ) {
  $need_upgrade_fixedpos = true;  $need_upgrade = true;
}


if (isset($_POST['wptdb_form_upgrade'])) {
  if ($need_upgrade_pop) {
    if (is_file(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_pop.php')) {
      include(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_pop.php');
    }
  }
  if ($need_upgrade_fixedpos) {
    if (is_file(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_fixedpos.php')) {
      include(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_fixedpos.php');
    }
  }
  if ($need_upgrade) {
    if (is_file(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_complete.php')) {
      include(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/upgrade_complete.php');
    }
  }
}

?>