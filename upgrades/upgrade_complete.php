<?php

update_option('wpthemedemobar_version',$updatedversion);

//if (is_file(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/main.php')) {
//@unlink(ABSPATH.'wp-content/plugins/wordpress-theme-demo-bar/upgrades/main.php');
//}

echo '<div class="updated" style="padding:5px;">
<b>Success ! Have a nice day. <br />
<a href="'.$zv_wptdb_siteurl.'/wp-admin/options-general.php?page=wordpress-theme-demo-bar/wp_theme_demo_bar.php">Refresh this page</a>
</b></div>';



die();
?>