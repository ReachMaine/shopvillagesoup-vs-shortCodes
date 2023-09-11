<?php
/**
 * Plugin Name:     VS Admin Edit
 * Description:     Creates custom menus for VS Plugins/Handles redirects to Manage pages for users
 * Author:          Samuel St. Onge
 * Text Domain:     vs-admin-edit
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         VS_Admin_Edit
 */

// Your code starts here.
add_action( 'admin_init', function () {

    global $pagenow;
    $user = new WP_User( get_current_user_id() );
    $roles = $user->roles;
    $admin = "administrator";
    $staff = "um_staff-member";

    if (in_array($admin, (array) $user->roles) || in_array($staff, (array) $user->roles))
    {
      return;
    }

    # Check current admin page.
    if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'brief' ) {
        wp_redirect( admin_url( '/admin.php?page=manage-myBriefs' ) );
        exit;
    }
    if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'offer' ) {
        wp_redirect( admin_url( '/admin.php?page=manage-myOffers' ) );
        exit;
    }
    if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'realEstate' ) {
        wp_redirect( admin_url( '/admin.php?page=manage-myRealEstates' ) );
        exit;
    }
    if (  isset( $_GET['u'] ) && $_GET['u'] == 'myProfile' ) {
      returnCompanyPage();
        exit;
    }
} );

//Return-to-own-site-button
function vs_menu_company_page() {
		add_menu_page(
        __( 'company_page', 'my-textdomain' ),
        __( 'Company Page', 'my-textdomain' ),
        'publish_posts',
        'return-company_page',
        'returnCompanyPage',
        3
		);
  }

function returnCompanyPage() {
    wp_redirect(um_user_profile_url(),301);
    exit;
}

add_action( 'admin_menu', 'vs_menu_company_page', 1);

add_action( 'admin_bar_menu', 'vs_toolbar_company_page', 31);

function vs_toolbar_company_page($admin_bar) {
  $admin_bar->add_menu(array('parent' => 'site-name',
    'title' => 'Visit Company Page',
    'id' => 'vs-company-profile',
    'href' => um_user_profile_url(),
    'meta' => array('target' => '_blank'))
  );
}

add_action( 'admin_bar_menu', 'vs_toolbar_manage_posts', 999);

function vs_toolbar_manage_posts($admin_bar) {
  $admin_bar->add_menu( array(
    'title' => 'Manage Posts',
    'id' => 'vs-manage-posts',
    'href' => get_site_url().'/wp-admin/admin.php?page=manage-myBriefs',
    'meta' => array('target' => '_blank')
  ));
  $admin_bar->add_menu( array(
    'parent' => 'vs-manage-posts',
    'title' => 'Briefs',
    'id' => 'manage-briefs',
    'href' => get_site_url().'/wp-admin/admin.php?page=manage-myBriefs',
    'meta' => array(
      'title' => __('Briefs'),
      'target' => '_blank',
      'class' => 'manage-posts'
    )
  ));
  $admin_bar->add_menu( array(
    'parent' => 'vs-manage-posts',
    'title' => 'Offers',
    'id' => 'manage-offers',
    'href' => 'get_site_url()./wp-admin/admin.php?page=manage-myOffers',
    'meta' => array(
      'title' => __('Offers'),
      'target' => '_blank',
      'class' => 'manage-posts'
    )
  ));
  $admin_bar->add_menu( array(
    'parent' => 'vs-manage-posts',
    'title' => 'Real Estates',
    'id' => 'manage-real_estates',
    'href' => get_site_url().'/wp-admin/admin.php?page=manage-myRealEstates',
    'meta' => array(
      'title' => __('Real Estates'),
      'target' => '_blank',
      'class' => 'manage-posts'
    )
  ));
}
