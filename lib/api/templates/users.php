<?php
$blogusers = get_users( array( 'fields' => array( 'display_name', 'user_login' ) ) );
echo json_encode($blogusers);
?>
