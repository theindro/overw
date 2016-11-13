<?php
/*
Template Name: uuenda profiili
*/
?>
<?php get_header(); ?>

<?php
if (isset($_GET['submit'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "overwatch";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    global $wpdb;
    $uus = $_GET['battletag'];

    $tablename = $wpdb->prefix . 'ranking';
    $data = array(
        'battletag' => $_GET['battletag'],
    );
    $wpdb->insert($tablename, $data);
    $uus = $_GET['battletag'];
    $result = $wpdb->get_results("SELECT * FROM wp_ranking where battletag = '$uus'");

    foreach ($result as $print) {
        $tag = $print->battletag;

        $pages = array("https://api.lootbox.eu/pc/eu/$tag/profile");

        foreach ($pages as $page) {
            ini_set('max_execution_time', 300);
            $html = @file_get_contents($page);
            $parsed_json = json_decode($html);

            $nimi = $parsed_json->data->username;
            if (empty($nimi)) {
                $delete = "DELETE FROM `wp_ranking` WHERE `wp_ranking`.`battletag` = '$uus'";
                if (mysqli_query($conn, $delete)) {
                    echo "<p class='viga'>Error: 404, Sellist battletag-i ei eksisteeri: $uus <br><a href='http://localhost/overwatch.ee/'>Tagasi</a></p>";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                exit();
            }
            $level = $parsed_json->data->level;
            $rank = $parsed_json->data->competitive->rank;
            $avatar = $parsed_json->data->avatar;
            $pilt = $parsed_json->data->competitive->rank_img;

            $sql = "INSERT INTO `wp_ranking` (battletag, nimi, lvl, rank, avatar, pilt)
  VALUES ('$tag','$nimi', '$level', '$rank', '$avatar', '$pilt')
  ON DUPLICATE KEY UPDATE
  nimi = '$nimi',
  lvl = '$level',
  rank = '$rank',
  avatar = '$avatar',
  pilt = '$pilt'";

            if (mysqli_query($conn, $sql)) {
                echo "uuendatud";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>


<?php get_footer(); ?>
