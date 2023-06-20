<?php
/**
 * Plugin Name:     VS ShortCodes Caching
 * Description:     Creates shortcodes to display custom content using file caching
 * Author:          Ray St. Onge
 * Text Domain:     vs-shortCodes
 * Domain Path:     /languages
 * Version:         1.7.1
 *
 * @package         VS_ShortCodes_Caching
 */

// Your code starts here.
function GetURLVar($key,$default="")
{
	$val = $default;
	if (isset($_GET[$key]))
	{
		$val = trim($_GET[$key]);
	}
	return $val;
}

add_action( 'admin_menu', 'vs_sc_plugin_add_settings_menu' );

function vs_sc_plugin_add_settings_menu() {

    add_options_page( 'VS ShortCode Plugin Settings', 'VS ShortCode Settings', 'manage_options',
        'vs_sc_plugin', 'vs_sc_plugin_option_page' );

}

// Create the option page
function vs_sc_plugin_option_page() {
    ?>
    <div class="wrap">

	    <form action="options.php" method="post">
		    <?php
          settings_fields( 'vs_sc_plugin_options' );
		      do_settings_sections( 'vs_sc_plugin' );
		      submit_button( 'Save Changes', 'primary' );
        ?>
	    </form>
    </div>
    <?php
}

// Register and define the settings
add_action('admin_init', 'vs_sc_plugin_admin_init');

function vs_sc_plugin_admin_init(){

	// Define the setting args
	$args = array(
	    'type' 				=> 'string',
	    'sanitize_callback' => 'vs_sc_plugin_validate_options',
	    'default' 			=> NULL
	);

    // Register our settings
    register_setting( 'vs_sc_plugin_options', 'vs_sc_plugin_options', $args );

    // Add a settings section
    add_settings_section(
    	'vs_sc_plugin_main',
    	'VSE Plugin Settings',
        'vs_sc_plugin_section_text',
        'vs_sc_plugin'
    );

    // Create our settings field for cachingKey
    add_settings_field(
    	'vs_sc_plugin_cachingKey',
    	'CSS Caching Key',
        'vs_sc_plugin_setting_cachingKey',
        'vs_sc_plugin',
        'vs_sc_plugin_main'
    );
		add_settings_field(
			'vs_sc_plugin_debugCaching',
			'Debug CSS Caching',
				'vs_sc_plugin_setting_debugCaching',
				'vs_sc_plugin',
				'vs_sc_plugin_main'
		);
}

// Draw the section header
function vs_sc_plugin_section_text() {

    echo '<p>Enter your settings here.</p>';

}

function vs_sc_plugin_setting_debugCaching()
{
	// Get option 'display_results' value from the database
// Set to 'disabled' as a default if the option does not exist
$options = get_option( 'vs_sc_plugin_options', [ 'debugCaching' => 'disabled' ] );
$debugCaching = $options['debugCaching'];

// Define the radio button options
$items = array( 'enabled', 'disabled' );

foreach( $items as $item ) {

	// Loop the two radio button options and select if set in the option value
	echo "<label><input " . checked( $debugCaching, $item, false ) . " value='" . esc_attr( $item ) . "' name='vs_sc_plugin_options[debugCaching]' type='radio' />" . esc_html( $item ) . "</label><br />";

}
}
// Display and fill the cachingKey text form field
function vs_sc_plugin_setting_cachingKey() {

    // Get option 'text_string' value from the database
    $options = get_option( 'vs_sc_plugin_options' );
    $cachingKey = $options['cachingKey'];

    // Echo the field
    echo "<input id='cachingKey' name='vs_sc_plugin_options[cachingKey]'
        type='text' value='" . esc_attr( $cachingKey ) . "' />";

}

