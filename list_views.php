<?php
/**
 * Description: Outputs a CSV of active Views and Displays with caching info
 * 
 * Usage: run via drush for a full drupal bootstrap, eg in site root drush scr list_views.php > views.csv
 *
 * CSV format
 *   VIEW: VID - View Name (in GUI)*, DISPLAY: Display ID - Display Name (defaults to Page|Block|etc) - Title (helpful if default Display Name), CACHE: Cache
 *   * of course unless there is no VID, then we we use the View Name
 */

// TODO clean up code

$csv = array();
$i = 0;

// loop through all of the Views
$views = views_get_all_views();
foreach ($views as $view) {

  // check for Views from code
  $view_id = (empty($view->vid))? "VIEW: " . $view->name: $view->vid . " - " . $view->human_name;

  // skip disabled Views
  if ($view->disabled) {
    $line = "VIEW DISABLED: $view_id";
    $csv[$i][0] = $line;
    $i++;
    continue;
  } 
  $line = "VIEW: $view_id";


  $view->init_display();   // follow Views internal practices, does some checks on default display

  // necessary??
  if (0 == count($view->display)) {
    $csv[$i][0] = $line . "ZERO DISPLAYS";
    $i++;
    continue;
  } 

  // loop through each Display to grab the name and cache
  foreach ($view->display as $display) {
    $csv[$i][] = $line;
    $csv[$i][] = "DISPLAY: " . $display->id . " - " . $display->display_title . " - " . $display->display_options['title'];

    // TODO check on the empty cache element--be sure it can't default to something other than "none"
    if (!isset($display->display_options['cache']) || "none" == $display->display_options['cache']['type']) {
      $cache = "None";
    }
    else if ("PHP" == $display->display_options['cache']['type']) {
      $cache = "PHP";
    }
    else {
      // TODO add support for custom
      $cache = "Time - Query {$display->display_options['cache']['results_lifespan']} / Output {$display->display_options['cache']['output_lifespan']}";
    }
    $csv[$i][] = "CACHE: $cache";

    $i++;
  }
}

ksort($csv); // does this actually improve CSV readability??

// send to stdout in csv format
$fp = fopen("php://output", 'w');
$dateline = array('Generated: ' . date("Ymd"));
$header = array('View', 'Display', 'Cache');
fputcsv($fp, $dateline);
fputcsv($fp, $header);
foreach ($csv as $fields) {
  fputcsv($fp, $fields);
}
fclose($fp);
