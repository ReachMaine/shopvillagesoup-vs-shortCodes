<?php
/**
 * Plugin Name:     Vs Redirect
 * Description:     Functions focused on the brief/offer/real-estate management redirects
 * Author:          Samuel St. Onge
 * Text Domain:     vs-redirect
 * Domain Path:     /languages
 * Version:         0.1.1
 *
 * @package         Vs_Redirect
 */

// Your code starts here.

//Return-to-own-site-button
add_action( 'admin_init', function () {


    global $pagenow;
    $user = new WP_User( get_current_user_id() );
    $roles = $user->roles;
    $admin = "administrator";
    $staff = "um_staff-member";
    $cardOnly = "um_user-card-only";
    $kwRealEstate = "um_knox-and-waldo-real-estate";
    $eaiRealEstate = "um_user-ea-and-islander-real-estate";

    if (in_array($admin, (array) $user->roles) || in_array($staff, (array) $user->roles))
    {
      return;
    }

    if (in_array($cardOnly, (array) $user->roles)) {
      returnCompanyPage();
      exit;
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

    if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'real_estate' ) {
      //Check if the user can access realestates or is staff
      if (in_array($kwRealEstate, (array) $user->roles) || in_array($eaiRealEstate, (array) $user->roles)) {
        wp_redirect( admin_url( '/admin.php?page=manage-myRealEstates' ) );
        exit;
      } else {
        returnCompanyPage();  //The user cannot access realestates, redirect them to their company page
        exit;
      }
    }

    if ( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'real_estate' ) {
      //Check if the user can access realestates or is staff
      if (in_array($kwRealEstate, (array) $user->roles) || in_array($eaiRealEstate, (array) $user->roles)) {
        return;
        exit;
      } else {
        returnCompanyPage();  //The user cannot access realestates, redirect them to their company page
        exit;
      }
    }


    if (  isset( $_GET['u'] ) && $_GET['u'] == 'myProfile' ) {
      returnCompanyPage();
      exit;
    }
} );

function returnCompanyPage() {
    wp_redirect(um_user_profile_url(),301);
    exit;
}
