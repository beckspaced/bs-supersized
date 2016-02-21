<?php

if (!defined ('ABSPATH')) die ('No direct access allowed');

$bs_Supersized_gallery_settings_key = "bs_supersized_options_gallery"; // ID / key gallery options
$bs_Supersized_directory_settings_key = "bs_supersized_options_directory"; // ID / key directory options
$bs_Supersized_medialibrary_settings_key = "bs_supersized_options_medialibrary"; // ID / key directory options
$bs_Supersized_plugin_options_key = "supersized-settings"; // ID / key for plugin options page


// gallery default settings 
$bs_Supersized_gallery_settings_default = array(
    'gallery_id' => '10',
    'gallery_enabled' => 'false'
);

// directory default settings
$bs_Supersized_directory_settings_default = array(
    'directory_path' => get_stylesheet_directory(),
    'directory_enabled' => 'false'
);

// directory default settings
$bs_Supersized_medialibrary_settings_default = array(
    'medialibrary_pattern' => 'supersized',
    'medialibrary_enabled' => 'false'
);

//$bsSupersizedGalleryID = 1;

$bsSupersizedClassMembers = array(
		"gallery_id" => $bs_Supersized_gallery_settings_default['gallery_id'],
        "gallery_settings_key" => $bs_Supersized_gallery_settings_key,
        "directory_settings_key" => $bs_Supersized_directory_settings_key,
        "medialibrary_settings_key" => $bs_Supersized_medialibrary_settings_key,
        "plugin_options_key" => $bs_Supersized_plugin_options_key,
        "gallery_settings_default" => $bs_Supersized_gallery_settings_default,
        "directory_settings_default" => $bs_Supersized_directory_settings_default,
        "medialibrary_settings_default" => $bs_Supersized_medialibrary_settings_default
		);

?>