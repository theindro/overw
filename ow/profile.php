<?php
/*
Template Name: UserProfile
*/
?>
<?php get_header(); ?>

<div class="container">
    <div class="row">

        <div class="col-sm-12">
            <?php
            if (isset($_GET['submit'])) {
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "overwatch";

                // Create connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                global $wpdb;
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
                            echo "";
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                        }

                    }
                }
                $username = $_GET['nimi'];
                global $wpdb;
                $result = $wpdb->get_results("SELECT * FROM wp_ranking where battletag = '$username';");
                foreach ($result as $print) {
                    $tag = $print->battletag;
                    $nimi = $print->nimi;
                    $avatar = $print->avatar;
                    $pilt = $print->pilt;
                    $lvl = $print->lvl;
                    $rank = $print->rank;
                }
            }
            ?>
            <div id="userhead">
                <img src="<?php echo $avatar ?>" alt="">

                <p><a href=""><?php echo $tag ?></a></p>

                <p>(<?php echo $level ?>)</p>
                <img class="pilt" src="<?php echo $pilt ?>" alt="">

                <div>
                    <?php echo $rank ?> <br>SKILL RATING
                </div>
            </div>
            <div id="profileline">
                <p>Competitive Stats</p>
            </div>
            <hr>
            <div id="cards">
                <div class="card">asd</div>
                <div class="card2">asd</div>
                <div class="card3">asd</div>
            </div>
        </div>
    </div> <!-- /.col -->
</div> <!-- /.row -->
<?php get_footer(); ?>

