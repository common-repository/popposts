<?
/*
Plugin Name: PopPosts
Plugin URI: http://www.nicblue.com/plugins/popposts
Description: A simple plugin to show the most popular posts of your blog
Author: Murat Dikici
Version: 0.0.1
Author URI: http://www.nicblue.com
*/

global $wpdb, $hitcount_table_name;
if (!function_exists("get_option")) {hitcount_readme();die;}

// Edit this line if you want to use a different MySQL table name:
$hitcount_table_name = $wpdb->prefix . "hitcount";

// Increments the database by one and returns the total number of hits to date.

function hitcount_increasehit() {
	global $wpdb, $hitcount_table_name, $post;
	$pid = $post->ID;
	$ptype = $post->post_type;

		if( ($wpdb->get_var("SELECT hits from $hitcount_table_name WHERE `pid` =  '$pid' and `type`='$ptype'"))){
			$wpdb->query("UPDATE $hitcount_table_name SET hits = hits + 1 WHERE pid = '$pid' and `type`='$ptype'");
		}
		else {
			$wpdb->query("insert into $hitcount_table_name (`type`,`pid`,`hits`) values('$ptype',$pid,'1')");
		}
			


}


function hitcount_gethits(){
    global $wpdb,$post,$hitcount_table_name;
    $pid = $post->ID;
    $ptype = $post->post_type;
    return $wpdb->get_var("SELECT hits FROM $hitcount_table_name WHERE pid = $pid and `type` =  '$ptype'");
}

// Prints an error message.
function hitcount_readme() {
	echo '<br><strong>Something is wrong!</strong><br>';
}

function displayhitcount($content){
  global $post;
  if(is_single() || is_page()){
    hitcount_increasehit();
    $hits = hitcount_gethits();
    if($hits==null or empty($hits)) $hits = 0;
    $content = $content."<br /><b>This post has been viewed ".$hits." times.</b><br /><br />";
  }
  return $content;
}


// Installs the plugin.
function hitcount_install() {
	global $wpdb, $hitcount_table_name;
	if ($wpdb->get_var("SHOW TABLES LIKE '$hitcount_table_name'") != $hitcount_table_name) {
		$wpdb->query("CREATE TABLE ".$hitcount_table_name." (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`type` ENUM( 'post', 'page' ) NOT NULL DEFAULT 'post',`pid` INT NOT NULL ,`hits` INT( 11 ) NOT NULL,`create_at` DATETIME NULL ,`last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) TYPE = MYISAM ;");
		add_option("hitcount_db_version", "1.0");
	}
}

add_filter('the_content', 'displayhitcount');

register_activation_hook(__FILE__, "hitcount_install");

/*add_action('admin_menu', 'hitcount_menu');

function hitcount_menu() {
  add_options_page('My Plugin Options', 'Number of View', 8, 'your-unique-identifier', 'hitcount_options');
  add_dashboard_page("number of view details", "Number of View", 8, "./../wp-content/plugins/number-of-view/list.php");
}

function hitcount_options() {
  global $wpdb, $hitcount_table_name;


  $hitslist = $wpdb->get_results("SELECT * FROM $hitcount_table_name");
  echo '<div class="wrap">';
  foreach($hitslist as $hits){
      echo "<br/>";
      echo "Post Id: ".$hits->pid." Hits: ".$hits->hits;
      $post = get_post($hits->pid);
      echo $post->post_title;
  }

  //echo '<p>Here is where the form would go if I actually had options.</p>';
  echo '</div>';
}
 * 
 */

function widget_hitcount_popularpost($args) {
    global $wpdb, $hitcount_table_name;
    $options = get_option("widget_hitcount_popularpost");
  extract($args);
  echo $before_widget;
  echo $before_title.$options['title'].$after_title;
  $hitslist = $wpdb->get_results("SELECT * FROM $hitcount_table_name WHERE type in ('post','page') ORDER BY hits desc limit ".$options['numpost']);
  echo '<div class="wrap">';
  echo "<ul>";
  foreach($hitslist as $hits){
      if ($ccc>0) echo "<br/>";
      //echo "Post Id: ".$hits->pid." Hits: ".$hits->hits;
      $post = get_post($hits->pid);
      $posttitle=$post->post_title;
      if (strlen($posttitle)>27) $posttitle=substr($post->post_title, 0, 27)."...";
      echo "<li><a href='".get_permalink($hits->pid)."' title='".$post->post_title."'>".$posttitle. '</a> ('. $hits->hits. ')</li>';
  }
  echo "</ul>";

  //echo '<p>Here is where the form would go if I actually had options.</p>';
  echo '</div>';
  echo $after_widget;
}

function widget_hitcount_popularpost_control()
{
  $options = get_option("widget_hitcount_popularpost");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'PopPosts',
      "numpost" => 5
      );
  }    

  if ($_POST['widget_hitcount_popularpost-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['widget_hitcount_popularpost-WidgetTitle']);
    $options['numpost'] = htmlspecialchars($_POST['widget_hitcount_popularpost-Numpost']);
    update_option("widget_hitcount_popularpost", $options);
  }
?>
  <p>
    <label for="widget_hitcount_popularpost-WidgetTitle"><?=_e("Widget Title")?>: </label>
    <input type="text" id="widget_hitcount_popularpost-WidgetTitle" name="widget_hitcount_popularpost-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br/>
  <label for="widget_hitcount_popularpost-Numpost"><?=_e("Number of Posts")?>: </label>
   <input type="text" id="widget_hitcount_popularpost-Numpost" name="widget_hitcount_popularpost-Numpost" value="<?php echo $options['numpost'];?>" />
    <input type="hidden" id="widget_hitcount_popularpost-Submit" name="widget_hitcount_popularpost-Submit" value="1" />
  </p>
<?php
}


function widget_hitcount_popularpost_init()
{
  register_sidebar_widget(__('PopPosts'), 'widget_hitcount_popularpost');
  register_widget_control( __('PopPosts'), 'widget_hitcount_popularpost_control', 300, 200 );
}
add_action("plugins_loaded", "widget_hitcount_popularpost_init");

?>