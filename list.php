<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * This portation of code is taken from nextgen-gallery plugin
 */
$path  = ''; // It should be end with a trailing slash

/** That's all, stop editing from here **/

if ( !defined('WP_LOAD_PATH') ) {

	/** classic root path if wp-content and plugins is below wp-config.php */
	$classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;

	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		if (file_exists( $path . 'wp-load.php') )
			define( 'WP_LOAD_PATH', $path);
		else
			exit("Could not find wp-load.php");
}

// let's load WordPress
require_once( WP_LOAD_PATH . 'wp-load.php');

global $wpdb, $hitcount_table_name;


 $hitslist = $wpdb->get_results("SELECT * FROM $hitcount_table_name");
  echo '<div class="wrap">';
  foreach($hitslist as $hits){
      echo "<br/>";
      echo " Hits: ".$hits->hits;
      $post = get_post($hits->pid);
      echo $post->post_title;
      echo "<pre>";
      print_r($post);
      echo "</pre>";
      //echo link
  }

  //echo '<p>Here is where the form would go if I actually had options.</p>';
  echo '</div>';

?>
