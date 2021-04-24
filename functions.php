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

/**Ez a rész arra jó, hogy ne lehessen elmenteni úgy az esetet, hogy a következő mezők üresek, ha már meg van adva lezárás dátum. */
add_filter('acf/validate_value/name=hogyan_zarult',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=munkabol_kihagyott_napok_szama',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=funkcio_elotte',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=funkcio_utana',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=stressz_elotte',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=stressz_utana',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=phq_elotte',	'validate_empty_fields', 10, 4);
add_filter('acf/validate_value/name=phq_utana',	'validate_empty_fields', 10, 4);

function validate_empty_fields( $valid, $value, $field, $input ){
	
	// bail early if value is already invalid
	if( !$valid ) {return $valid; }
	

	$eset_lezaras_datum = $_POST['acf']['field_607c1199cbe87']; // Ez a mező a lezárás dátuma mező.
	
	if($eset_lezaras_datum){
		if(!$value){
			$valid = __('Lezárt esetnél kötelező kitölteni!');
		}
	}

	return $valid;
	
}

 /*add_filter('acf/update_value/name=maximum_alkalmak_szama', 'my_acf_update_value', 10, 4);
 function my_acf_update_value( $value, $post_id, $field, $original=0 ) {
  $ceg_id = $_POST['acf']['field_607c123c2d4f8'];
  $max = get_field('face_to_face_maximum_appointment', $ceg_id);
  return (int)$max;
}*/

/*function sync_acf($post_id, $post, $update) {
    
  $ceg_id = get_field('munkavallalo_cege', $post_id); // NOTE: enter the name of the ACF field here
  $max = get_field('face_to_face_maximum_appointment', $ceg_id);
  update_field('face_to_face_maximum_appointment', 10, $post_id);
}
add_action('save_post', 'sync_acf', 10, 3);*/

add_action('acf/save_post', 'save_post_functions', 20);

function save_post_functions( $post_id ) {
    if( isset($_POST['acf']['field_607c123c2d4f8']) ) {
      $ceg_id = $_POST['acf']['field_607c123c2d4f8'][0];
      $max = get_field('face_to_face_maximum_appointment', $ceg_id);
      $_POST['acf']['field_6082f512aa790'] = (int)$max;
    }
}