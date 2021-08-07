<?php

$file = 'upload/avatar_'.current_user();

$width = $panelvars['max_width'] ?: 100;
$height = $panelvars['max_height'] ?: 100;

if( !empty( $_FILES['avatar'] ))
	try {
		$upload = new upload_img( $_FILES['avatar'] );
		$upload->restrictType(array( 'png', 'jpg', 'jpeg', 'gif' ));

		if( $panelvars['resize'] ) {
			$upload->resizeScale( $width, $height );
		} else {
			$upload->restrictImageSize( $width, $height );
		}

		$upload->savePng($file);

		// Create Thumbnail
		$upload->resizeScale( 16, 16 )->savePng('upload/thumb_'.current_user());
		db()->user_data->updateRow(array('avatar' => 1), $user->id );

		echo '<div class="success">Upload erfolgreich.</div>';
	} catch( Exception $e ) {
		echo '<div class="error">'.$e->getMessage().'</div>';
	}

if( file_exists( $file.'.png' )) {
	echo '<div class="current_avatar">
		<p><img src="'.$file.'.png" alt="Avatar"></p>
		<p><a href="" class="btn">Avatar löschen</a></p>
		</div>';
}

$form = new form_renderer( PAGE_SELF );
$form->upload( 'avatar', 'Avatar', 'image/*' );
echo $form;
