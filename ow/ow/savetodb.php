<?php
/*
Template Name: savetodbscript
*/
?>
<?php get_header(); ?>

<div class="container">
    <div class="row">

        <div class="col-sm-12">
            <?php

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "overwatch";

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            global $wpdb;
            $result = $wpdb->get_results("SELECT battletag FROM wp_ranking ");
            foreach ($result as $print) {
                $tag = $print->battletag;

                $pages = array("https://api.lootbox.eu/pc/eu/$tag/profile");

                foreach ($pages as $page) {
                    ini_set('max_execution_time', 300);
                    $html = file_get_contents($page);
                    $parsed_json = json_decode($html);

                    $nimi = $parsed_json->data->username;
                    $level = $parsed_json->data->level;
                    $rank = $parsed_json->data->competitive->rank;
                    $avatar = $parsed_json->data->avatar;
                    $pilt = $parsed_json->data->competitive->rank_img;

                    echo "Name: " . $nimi . '<br>';
                    echo "Level: " . $level . '<br>';
                    echo "Rank: " .$rank . '<br>';
                    echo "Avatar: " .$avatar . '<br>';
                    echo "Rankimg: " .$pilt . '<br>';


                    $sql = "INSERT INTO `wp_ranking` (battletag, nimi, lvl, rank, avatar, pilt)
  VALUES ('$tag','$nimi', '$level', '$rank', '$avatar', '$pilt')
  ON DUPLICATE KEY UPDATE
  nimi = '$nimi',
  lvl = '$level',
  rank = '$rank',
  avatar = '$avatar',
  pilt = '$pilt'";

                    if (mysqli_query($conn, $sql)) {
                        echo "New record have been created or updated successfully" . "<br><br>";
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    }

                }
            }
            ?>
        </div> <!-- /.col -->

    </div> <!-- /.row -->
</div>
<?php get_footer(); ?>