// Validate user input for all three options
function vs_sc_plugin_validate_options( $input ) {


   $valid = $input;

    return $valid;
}
add_shortcode('vsPosts','getVSPosts');
// Use of shortcode [vsPosts type='brief' site='waldo' status='published']
function getVSPosts($attr) {
  //echo "getVSPosts<br>";
  //print_r($attr);
  //echo "<br>";
  $shortCodeArgs = shortcode_atts( array(
          'type' => 'offer',
          'site' => 'all',
          'status' => 'publish',
          'currentUser' => 0,
          'numcols' => 1,
          'pretty' => 0,
          'caching' => 1,
          'numposts' => 5
      ), $attr );
		//echo "getVSPosts<br>";
    $profile_id = um_profile_id();
    $shortCodeArgs['type'] = GetURLVar("type",$shortCodeArgs['type']);
    $shortCodeArgs['site'] = GetURLVar("site",$shortCodeArgs['site']);
    $shortCodeArgs['status'] = GetURLVar("status",$shortCodeArgs['status']);
    $shortCodeArgs['currentUser'] = GetURLVar("currentUser",$shortCodeArgs['currentUser']);
    $shortCodeArgs['numcols'] = GetURLVar("numcols",$shortCodeArgs['numcols']);
    $shortCodeArgs['pretty'] = GetURLVar("pretty",$shortCodeArgs['pretty']);
    $shortCodeArgs['caching'] = GetURLVar("caching",$shortCodeArgs['caching']);
    $shortCodeArgs['numposts'] = GetURLVar("numposts",$shortCodeArgs['numposts']);
	//	print_r ($shortCodeArgs);


    $htmlText = retreiveVSPosts($shortCodeArgs);
    $className = "columns".$shortCodeArgs['numcols'];
		if ($shortCodeArgs['caching'])
		{
			// Get option 'text_string' value from the database
	    $options = get_option( 'vs_sc_plugin_options' );
	    $cachingKey = $options['cachingKey'];
			$debugCaching = $options['debugCaching'];
			if ($debugCaching == "enabled")
			{
				$cachingKey = date("YmdHis");
			}
			$fname = "cache-".$shortCodeArgs['site']."-".$shortCodeArgs['type']."-".$shortCodeArgs['numcols']."-".$shortCodeArgs['numposts'].".html";
			unlink($fname);
			$page = '<!doctype html>
<html lang="en-US">

<head
<meta http-equiv="Cache-control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="Expires: Sat, 26 Jul 1997 05:00:00 GMT">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">';
      $page = $page.'<link rel="stylesheet" id="shortCode_style-css"  href="https://shop.villagesoup.com/wp-content/plugins/vs-shortCodes/css/shortCodes.css?x='.$cachingKey.'" media="all" />';



	    $page = $page.'</head>';

			$page = $page.'<body class="'.$className.'">';
			$page = $page.$htmlText;
			$page = $page.'</body></html>';
			$fc = fopen($fname,"w") or die();
			fwrite($fc,$page);
			fclose($fc);
		}
    return $htmlText;
}
// this function parses the args and then determines what should be called
function retreiveVSPosts($shortCodeArgs){
//  'type' => 'brief',
//  'site' => 'all',
//  'status' => 'published',
//  'currentUser' => 0,
//  'numcols' => 1,
//  'pretty' => 0,
//  'numposts' = 5
  $args = array();
  $profile_id = um_profile_id();
  if ($shortCodeArgs['currentUser'])
  {
    $args = array(
      'post_type'   => $shortCodeArgs['type'],
      'post_status' => $shortCodeArgs['status'],
      'author'     => $profile_id
    );
  } else{
    $args = array(
      'post_type'   => $shortCodeArgs['type'],
      'post_status' => $shortCodeArgs['status']
    );
    $profile_id = 0;
  }
  if ($shortCodeArgs['site'] != "all")
  {
    $args['meta_key'] = 'publish_on_'.$shortCodeArgs['site'].'_'.$shortCodeArgs['type'];
    $args['meta_value'] = 1;
  }
  else{
    $args['site'] = $shortCodeArgs['site'];
  }
  $args['posts_per_page'] = $shortCodeArgs['numposts'];

  if ($shortCodeArgs['numcols'] > 1){
    return retrievePostsCol($args,$shortCodeArgs['numcols'],$shortCodeArgs['numposts']);
  }
  else{
    if ($shortCodeArgs['pretty'])
    {
      retrievePostsPretty($shortCodeArgs['type'],$profile_id);
    }else{
      return getPosts($args,$profile_id);
    }
  }
}


