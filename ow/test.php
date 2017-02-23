<?php
/**
 * Created by PhpStorm.
 * User: indro
 * Date: 28.11.2016
 * Time: 20:53
 */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "overwatch";

$tag = 'deathwish-22634';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

$json_string = "https://api.lootbox.eu/pc/eu/$tag/competitive/heroes";
$jsondata = file_get_contents($json_string);
$obj = json_decode($jsondata, true);

$del = "DELETE FROM wp_heroes WHERE battle_tag_id = '$tag'";
if (mysqli_query($conn, $del)) {
    echo "";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
//print_r($obj);
foreach ($obj as $value => $data) {
    $tag = 'deathwish-22634';
    $name = $data['name'];
    $playtime = $data['playtime'];
    $image = $data['image'];
    $pct = $data['percentage'];
    $sql = "INSERT INTO wp_heroes (battle_tag_id, hero_id, playtime, image, percentage) VALUES ('$tag', '$name', '$playtime', '$image', '$pct') ON DUPLICATE KEY UPDATE
 battle_tag_id = '$tag',
 hero_id = '$name',
 playtime = '$playtime',
 image = '$image',
 percentage = '$pct'";

    if (mysqli_query($conn, $sql)) {
        echo "";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}
