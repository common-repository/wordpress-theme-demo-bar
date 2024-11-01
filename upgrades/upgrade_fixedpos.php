<?php

$wpthemedemobar_options = get_option('wpthemedemobar_options');

if (!empty($wpthemedemobar_options)) { 
  if (in_array('ontop',$wpthemedemobar_options['hide'])) {
  
    $wpthemedemobar_misc = get_option('wpthemedemobar_misc');
    $wpthemedemobar_misc['demobar_fixedpos'] = 1;

    update_option('wpthemedemobar_misc',$wpthemedemobar_misc);
  } 
}

$updatedversion = 151;

?>