// register jquery and style on initialization
add_action('init', 'register_scriptShortCodes');
function register_scriptShortCodes() {
    wp_register_style( 'shortCode_style', plugins_url('/css/shortCodes.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_styleShortCodes');

function enqueue_styleShortCodes(){
   wp_enqueue_style( 'shortCode_style' );
}

function getMyBriefs() {
    $profile_id = um_profile_id();
    return retrievePosts('brief', um_profile_id());
}
add_shortcode('myBriefs','getMyBriefs');

function getMyOffers() {
    $profile_id = um_profile_id();
    return retrievePosts('offer', um_profile_id());

}
add_shortcode('myOffers','getMyOffers');

function getMyRealEstates() {
    $profile_id = um_profile_id();
    return retrievePosts('realestate', um_profile_id());
}
add_shortcode('myRealEstates','getMyRealEstates');

function getAllBriefs2col() {
    $profile_id = um_profile_id();
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_value' => 1
    );
    return retrievePostsCol($args,2,10);

}
add_shortcode('allBriefs2Col','getAllBriefs2col');

function getAllBriefs() {
    $profile_id = um_profile_id();
    return retrievePosts('brief');
}
add_shortcode('allBriefs','getAllBriefs');

function getAllRealEstatePretty() {
    $profile_id = um_profile_id();
    return retrievePostsPretty('real_estate');
}
add_shortcode('allRealEstatePretty','getAllRealEstatePretty');

function getHomePageRealEstate() {
    $profile_id = um_profile_id();
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_value' => 1
    );
    return retrievePostsCol($args,3,12);

}
add_shortcode('homePageRealEstate','getHomePageRealEstate');

function getAllOffers() {
    $profile_id = um_profile_id();
    return retrievePosts('offer');
}
add_shortcode('allOffers','getAllOffers');

function getAllOffersPretty() {
    $profile_id = um_profile_id();
    return retrievePostsPretty('offer');
}
add_shortcode('allOffersPretty','getAllOffersPretty');

function getAllBriefsPretty() {
    $profile_id = um_profile_id();
    return retrievePostsPretty('brief');
}
add_shortcode('allBriefsPretty','getAllBriefsPretty');


function getAllBriefsKnox() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_knox_brief',
      'meta_value' => 1,
      'caching' => 1

    );
    return getPosts($args);
}
add_shortcode('allBriefsKnox','getAllBriefsKnox');

function getAllBriefsKnox2Col() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_knox_brief',
      'meta_value' => 1,
      'caching' => 1
    );
    return retrievePostsCol($args,2,10);
}
add_shortcode('allBriefsKnox2Col','getAllBriefsKnox2Col');
function getAllBriefsWaldo() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_waldo_brief',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allBriefsWaldo','getAllBriefsWaldo');

function getAllBriefsWaldo2Col() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_waldo_brief',
      'meta_value' => 1
    );
    return retrievePostsCol($args,2,10);
}
add_shortcode('allBriefsWaldo2Col','getAllBriefsWaldo2Col');

function getAllOffersKnox() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_knox_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allOffersKnox','getAllOffersKnox');

function getAllOffersWaldo() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_waldo_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allOffersWaldo','getAllOffersWaldo');

function getAllDiningOffersWaldo() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'cat'  => 136,
      'meta_key' => 'publish_on_waldo_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allDiningOffersWaldo','getAllDiningOffersWaldo');

function getAllNonDiningOffersWaldo() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'category__not_in' => 136,
      'meta_key' => 'publish_on_waldo_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allNonDiningOffersWaldo','getAllNonDiningOffersWaldo');

function getAllDiningOffersKnox() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'cat'  => 136,
      'meta_key' => 'publish_on_knox_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allDiningOffersKnox','getAllDiningOffersKnox');

function getAllNonDiningOffersKnox() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'category__not_in' => 136,
      'meta_key' => 'publish_on_knox_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allNonDiningOffersKnox','getAllNonDiningOffersKnox');

function getAllBriefsIslander() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_islander_brief',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allBriefsIslander','getAllBriefsIslander');

function getAllOffersIslander() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_islander_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allOffersIslander','getAllOffersIslander');

function getAllDiningOffersIslander() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'cat'  => 136,
      'meta_key' => 'publish_on_islander_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allDiningOffersIslander','getAllDiningOffersIslander');

function getAllNonDiningOffersIslander() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'category__not_in' => 136,
      'meta_key' => 'publish_on_islander_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allNonDiningOffersIslander','getAllNonDiningOffersIslander');

