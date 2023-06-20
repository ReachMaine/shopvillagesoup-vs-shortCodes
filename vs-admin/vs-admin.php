<?php
/**
 * Plugin Name:     VS Admin
 * Description:     Creates admin pages for users to edit their briefs/offers/real estate
 * Author:          Ray St. Onge
 * Text Domain:     vs-admin
 * Domain Path:     /languages
 * Version:         1.1.0
 *
 * @package         VS_Admin
 */

// Your code starts here.
define( 'VS_BRIEF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
 // Update CSS within in Admin
 function brief_style() {
   wp_enqueue_style( 'brief_style', VS_BRIEF_PLUGIN_URL . 'css/vs-admin.css', array(), false, 'screen' );
 }
 add_action('admin_enqueue_scripts','brief_style');

//-----------------------------TOOLBAR-----------------------------//
//Add a company-page return under the 'Shop villagesoup' section
 add_action( 'admin_bar_menu', 'vs_toolbar_company_page', 31);
 function vs_toolbar_company_page($admin_bar) {
   $admin_bar->add_menu(array('parent' => 'site-name',
     'title' => 'Visit Company Page',
     'id' => 'vs-company-profile',
     'href' => um_user_profile_url(),
     'meta' => array('target' => '_blank'))
   );
 }
//Add a section to manage briefs/offers/real-estate
 add_action( 'admin_bar_menu', 'vs_toolbar_manage_posts', 999);
 function vs_toolbar_manage_posts($admin_bar) {
   $admin_bar->add_menu( array(
     //'title' => '<span class="dashicons-category"></span>'.__( 'Manage Posts', 'manage-posts' ), //Dashicon f318 'category'
     'title' => __( 'Manage Posts', 'manage-posts'),
     'id' => 'vs-manage-posts',
     'href' => 'https://shop.villagesoup.com/wp-admin/admin.php?page=manage-myBriefs',
     'meta' => array(
       'target' => '_self',
       'title' => __( 'Manage Posts', 'manage-posts')
     )
   ));
   $admin_bar->add_menu( array(
     'parent' => 'vs-manage-posts',
     'title' => 'Briefs',
     'id' => 'manage-briefs',
     'href' => 'https://shop.villagesoup.com/wp-admin/admin.php?page=manage-myBriefs',
     'meta' => array(
       'title' => __('Briefs'),
       'class' => 'manage-posts'
     )
   ));
   $admin_bar->add_menu( array(
     'parent' => 'vs-manage-posts',
     'title' => 'Offers',
     'id' => 'manage-offers',
     'href' => 'https://shop.villagesoup.com/wp-admin/admin.php?page=manage-myOffers',
     'meta' => array(
       'title' => __('Offers'),
       'class' => 'manage-posts'
     )
   ));
   $admin_bar->add_menu( array(
     'parent' => 'vs-manage-posts',
     'title' => 'Real Estates',
     'id' => 'manage-real_estates',
     'href' => 'https://shop.villagesoup.com/wp-admin/admin.php?page=manage-myRealEstates',
     'meta' => array(
       'title' => __('Real Estates'),
       'class' => 'manage-posts'
     )
   ));
 }

//-----------------------------MENU-----------------------------//
// Menu for returning to Company page
 add_action( 'admin_menu', 'vs_menu_company_page', 2);
 function vs_menu_company_page() {
 		add_menu_page(
         __( 'company_page', 'my-textdomain' ),
         __( 'Company Page', 'my-textdomain' ),
         'publish_posts',
         '/user?u=myProfile',
         'returnCompanyPage',
 				'dashicons-id-alt',
         2
 		);
   }

 // Add the menus for briefs
function vs_admin_brief_menu() {
		add_menu_page(
			__( 'manange-briefs', 'my-textdomain' ),
			__( 'Manage Briefs', 'my-textdomain' ),
			'publish_posts',
			'manage-myBriefs',
			'vs_admin_briefs_contents',
			'dashicons-testimonial',
			3
		);
    add_submenu_page(
      'manage-myBriefs',
      'Add New Brief', //page title
      'New Brief', //menu title
      'publish_posts', //capability,
      'add-New-Brief',//menu slug
      'addNewBrief' //callback function
    );
	}

  function addNewBrief() {
    wp_redirect(admin_url('/post-new.php?post_type=brief'));
  }

	add_action( 'admin_menu', 'vs_admin_brief_menu' );

   // Add the menus for offers
  add_action('wp_enqueue_scripts', 'enqueue_styleOffers');
  function enqueue_styleOffers(){
     //wp_enqueue_script('custom_jquery');
     wp_enqueue_style( 'offer_style' );
  }

  function vs_admin_offer_menu() {
  		add_menu_page(
  			__( 'manange-offers', 'my-textdomain' ),
  			__( 'Manage Offers', 'my-textdomain' ),
  			'publish_posts',
  			'manage-myOffers',
  			'vs_admin_offers_contents',
  			'dashicons-money-alt',
  			4
  		);
      add_submenu_page(
        'manage-myOffers',
        'Add New Offer', //page title
        'New Offer', //menu title
        'publish_posts', //capability,
        'add-New-Offer',//menu slug
        'addNewOffer' //callback function
      );
    }

  function addNewOffer() {
      wp_redirect(admin_url('/post-new.php?post_type=offer'));
  }
  add_action( 'admin_menu', 'vs_admin_offer_menu' );
  // Add the menus for real estate
  add_action('wp_enqueue_scripts', 'enqueue_styleRealEstates');

  function enqueue_styleRealEstates(){
     wp_enqueue_style( 'realEstate_style' );
  }
  function vs_admin_realEstate_menu() {
  		add_menu_page(
  			__( 'manange-realEstates', 'my-textdomain' ),
  			__( 'Manage Real Estates', 'my-textdomain' ),
  			'publish_posts',
  			'manage-myRealEstates',
  			'vs_admin_realEstates_contents',
  			'dashicons-store',
  			5
  		);
      add_submenu_page(
        'manage-myRealEstates',
        'Add New Real Estate', //page titleopen-folder
        'New Real Estate', //menu title
        'publish_posts', //capability,
        'add-New-Real-Estate',//menu slug
        'addNewRealEstate' //callback function
      );
    }

    function addNewRealEstate() {
      wp_redirect(admin_url('/post-new.php?post_type=real_estate'));
    }

  	add_action( 'admin_menu', 'vs_admin_realEstate_menu' );

if(  ! function_exists( 'current_user_has_role' ) ){
    function current_user_has_role( $role ){
        return user_has_role_by_user_id( get_current_user_id(), $role );
    }
}
//-----------------------------BLOCK MENU-----------------------------//
//Blocks allowed to be used in the block editor
if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php");
}
if(!function_exists('current_user_has_role')) {
    include_once(ABSPATH.'wp-includes/user.php');
}
$user = wp_get_current_user();
if($user->ID != 0 or $user->ID != null){
  $user = get_userdata( $user->ID );

  // Get all the user roles as an array.
  $user_roles = $user->roles;

  // Check if the role you're interested in, is present in the array.
  if (! in_array( 'administrator', $user_roles, true ) ) {
    add_filter('allowed_block_types','vs_allowed_block_types');
  }
}
function vs_allowed_block_types( $allowed_blocks) {
  return array(
    'core/paragraph',     //Text
    'core/heading',
    'core/list',
    'core/quote',
    'core/table',
    'core/preformatted',
    'core/pullquote',
    'core/image',         //Media
    'core/gallery',
    'core/cover',
    'core/media-text',
    'core/text-columns',  //Design
    'core/columns',
    'core/separator',
    'core/spacer',
    'core/group',
    'core/row',
    'core/stack',
    'core/embed'          //Embed*
  );
} //*Either you embed everything WordPress offers, or nothing at all

