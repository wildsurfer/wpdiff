<?php

ini_set('memory_limit', '256M');
set_time_limit(3600);

$library_path = realpath(dirname(__FILE__)).'/../library';
set_include_path(get_include_path() . PATH_SEPARATOR . $library_path);

require_once('WPDIFF/Wordpress/Original.php');
require_once('WPDIFF/Wordpress/Probationer.php');
require_once('WPDIFF/Differer.php');

// Creating local wordpress object
$wp_local = new WPDIFF_Wordpress_Probationer('/home/kuzma/workspace/tmp/data/32/home/www/dev12.artmyweb.com/docroot');

// Creating original wordpress object that will be fetched from wordpress.org website.
// Changing this line to your WP directory is enough to see how it works
$wp_orig = new WPDIFF_Wordpress_Original($wp_local->getVersion());

// Disabling themes and uploads directories
$wp_local->disableThemes();
$wp_local->disableUploads();

$wp_orig->disableThemes();
$wp_orig->disableUploads();

$differer = new WPDIFF_Differer($wp_local,$wp_orig);

$version_diff = $differer->versionDiff();
$version_diff_template = '';
if ($version_diff) {
    $version_diff_template .= "<p><strong>Left version:</strong> {$version_diff['left']}</p>";
    $version_diff_template .= "<p><strong>Right version:</strong> {$version_diff['right']}</p>";
} else {
    $version_diff_template .= '<p>No version differences detected. Current version is '.$wp_local->getVersion();
}

$files_diff = $differer->filesDiff();
$files_diff_template = '<div>';
if ($files_diff) {
    $files_diff_template .= '<p><strong>Total</strong>: '.count($files_diff).'</p>';
    foreach ($files_diff as $file => $html) {
        $files_diff_template .= "<strong>{$file}</strong><br />{$html}<br />";
    }
}
$files_diff_template .= '</div>';

$files_only_in_left = $differer->filesOnlyInLeft();
$files_only_in_left_template = '<ul>';
if ($files_only_in_left) {
    foreach ($files_only_in_left as $file) {
        $files_only_in_left_template .= "<li>{$file}</li>";
    }
}
$files_only_in_left_template .= '</ul>';

$files_only_in_right = $differer->filesOnlyInRight();
$files_only_in_right_template = '<ul>';
if ($files_only_in_right) {
    foreach ($files_only_in_right as $file) {
        $files_only_in_right_template .= "<li>{$file}</li>";
    }
}
$files_only_in_right_template .= '</ul>';

$main_template = <<<HTML
<html>
    <head>
        <title>WPDIFF</title>
        <link rel="stylesheet" href="../library/php-diff/example/styles.css" type="text/css" charset="utf-8"/>
    </head>
    <body>
        <h1>Difference in versions</h1>
        {$version_diff_template}
        <h1>Difference in files</h1>
        {$files_diff_template}
        <h1>Files only in left</h1>
        {$files_only_in_left_template}
        <h1>Files only in right</h1>
        {$files_only_in_right_template}
    </body>
</html>
HTML;

echo($main_template);