function getAllBriefsEA() {
    $args = array(
      'post_type'   => 'brief',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_ea_brief',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allBriefsEA','getAllBriefsEA');

function getAllOffersEA() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'meta_key' => 'publish_on_ea_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allOffersEA','getAllOffersEA');

function getAllDiningOffersEA() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'cat'  => 136,
      'meta_key' => 'publish_on_ea_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allDiningOffersEA','getAllDiningOffersEA');

function getAllNonDiningOffersEA() {
    $args = array(
      'post_type'   => 'offer',
      'post_status' => 'publish',
      'category__not_in' => 136,
      'meta_key' => 'publish_on_ea_offer',
      'meta_value' => 1
    );
    return getPosts($args);
}
add_shortcode('allNonDiningOffersEA','getAllNonDiningOffersEA');



## retrieves posts based on paramaters given.
# @param1 = post type (brief, offer, realestate)
# @param2 = user id number (use um_profile_id() to get current profile being viewed. pass nothing for all posts)
function retrievePosts($type, $uid = 0){

  $args = array();
  if ($uid){
    $args = array(
      'post_type'   => $type,
      'post_status' => 'publish',
      'author'     => $uid
    );
  } else {
    $args = array(
      'post_type'   => $type,
      'post_status' => 'publish'
    );
  }
  return retrievePostsPretty($type,$uid);
  //return getPosts($args,$uid);
}


function getPosts($args,$uid = 0){
  ob_start();
  if (!isset($args['caching']))
  {
    $args['caching'] =1;
  }
  $htmlText = '';
  $filename = $args['meta_key'].'.html';
  if ($args['caching'])
	{
		$fTime = filemtime($filename);
		$fTimeStr = date("Y-m-d H:i:s",$fTime);
    $timeInSeconds = 5 * 60;
    $cacheDelta = $timeInSeconds + rand(0,$timeInSeconds/2);
    $cacheTime = $fTime + $cacheDelta;
    $cacheTimeStr = date("Y-m-d H:i:s",$cacheTime);
    $now = date("Y-m-d H:i:s");
    if ($now < $cacheTimeStr)
    {
			ob_get_clean();
      return (file_get_contents($filename));
		}
  }
  $htmlText = $htmlText.'
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clamp-js/0.7.0/clamp.min.js" integrity="sha512-Zf7q41OZ49XVIFrkbCVLkBEklVxQv4sVdMGnCwL9bfuCfA862QmAJSU61yrcrMwze7Ij7oUXpQVoUXmftBfk0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  $(document).ready(function(){
    var clampable = $( ".headline-iframe" );
    var arrayLength = clampable.length;
    for (var i = 0; i < arrayLength; i++) {
      $clamp(clampable[i], {
        clamp:3
      });
    }
    $( "#comments" ).remove();
  });
</script>
<div class = "wrapper-iframe">';
	$htmlText=$htmlText.'<ul class="wrapper-ul">';
  if (!isset($args['posts_per_page']))
  {
    $args['posts_per_page'] =5;
  }

  $cpt_query = new WP_Query($args);
  if ($cpt_query->have_posts()) { while ($cpt_query->have_posts()) : $cpt_query->the_post();


      $authorID = um_get_display_name( get_the_author() );

      $htmlText=$htmlText. '<li class = "post-item-iframe">';
      $htmlText=$htmlText. '<div class = "thumbnail-iframe"><a href="';
      $htmlText=$htmlText. get_permalink();
      $htmlText=$htmlText. '">';
      if ( has_post_thumbnail()) {
        //$htmlText=$htmlText.the_post_thumbnail( array(156, 156), ['class' => 'prettyThumbnail'] );
        $htmlText=$htmlText.get_the_post_thumbnail();
      }
      else{
        um_fetch_user( get_the_author_meta( 'ID' ) );
        $htmlText=$htmlText. um_user('profile_photo',100);
      }
      $htmlText=$htmlText. '</a></div>';
      $htmlText=$htmlText. '<div class = "text-iframe"><h2><a class = "headline-iframe" href="';
      $htmlText=$htmlText. get_permalink();
      $htmlText=$htmlText. '" target="_blank">';
      $htmlText=$htmlText. get_the_title();
      $htmlText=$htmlText. '</a></h2>';
      //$authorID = um_get_display_name( get_the_author() );
      $htmlText=$htmlText. '<div class = "byline-iframe">';
     // echo '<div class="river-date"><div class="time">';
      $htmlText=$htmlText. get_the_date();
    //  echo '</div> | ';
      $htmlText=$htmlText. ' | By ';
      $htmlText=$htmlText. get_the_author_meta('bizName', $authorID) . '</div></div>';
     // echo '</div>';
      $htmlText=$htmlText. '</li>';
      $htmlText=$htmlText. '<hr/>';

      endwhile;
			$htmlText=$htmlText."</ul>";
      $htmlText=$htmlText. '</div>';
    }
  else
    {
      $postType = "Briefs";
      if ($args['post_type'] == 'offer')
      {
        $postType = "Offers";
      }
      else {
        if ($args['post_type'] == 'real_estate')
        {
          $postType = "Real Estate posts";
        }
      }
      $htmlText=$htmlText. "No ".$postType." were found";
    }

    if ($args['caching']){
      $fp = fopen($filename,"w");
      fwrite($fp,$htmlText);
      fclose($fp);
    }
    ob_get_clean();
   return $htmlText;
};

