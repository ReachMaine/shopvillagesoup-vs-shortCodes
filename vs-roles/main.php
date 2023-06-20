<?php
/*
* Plugin Name: VillageSoup Custom Roles
* Description: Custom Roles for Briefs and offers
* Version: 1.0
* Author: rstonge@villagesoup.com
*/

function remove_classified_capability()
{
	
     if( is_user_logged_in() ) 
     { // check if there is a logged in user 
       $user = wp_get_current_user(); // getting & setting the current user 
	     $roles = ( array ) $user->roles; // obtaining the role 
	     //echo '<pre>'.print_r( $roles, true ).'</pre>';
       if (in_array("um_user-knox-and-waldo",$roles) || in_array("um_user-ea-and-islander",$roles) || in_array("um_user-knox-waldo-ea-islander",$roles))
       {
           $slug = 'edit.php?post_type=classified';
           remove_menu_page($slug);
           $slug = 'edit.php?post_type=real_estate';
           remove_menu_page($slug);		   
           $slug = 'edit.php';
           remove_menu_page($slug);		   
		       $slug = 'tools.php';
           remove_menu_page($slug);   
           $slug = 'edit-comments.php';
           remove_menu_page($slug);          
           $slug = 'upload.php';
           remove_menu_page($slug);
		       $slug = 'edit.php?post_type=elementor_library&tabs_group=library';
           remove_menu_page($slug);
       }	 
       if (in_array("um_user-knox-and-waldo-re",$roles) || in_array("um_user-ea-and-islander-re",$roles) || in_array("um_user-knox-waldo-ea-islander-re",$roles))
       {
           $slug = 'edit.php?post_type=classified';
           remove_menu_page($slug);
           $slug = 'edit.php';
           remove_menu_page($slug);		   
		       $slug = 'tools.php';
           remove_menu_page($slug);   
           $slug = 'edit-comments.php';
           remove_menu_page($slug);          
           $slug = 'upload.php';
           remove_menu_page($slug);
		       $slug = 'edit.php?post_type=elementor_library&tabs_group=library';
           remove_menu_page($slug);
       }	 

       if (in_array("um_user-card-only",$roles))
       {
          $slug = 'edit.php?post_type=brief';
           remove_menu_page($slug); 
           $slug = 'edit.php?post_type=offer';
           remove_menu_page($slug);
           $slug = 'edit.php?post_type=classified';
           remove_menu_page($slug);
           $slug = 'edit.php?post_type=real_estate';
           remove_menu_page($slug);		   
           $slug = 'edit.php';
           remove_menu_page($slug);		   
		       $slug = 'tools.php';
           remove_menu_page($slug);
		       $slug = 'upload.php';
           remove_menu_page($slug);   
       }
       
	 }
	 
}
add_action('admin_init', 'remove_classified_capability',999);

function wpdocs_remove_customize( $wp_admin_bar ) {
	$user = wp_get_current_user(); // getting & setting the current user 
	$roles = ( array ) $user->roles; // obtaining the role 
	//echo '<pre>'.print_r( $roles, true ).'</pre>';
  if (in_array("um_user-knox-and-waldo",$roles) || in_array("um_user-ea-and-islander",$roles) || in_array("um_user-knox-waldo-ea-islander",$roles))
  {
    // Remove customize, background and header from the menu bar.   
    $wp_admin_bar->remove_node( 'new-classified' );  
	  $wp_admin_bar->remove_node( 'new-real_estate' );  
	  $wp_admin_bar->remove_node( 'new-post' );  
    $wp_admin_bar->remove_node( 'new-media' );  
  }

  if (in_array("um_user-knox-and-waldo-re",$roles) || in_array("um_user-ea-and-islander-re",$roles) || in_array("um_user-knox-waldo-ea-islander-re",$roles))
  {
    // Remove customize, background and header from the menu bar.   
    $wp_admin_bar->remove_node( 'new-classified' );  
	  $wp_admin_bar->remove_node( 'new-post' );  
    $wp_admin_bar->remove_node( 'new-media' );  
  }

  if (in_array("um_user-card-only",$roles))
  {
    $wp_admin_bar->remove_node( 'new-classified' );  
	  $wp_admin_bar->remove_node( 'new-real_estate' );  
	  $wp_admin_bar->remove_node( 'new-post' );  
    $wp_admin_bar->remove_node( 'new-offer' );  
    $wp_admin_bar->remove_node( 'new-brief' );  
  }
	
}
add_action( 'admin_bar_menu', 'wpdocs_remove_customize', 999 );