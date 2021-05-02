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


add_action('acf/save_post', 'save_post_functions', 11);

function save_post_functions( $post_id ) {
  $ceg_id = get_field('munkavallalo_cege', $post_id)[0]; // NOTE: enter the name of the ACF field here
  $max = get_field('face_to_face_maximum_appointment', $ceg_id);
  $tanacsadas_datumai = get_field('tanacsadas_datumai', $post_id);
  $datumok = explode(",", $tanacsadas_datumai);
  update_field('lezajlott_alkalmak_szama', count($datumok), $post_id);
  update_field('maximum_alkalmak_szama', (int)$max, $post_id);

  //saveing author based on tanacsado field
  $author_id = get_field("tanacsado_neve", $post_id)['ID'];
  $arg = array(
    'ID' => $post_id,
    'post_author' => $author_id,
  );
  wp_update_post( $arg );
  if(get_field("lezaras_datuma")){
    wp_set_object_terms( $post_id, 2, 'category' ); //zárt kategória ID-je: 2

  }else{
    wp_set_object_terms( $post_id, 1, 'category' ); //nyitott kategória ID-je: 2

  }

}

add_action('new_to_publish', 'new_eset');


add_action( 'save_post_esetek',  'send_email', 10, 3 );

function send_email( $post_id, $post, $update ) {
      // If an old book is being updated, exit
      if ( $update ) {
        return;
    }
    $author_id = get_field("tanacsado_neve", $post_id)['ID'];
    if($author_id){
      $user_email = get_user_by('ID', $author_id)->user_email;
      $to = $user_email;
      $subject = 'Új eset került rögzítésre az Emplifyon!';
      $body = 'Tekintse meg nyitott ügyeit <a href="http://emplify.teachother.hu/wp-admin/edit.php?post_type=esetek">itt</a>';
      $headers = array('Content-Type: text/html; charset=UTF-8');
      
      wp_mail( $to, $subject, $body, $headers );
    }

  
}