function retrievePostsCol($args,$numCols=2,$maxPosts=10){
   ob_start();
  $htmlText = '';
  $filename = $args['meta_key'].$numCols.'cols.html';
	if ($args['caching'])
	{
		$fTime = filemtime($filename);
    $fTimeStr = date("Y-m-d H:i:s",$fTime);
    $timeInSeconds = 5 * 60;
    $cacheDelta = $timeInSeconds + rand(0,$timeInSeconds/2);
    //$cacheDelta = $timeInSeconds;
    $cacheTime = $fTime + $cacheDelta;
    $cacheTimeStr = date("Y-m-d H:i:s",$cacheTime);
    $now = date("Y-m-d H:i:s");

    if ($now < $cacheTimeStr)
    {
			ob_get_clean();
      return (file_get_contents($filename));
    }
	}
  $args['posts_per_page']= $maxPosts;


$htmlText = $htmlText.'
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clamp-js/0.7.0/clamp.min.js" integrity="sha512-Zf7q41OZ49XVIFrkbCVLkBEklVxQv4sVdMGnCwL9bfuCfA862QmAJSU61yrcrMwze7Ij7oUXpQVoUXmftBfk0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(document).ready(function(){
  var clampable = $( ".headline-iframe" );
  var arrayLength = clampable.length;
  for (var i = 0; i < arrayLength; i++) {
    $clamp(clampable[i], {
      clamp:3
    });
  }
  $( "#comments" ).remove();
});
</script>';
$htmlText = $htmlText."<div class = 'wrapper-iframe-multiCol".$numCols."'>";

  //$args['posts_per_page'] =10;
  $cnt = 1;
  $column = 1;
  $maxPerCol = $maxPosts/$numCols;
  $cpt_query = new WP_Query($args);
  if ($cpt_query->have_posts()) { while ($cpt_query->have_posts()) : $cpt_query->the_post();


      if ($cnt == 1)
      {
        $htmlText=$htmlText.'<div class="column'.$column.'">';
        $column++;
      }
      $authorID = um_get_display_name( get_the_author() );
      $htmlText=$htmlText.'<li class = "post-item-iframe">';
      $htmlText=$htmlText. '<div class = "thumbnail-iframe"><a href="';
      $htmlText=$htmlText. get_permalink();
      $htmlText=$htmlText. '">';
      if ( has_post_thumbnail()) {
        //the_post_thumbnail( array(156, 156), ['class' => 'prettyThumbnail'] );
        $htmlText=$htmlText.get_the_post_thumbnail();
      }
      else{
        um_fetch_user( get_the_author_meta( 'ID' ) );
        //echo um_user('profile_photo',100);
        $htmlText=$htmlText. um_user('profile_photo',100);
      }
      $htmlText=$htmlText. '</a></div>';
      $htmlText=$htmlText. '<div class = "text-iframe"><h2><a class = "headline-iframe" href="';
      $htmlText=$htmlText. get_permalink();
      $htmlText=$htmlText. '" target="_blank">';
      $htmlText=$htmlText. get_the_title();
      $htmlText=$htmlText. '</a></h2>';
      //$authorID = um_get_display_name( get_the_author() );
      $htmlText=$htmlText. '<div class = "byline-iframe">';
     // echo '<div class="river-date"><div class="time">';
      $htmlText=$htmlText. get_the_date();
    //  echo '</div> | ';
      $htmlText=$htmlText. ' | By ';
      $htmlText=$htmlText. get_the_author_meta('bizName', $authorID) . '</div></div>';
     // echo '</div>';
      $htmlText=$htmlText. '</li>';
      $htmlText=$htmlText. '<hr/>';
      if ($cnt == $maxPerCol)
      {
        $htmlText=$htmlText. '</div>';
        $cnt =0;
      }
      $cnt++;


      endwhile;
      $htmlText=$htmlText. '</div>';
    }
  else
    {
      $postType = "Briefs";
      if ($args['post_type'] == 'offer')
      {
        $postType = "Offers";
      }
      else {
        if ($args['post_type'] == 'real_estate')
        {
          $postType = "Real Estate posts";
        }
      }
      $htmlText= "No ".$postType." were found";
    }
    if ($args['caching']){
      $fp = fopen($filename,"w");
      fwrite($fp,$htmlText);
      fclose($fp);
    }
   ob_get_clean();
   return $htmlText;
};

