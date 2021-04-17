<?php 

function alter_the_edit_screen_query( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'activate_plugins' ) )  {
			add_action( 'views_edit-post', 'remove_items_from_edit' );
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}

add_filter('parse_query', 'alter_the_edit_screen_query' );

function remove_items_from_edit( $views ) {
    unset($views['all']);
    unset($views['publish']);
    unset($views['trash']);
    unset($views['draft']);
    unset($views['pending']);
    return $views;
}

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

add_filter( 'wp_insert_post_data' , 'modify_post_title' , '99', 1 ); // Grabs the inserted post data so you can modify it.
function modify_post_title( $data )
{
  if($data['post_type'] == 'esetek') { // If the actual field name of the rating date is different, you'll have to update this.
    $date = date("Ymd");
    $title = $date;
    $data['post_title'] =  $title ; //Updates the post title to your new title.
  }
  return $data; // Returns the modified data.
}