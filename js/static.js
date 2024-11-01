function wptdb_jsfunc_toggleformatform(formatid) {
var getelemx = document.getElementById("wptdb_formatdiv_editname_"+formatid);
  if (getelemx.style.display != "block") {
    getelemx.style.display = "block";
    document.getElementById("wptdb_formatdiv_editformat_"+formatid).style.display = "block";
    document.getElementById("wptdb_formatdiv_name_"+formatid).style.display = "none";
    document.getElementById("wptdb_formatdiv_format_"+formatid).style.display = "none";
  } else {
    getelemx.style.display = "none";
    document.getElementById("wptdb_formatdiv_editformat_"+formatid).style.display = "none";
    document.getElementById("wptdb_formatdiv_name_"+formatid).style.display = "block";
    document.getElementById("wptdb_formatdiv_format_"+formatid).style.display = "block";
  }
}

function wptdb_toggle(elemid,hidethistoo) {
var getelemx = document.getElementById(elemid);
  if (getelemx.style.display != "block") {
    getelemx.style.display = "block";
      if (hidethistoo != null) {
        document.getElementById(hidethistoo).style.display = "none";
      }
  } else {
    getelemx.style.display = "none";
  }
}


function wptdb_jsfunc_toggleform(name) {
var getelemx = document.getElementById("indiv_settings_form_info_"+name);
  if (getelemx.style.display != "block") {
    getelemx.style.display = "block";
    document.getElementById("indiv_settings_form_download_"+name).style.display = "block";
    document.getElementById("indiv_settings_form_downloadorbuy_"+name).style.display = "inline";
    document.getElementById("indiv_settings_original_download_"+name).style.display = "none";
    document.getElementById("indiv_settings_original_info_"+name).style.display = "none";
    document.getElementById("indiv_settings_original_downloadorbuy_"+name).style.display = "none";
    document.getElementById("indiv_settings_submitbutton_"+name).style.display = "block";
  } else {
    getelemx.style.display = "none";
    document.getElementById("indiv_settings_form_download_"+name).style.display = "none";
    document.getElementById("indiv_settings_form_downloadorbuy_"+name).style.display = "none";
    document.getElementById("indiv_settings_original_download_"+name).style.display = "block";
    document.getElementById("indiv_settings_original_info_"+name).style.display = "block";
    document.getElementById("indiv_settings_original_downloadorbuy_"+name).style.display = "inline";
    document.getElementById("indiv_settings_submitbutton_"+name).style.display = "none";
  }
}