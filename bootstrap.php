<?php

/**
 * @file
 * Baseline example fastest* possible authenticated page:
 * no menu/routing
 * no theming
 *
 * * Really you can't serve a production page this fast.  Unless you skip a 
 * full Drupal bootstrap, which is what eg, page caching  or authcache do.  
 * It's a good proxy though, helps set expectations, and measures a full Drupal 
 * bootstrap.
 *
 */

/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());

include_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

global $user;

print "Hello {$user->name}";

// Or if you would like a test that can do a nice side-by-side w/ anon users
// print "Hello user uid {$user->uid}";