function retrievePostsPretty($type,$uid=0){
   ob_start();

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if ($uid)
    {
      $args = array(
          'posts_per_page' => 10,
          'post_status' => 'publish',
          'paged' => $paged,
          'post_type' => $type,
          'author'     => $uid
      );
    }
    else {

        $args = array(
            'posts_per_page' => 10,
            'post_status' => 'publish',
            'paged' => $paged,
            'post_type' => $type
        );
    }



    $cpt_query = new WP_Query($args);

  if ($cpt_query->have_posts()) { while ($cpt_query->have_posts()) : $cpt_query->the_post();

        echo '<li class = "post-item">';
        echo '<div class="river-date"> Published <div class="time">';
        echo get_the_date();
        echo '</div></div>';
        echo '<a href="';
        echo the_permalink();
        echo '">';
        //the_post_thumbnail( array(156, 156), ['class' => 'prettyThumbnail'] );
        if ( has_post_thumbnail()) {
        the_post_thumbnail( array(156, 156), ['class' => 'prettyThumbnail'] );
        }
        else{
          um_fetch_user( get_the_author_meta( 'ID' ) );
          echo um_user('profile_photo',100);
        }
        echo '</a>';
        echo '<h2><a href="';
        echo the_permalink();
        echo '">';
        echo the_title();
        echo '</a></h2>';
				$authorID = um_get_display_name( get_the_author() );
        if (!$uid)
        {
          um_fetch_user($authorID);
          echo '<div class = "byline-all">By ';
          echo '<a href="/user/'.get_the_author_meta('um_user_profile_url_slug_name_dash', $authorID).'">';
          echo get_the_author_meta('bizName', $authorID) . '</a></div>';
        }

        the_excerpt();
        echo '</li>';
        echo '<hr/>';
      endwhile;
    } else {
      if ($uid) { //Display a "No X found." if there is an author with none of the post type.
        switch ($type) {
          case "brief":
            echo 'No briefs found.';
            break;
          case "offer":
            echo 'No offers found.';
            break;
          case "real_estate":
            echo 'No real estates found.';
            break;
          default:
            echo 'No posts found.';
        }
      }
    }

       ?>

<nav>
    <div>
        <div><?php previous_posts_link( '&laquo; Back...', $cpt_query->max_num_pages) ?></div>
        <div><?php next_posts_link( 'More... &raquo;', $cpt_query->max_num_pages) ?></div>
    </div>
</nav>
<?php
  return ob_get_clean();
};

function getAuthorName($authorID) {
	return trim(um_get_display_name($authorID));
}
function getProfileURL($authorID){
  return '/user/'.get_the_author_meta('um_user_profile_url_slug_name_dash', $authorID);
}
function reformatMagicDate($magicDate){
  $parts = explode(" ", $magicDate);
  $dateRaw = date_create($parts[0]);
  $dateFormatted = date_format($dateRaw,"F d, Y");
  return $dateFormatted;
}
?>
