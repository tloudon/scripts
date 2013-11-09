<?php
/**
 * Description: Outputs a CSV of active Blocks with caching info
 * 
 * Usage: Optionally define THEME and run via drush for a full drupal 
 * bootstrap, eg in site root drush scr list_blocks.php > blocks.csv
 *
 * CSV format
 *   BLOCK: BID - Info, CACHE: Cache
 */

$theme = variable_get('theme_default','none');

$csv = array();
$i = 0;

$blocks = _block_rehash($theme);
foreach ($blocks as $block) {

  // Flag blocks w/o regions for easier sorting
  if (-1 == $block['region']) {
    $csv[$i][0] = "NO REGION BLOCK: {$block['bid']} - {$block['info']}";
  }
  else {
    $csv[$i][0] = "BLOCK: {$block['bid']} - {$block['info']}";
  }

  // Push cache info
  $cache = (-1 == $block['cache'])? 'None': $block['cache'];
  $csv[$i][1] = "CACHE: $cache";

  $i++;
}

ksort($csv); // does this actually improve CSV readability??

// send to stdout in csv format
$fp = fopen("php://output", 'w');
$dateline = array('Generated: ' . date("Ymd") . " for the $theme theme.");

$header = array('Block', 'Cache');

fputcsv($fp, $dateline);
fputcsv($fp, $header);
foreach ($csv as $fields) {
  fputcsv($fp, $fields);
}
fclose($fp);
