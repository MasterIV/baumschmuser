<?php

$result = $session->changePassword( PAGE_SELF );

if( $result['error'] ) echo '<div class="error">'.$result['error'].'</div>';
if( $result['success'] ) echo '<div class="success">'.$result['success'].'</div>';

echo $result['form'];
