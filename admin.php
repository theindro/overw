<?php
/*
Template Name: Admin
*/
?>

<?php
if ($current_user->roles[0] == 'administrator') {

global $wpdb;

$data = array(
    'name' => $_POST['tournament_name'],
    'link' => $_POST['tournament_link'],
    'date' => date("Y-m-d"),
);
$insert_tournament = $wpdb->insert('wp_tournaments', $data);

exit('Ok');
} else {
    echo 'No permissions!';
}

?>
