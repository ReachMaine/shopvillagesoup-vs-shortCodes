<?php
/**
 * Plugin Name:     Vs User Admin
 * Description:     Adds History to users
 * Author:          Ray St. Onge
 * Text Domain:     vs-user-admin
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         VS_User_Admin
 */

// Your code starts here.
/**********************/
/*** Adding extra field to get the the user who creates the another user during ADD NEW USER ***/
function custom_user_profile_fields($user){
    $newuser = 1;
    if(is_object($user)){
      $created_date = date("Y-m-d H:i:s");
      $created_by = esc_attr( get_the_author_meta( 'created_by', $user->ID ) );
      $created_date = esc_attr( get_the_author_meta( 'created_date', $user->ID ) );
      if (!strlen($created_by))
      {
          $created_by = 38; // Set it to Sam who most likely created most users
          $created_date = "2022-06-27 12:00:00";
      }
    }
    else
    {
      $created_by = get_current_user_id();
      $created_date = date("Y-m-d H:i:s");
    }
    $creator = get_userdata($created_by);
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="created_by">Created By</label></th>
            <td>
              <!--
                <input type="text" class="regular-text" name="created_by" value="<?php echo $created_by; ?>" id="created_by" /><br />
              -->
                <input type="hidden" name="created_by" value="<?php echo $created_by; ?>" id="created_by" />
                <input type="hidden" name="created_date" value="<?php echo $created_date; ?>" id="created_date" />
                <?php
                if (!strlen($creator->first_name) && !strlen($creator->last_name)){
                  ?>
                  <span class="description"><?php echo $creator->user_email;?> </span><br />
                  <?php
                }else{
                  ?>
                <span class="description"><?php echo $creator->first_name . ' ' . $creator->last_name;?> </span><br />
                <?php
                }
                ?>
                <span class="description"><?php echo $created_date;?> </span><br />
            </td>
        </tr>
    </table>
<?php
}
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
  if ( in_array( 'administrator', $user_roles, true ) ) {
    add_action( 'show_user_profile', 'custom_user_profile_fields' );
    add_action( 'edit_user_profile', 'custom_user_profile_fields' );
    add_action( "user_new_form", "custom_user_profile_fields" );
    add_action('user_register', 'save_custom_user_profile_fields');
    add_action('profile_update', 'save_custom_user_profile_fields');
  }
}
function save_custom_user_profile_fields($user_id){
    # again do this only if you can
    //if(!current_user_can('manage_options'))
      //  return false;

    # save my custom field
    update_user_meta($user_id, 'created_by', $_POST['created_by']);
    update_user_meta($user_id, 'created_date', $_POST['created_date']);

}
