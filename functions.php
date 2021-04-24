<?php 

function alter_the_edit_screen_query( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'activate_plugins' ) )  {
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}

add_filter('parse_query', 'alter_the_edit_screen_query' );

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/*add_filter( 'wp_insert_post_data' , 'modify_post_title' , '99', 1 ); // Grabs the inserted post data so you can modify it.
function modify_post_title( $data )
{
  if($data['post_type'] == 'esetek') { // If the actual field name of the rating date is different, you'll have to update this.
    $date = date("Ymd");
    $title = $date;
    $data['post_title'] =  $title ; //Updates the post title to your new title.
  }
  return $data; // Returns the modified data.
}*/

add_filter('acf/validate_value/name=hogyan_zarult',	'validate_empty_fields', 10, 4);

function validate_empty_fields( $valid, $value, $field, $input ){
	
	// bail early if value is already invalid
	if( !$valid ) {return $valid; }
	

	$is_event_public = $_POST['acf']['field_607c1199cbe87']; // This field is a checkbox
	
	if($is_event_public){
		if(!$value){
			$valid = __('Lezárt esetnél kötelező kitölteni!');
		}
	}

	return $valid;
	
}