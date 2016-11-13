<?php
/*
Template Name: UserProfile
*/
?>

<?php get_header(); ?>

<?php
function favfunct()
{
    echo 'test';
}

?>

<script>

    $(document).ready(function () {
        $('#uuenda').click(function (e) {
            alert('uuenda');
        });
    });
</script>

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
                $uus = $_GET['battletag'];

                $query = mysqli_query($conn, "SELECT battletag FROM wp_ranking WHERE battletag='" . $uus . "'");
                // Kui andmebaasis ei ole siis lisab uue ja v6tab api-st info andmebaasi.
                if (mysqli_num_rows($query) == 0) {
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
                                echo "";
                            } else {
                                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                            }
                        }
                    }
                } else {
                    // kuva andmebaasist profiili andmed
                    global $wpdb;
                    $result = $wpdb->get_results("SELECT * FROM wp_ranking where battletag = '$uus';");
                    foreach ($result as $print) {
                        $tag = $print->battletag;
                        $nimi = $print->nimi;
                        $avatar = $print->avatar;
                        $pilt = $print->pilt;
                        $lvl = $print->lvl;
                        $rank = $print->rank;
                    }
                }
            }
            ?>
            <div id="userhead">
                <img src="<?php echo $avatar ?>" alt="">

                <p><a href=""><?php echo $tag ?></a></p>

                <p>(<?php echo $lvl ?>)</p>
                <img class="pilt" src="<?php echo $pilt ?>" alt="">

                <div>
                    <?php echo $rank ?> <br>SKILL RATING
                </div>
                <form action="http://localhost/overwatch.ee/uuenda/" method="GET">
                    <input type="hidden" name="battletag" value="<?php echo $tag ?>" >
                    <input type="submit" name="submit" id="uuenda">
                </form>
            </div>

            <div id="profileline">
                <p>Competitive Stats</p>
            </div>

            <hr>

            <ul id="rig">
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
                <li>
                    <div class="rig-cell">
                        <h3>lol</h3>
                    </div>
                </li>
            </ul>

            <div id="profileline">
                <p>Kõige rohkem mängitud kangelased</p>
            </div>

            <hr>


        </div>
    </div> <!-- /.col -->
</div> <!-- /.row -->
<?php get_footer(); ?>

