<?php
/*
Template Name: UserProfile
*/
?>


<?php get_header(); ?>

<script>

    $(function(){
        // don't cache ajax or content won't be fresh
        $.ajaxSetup ({
            cache: false
        });
        var ajax_load = "<img id='loader' src='http://i.imgur.com/7OhVFhy.gif' alt='loading...' /> ";
        var tag = '?battletag=<?php echo $_GET['battletag']; ?>';
        // load() functions
        var loadUrl = "http://localhost/overwatch.ee/uuenda/" + tag;
        $("#uuenda").click(function(){
            $('#hide').hide();
            $("#message2").html(ajax_load).load(loadUrl);
        });
// end
    });
</script>

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
                if (empty($uus)) {
                    echo "<p class='viga'>Error: 404, Palun sisesta battletag! <br><a href='http://localhost/overwatch.ee/'>Tagasi</a></p>";
                    exit();
                }


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
                                    echo "<div class=\"container\">
                                           <header class=\"page-header\">
                <h1 class=\"page-title\">Error 404 - Sellist battletagi ei eksisteeri: $uus</h1>
            </header><a href='http://localhost/overwatch.ee/''>Tagasi</a>
            </div>
            ";
                                } else {
                                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                }
                                exit();
                            }
                            $lvl = $parsed_json->data->level;
                            $rank = $parsed_json->data->competitive->rank;
                            $avatar = $parsed_json->data->avatar;
                            $pilt = $parsed_json->data->competitive->rank_img;
                            $wins = $parsed_json->data->games->competitive->wins;
                            $lost = $parsed_json->data->games->competitive->lost;
                            $played = $parsed_json->data->games->competitive->played;
                            $playtime = $parsed_json->data->playtime->competitive;


                            $sql = "INSERT INTO `wp_ranking` (battletag, nimi, lvl, rank, avatar, pilt, wins, lost, played, playtime)
 VALUES ('$tag','$nimi', '$lvl', '$rank', '$avatar', '$pilt', '$wins', '$lost', '$played', '$playtime')
 ON DUPLICATE KEY UPDATE
 nimi = '$nimi',
 lvl = '$lvl',
 rank = '$rank',
 avatar = '$avatar',
 pilt = '$pilt',
 wins = '$wins',
 lost = '$lost',
 played = '$played',
 playtime = '$playtime'";


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
                        $wins = $print->wins;
                        $lost = $print->lost;
                        $played = $print->played;
                        $playtime = $print->playtime;
                    }
                }
            }
            ?>
            <?php
            $winrate = number_format(($wins / $played) * 100, 1);
            ?>
<div id="hide">
<div class="container">
    <div class="row">
        <div class="col-sm-12">

            <div id="userhead">
                <img src="<?php echo $avatar ?>" alt="">


                <p><a href=""><?php echo $tag ?></a></p>


                <p>(<?php echo $lvl ?>)</p>
                <img class="pilt" src="<?php echo $pilt ?>" alt="">
                <div id="div1">
                    <?php echo $rank ?> <br>SKILL RATING
                </div>



                    <input type='submit' id="uuenda" value="Uuenda"/>
            </div>


            <div id="profileline">
                <p id="esimene">Competitive Stats</p>
            </div>
            <div id="first">
                <p class="compstats"><?php echo $winrate; ?>% Winrate <br></p>
                <p class="compstats"><?php echo $wins ?> Võitu <br></p>
                <p class="compstats"><?php echo $played ?> Mängu kokku <br></p>
                <p class="compstats"> <?php echo $playtime ?> tundi</p>
            </div>
            <div id="second">
                <p class="compstats"><?php echo $winrate; ?>% Winrate <br></p>
                <p class="compstats"><?php echo $wins ?> Võitu <br></p>
                <p class="compstats"><?php echo $played ?> Mängu kokku <br></p>
                <p class="compstats"> <?php echo $playtime ?> tundi</p>
            </div>
            <div id="third">
                <p class="compstats">150 Kuld medalit<br></p>
                <p class="compstats">131 Hõbe medalit<br></p>
                <p class="compstats">142 Pronks medalit<br></p>
                <p class="compstats">.</p>
            </div>

            <div id="profileline">
                <p id="teine">Kõige rohkem mängitud kangelased</p>
            </div>




    </div> <!-- /.col -->
</div> <!-- /.row -->
<?php get_footer(); ?>
    </div>
<div id="message2"></div>





