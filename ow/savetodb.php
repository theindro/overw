<?php
/*
Template Name: savetodbscript
*/
?>

<form action="" method="post">
    <input type="submit">
</form>


<?php
/* loeb anmdebaasist battletagid
global $wpdb;
$result = $wpdb->get_results("SELECT * FROM wp_battletag");
foreach ($result as $print) {
    $array1 = 'https://api.lootbox.eu/pc/eu/' . $print->battletag . '/profile ';
    echo $array1;
}
*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "overwatch";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

$pages = array("https://api.lootbox.eu/pc/eu/indro-2407/profile",
    "https://api.lootbox.eu/pc/eu/Zinc-2266/profile",
    "https://api.lootbox.eu/pc/eu/midy-11980/profile",
    "https://api.lootbox.eu/pc/eu/Nuffenzo-2264/profile",
    "https://api.lootbox.eu/pc/eu/Yoko-2908/profile",
    "https://api.lootbox.eu/pc/eu/evilmojo-2706/profile",
    "https://api.lootbox.eu/pc/eu/ZiCdaMASTA-2832/profile");
foreach ($pages as $page) {
    ini_set('max_execution_time', 300);
    $html = file_get_contents($page);
    $parsed_json = json_decode($html);

    $nimi = $parsed_json->data->username;
    $level = $parsed_json->data->level;
    $rank = $parsed_json->data->competitive->rank;
    $avatar = $parsed_json->data->avatar;
    $pilt = $parsed_json->data->competitive->rank_img;

    echo $nimi . '<br>';
    echo $level . '<br>';
    echo $rank . '<br>';
    echo $avatar . '<br>';
    echo $pilt . '<br>';


    $sql = "INSERT INTO `wp_ranking` (nimi, lvl, rank, avatar, pilt)
  VALUES ('$nimi', '$level', '$rank', '$avatar', '$pilt')
  ON DUPLICATE KEY UPDATE
  lvl = '$level',
  rank = '$rank',
  avatar = '$avatar',
  pilt = '$pilt'";

    if (mysqli_query($conn, $sql)) {
        echo "New record created successfully" . "<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}


?>


