<?php
/*
Template Name: Heroes list
*/
?>

<?php get_header(); ?>

    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div class="lisa">
                <?php
                if (isset($_POST['submit'])) {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "overwatch";

                    // Create connection
                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    global $wpdb;
                    $tablename = $wpdb->prefix . 'ranking';
                    $data = array(
                        'battletag' => $_POST['battletag'],
                    );
                    $wpdb->insert($tablename, $data);
                    $uus = $_POST['battletag'];
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
                                    echo "Kasutaja on lisatud või uuendatud edetabelis!" . "<br><br>";
                                } else {
                                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                }

                            }
                        }
                }
                ?>

                    <form action="" method="post" id="inputform">
                        <p>Ei leia enda nime listist? Kirjutage allolevasse vormi oma battletag.</p>
                        <input class="lisakasutaja" placeholder="BattleTag-2413" name="battletag" type="text">
                        <input type="submit" name="submit" class="nupplisa">
                        <p>Loevad ka suured ja väiksed tähed ning kasutage '#' asemel '-'</p>
                    </form>
                </div>

                <?php
                echo '<br>';
                echo '<table id="rank" class="rank">';
                echo '<tr><th>Avatar</th><th>Nimi</th><th>Level</th><th>Rank</th><th></th></tr>';
                global $wpdb;
                $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC;");
                foreach ($result as $print) {

                    echo ' <td><img class="avatar" src="' . $print->avatar . '" alt=""></td>';
                    echo '<td class="tabl">' . $print->nimi . '</td>';
                    echo ' <td class="tabl">' . $print->lvl . '</td>';
                    echo '<td class="tabl";>' . $print->rank . '</td>';
                    echo '<td><img class="avatar" src="' . $print->pilt . '" alt=""></td></tr>';
                }
                echo '</table>';
                ?>

                <br>
                <br>
                <br>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
    </div>
<?php get_footer(); ?>