//------------------------------DISPLAY------------------------------//
    //Display content
    function vs_admin_realEstates_contents() {
  		?>
  			<h1>
  				<?php esc_html_e( 'My Real Estates', 'my-plugin-textdomain' ); ?>
  			</h1>
  		<?php
      $profile_id = um_profile_id();
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $args = array(
          'author'        =>  $profile_id,
          'posts_per_page' => 10,
          'paged' => $paged,
          'post_type' => 'real_estate'
      );
      vs_edit_posts($args);
    }
    function vs_admin_offers_contents() {
  		?>
  			<h1>
  				<?php esc_html_e( 'My Offers', 'my-plugin-textdomain' ); ?>
  			</h1>
  		<?php
      $profile_id = um_profile_id();
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $args = array(
          'author'        =>  $profile_id,
          'posts_per_page' => 10,
          'paged' => $paged,
          'post_type' => 'offer'
      );
      vs_edit_posts($args);
    }
	function vs_admin_briefs_contents() {
		?>
			<h1>
				<?php esc_html_e( 'My Briefs', 'my-plugin-textdomain' ); ?>
			</h1>
		<?php
    $profile_id = um_profile_id();
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'author'        =>  $profile_id,
        'posts_per_page' => 10,
        'paged' => $paged,
        'post_type' => 'brief'
    );
    vs_edit_posts($args);
  }
  function vs_edit_posts($args){
    $cpt_query = new WP_Query($args);

    $publishClass = "";
  if ($cpt_query->have_posts()) : while ($cpt_query->have_posts()) : $cpt_query->the_post();
        $publishClass = "";
        $status = get_post_status();        //get the post status
        $status = strtolower($status);      //covert $status to lowercase for standardization
        if ($status != "publish") {         //check if the post has the publish status
          $publishClass = "notpublished";   //->It does not, give $publishClass the "notpublished" status
        }
        $status = ucfirst($status);         //capitalize the first letter
        if ($status == "Publish") {         //change "Publish"
          $status = "Published";            //-> "Published"
        }
        echo '<li class = "post-item">';
        echo '<div class="river-date"> ';
        echo '<div class="'.$publishClass.'">'.$status.'</div>';
        echo ' <div class="time">';
        echo get_the_date();
        echo ' </div>';
        echo ' <div class="time">';
        echo gt_get_post_view();
        //the_date();
        echo '</div></div>';
        echo '<a href="';
        echo '/wp-admin/post.php?post='.get_the_ID().'&action=edit';  //image link target
        echo '">';
        the_post_thumbnail( array(156, 156), ['class' => 'prettyThumbnail'] );
        echo '</a>';
        echo '<h2><a href="';
        echo '/wp-admin/post.php?post='.get_the_ID().'&action=edit';  //title link target
        echo '">';
        echo the_title();  //get the title
        echo '</a></h2>';

        //toolbar
        echo '<h4><a href="';
        echo the_permalink(); //'view' link target
        echo '">';
        echo 'View'; //'view' text
        echo '</a>';
        echo ' | ';  //spaceing '|'
        echo '<a href="';
        echo '/wp-admin/post.php?post='.get_the_ID().'&action=edit';  //'edit' link target
        echo '">';
        echo 'Edit'; //'edit' text
        echo '</a>';
        echo '</h4>';

      //  $authorID = um_get_display_name( get_the_author() );
      //  echo '<div class = "byline-all">By ' . get_the_author_meta('bizName', $authorID) . '</div>';
        the_excerpt();

        echo '</li>';
        echo '<hr/>';
      endwhile;
    else:
      echo '<br>';
      echo '<div><h4>No posts found.</h4></div>';
    endif;?>

<nav>
    <div>
        <div><?php previous_posts_link( '&laquo; Back...', $cpt_query->max_num_pages) ?></div>
        <div><?php next_posts_link( 'More... &raquo;', $cpt_query->max_num_pages) ?></div>
    </div>
</nav>
<?php

};
add_filter( 'ajax_query_attachments_args', 'wpsnippet_show_current_user_attachments' );
function wpsnippet_show_current_user_attachments( $query ) {
	 $user_id = get_current_user_id();
	 if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts')) {
			$query['author'] = $user_id;
	 }
	 return $query;
}


?>
