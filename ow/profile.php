<?php
/*
Template Name: UserProfile
*/
?>


<?php get_header(); ?>

<script>

    $(function () {
        // don't cache ajax or content won't be fresh
        $.ajaxSetup({
            cache: false
        });
        var ajax_load = "<img id='loader' src='http://i.imgur.com/7OhVFhy.gif' alt='loading...' /> ";
        var tag = '?battletag=<?php echo $_GET['battletag']; ?>';
        // load() functions
        var loadUrl = "http://localhost/overwatch.ee/uuenda/" + tag;
        $("#uuenda").click(function () {
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

                $pages2 = array("https://api.lootbox.eu/pc/eu/$tag/competitive/allHeroes/");

                foreach ($pages2 as $page2) {
                    ini_set('max_execution_time', 300);
                    $html = @file_get_contents($page2);
                    $parsed_json = json_decode($html);


                    $gmedals = 'Medals-Gold';
                    $smedals = 'Medals-Silver';
                    $bmedals = 'Medals-Bronze';
                    $elimavg = 'Eliminations-Average';
                    $damageavg = 'DamageDone-Average';
                    $objavg = 'ObjectiveTime-Average';
                    $deathavg = 'Deaths-Average';


                    $gold = $parsed_json->$gmedals;
                    $silver = $parsed_json->$smedals;
                    $bronze = $parsed_json->$bmedals;
                    $elims = $parsed_json->$elimavg;
                    $deaths = $parsed_json->$deathavg;
                    $objtime = $parsed_json->$objavg;
                    $damage = $parsed_json->$damageavg;
                }

                $sql = "INSERT INTO `wp_ranking` (battletag, nimi, lvl, rank, avatar, pilt, wins, lost, played, playtime, goldmedal, silvermedal, bronzemedal, elims, deaths, objtime, damage)
 VALUES ('$tag','$nimi', '$lvl', '$rank', '$avatar', '$pilt', '$wins', '$lost', '$played', '$playtime', '$gold', '$silver', '$bronze', '$elims', '$deaths', '$objtime', '$damage')
 ON DUPLICATE KEY UPDATE
 nimi = '$nimi',
 lvl = '$lvl',
 rank = '$rank',
 avatar = '$avatar',
 pilt = '$pilt',
 wins = '$wins',
 lost = '$lost',
 played = '$played',
 playtime = '$playtime',
 goldmedal = '$gold',
   silvermedal = '$silver',
    bronzemedal = '$bronze',
    elims = '$elims',
     deaths = '$deaths',
     objtime = '$objtime',
     damage = '$damage'";


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

            $gold = $print->goldmedal;
            $silver = $print->silvermedal;
            $bronze = $print->bronzemedal;

            $elims = $print->elims;
            $deaths = $print->deaths;
            $objtime = $print->objtime;
            $damage = $print->damage;

        }
    }
}
?>
<?php
$color = "#000000";

$winrate = number_format(($wins / $played) * 100, 1);

if (($winrate >= 1) && ($winrate <= 49.99))
    $color = "#FF0000";
else if (($winrate >= 50) && ($winrate <= 100))
    $color = "#00d610";
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
                    <p id="esimene">Competitive Stats <select id="season">
                            <option value="Season3" class="seasons">Season 3</option>
                            <option value="Season2" class="seasons">Season 2</option>
                            <option value="Season1" class="seasons">Season 1</option>
                        </select></p>
                </div>

                <table style="width:100%;" id="statistika">
                    <tr>
                        <th style="width:33%;">Overall</th>
                        <th style="width:33%;">Average</th>
                        <th style="width:33%;">Medals</th>
                    </tr>
                    <tr>
                        <td>Winrate: <?php echo "<span style=\"color: $color\">$winrate%</span>" ?></td>
                        <td>Eliminations: <?php echo $elims; ?></td>
                        <td>Gold: <?php echo $gold; ?></td>
                    </tr>
                    <tr>
                        <td>Wins: <?php echo $wins ?> games</td>
                        <td>Deaths: <?php echo $deaths; ?></td>
                        <td>Silver: <?php echo $silver; ?></td>
                    </tr>
                    <tr>
                        <td>Total: <?php echo $played ?> games</td>
                        <td>Objectime time: <?php echo $objtime; ?></td>
                        <td>Bronze: <?php echo $bronze; ?></td>
                    </tr>
                    <tr>
                        <td>Playtime: <?php echo $playtime ?> hours</td>
                        <td>Damage done: <?php echo $damage; ?></td>
                    </tr>
                </table>

                <div id="profileline">
                    <p id="teine">hero stats</p>
                </div>

                <div class="herostats">
                    <div class="heropic">
                        <img src="https://blzgdapipro-a.akamaihd.net/game/heroes/small/0x02E0000000000029.png" alt="">
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">20%</div>
                            <div>
                                <progress class="pbar" max="100" value="20"></progress>
                            </div>
                            <div>Accuracy</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1.4 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="7"></progress>
                            </div>
                            <div>K/d ratio</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1745 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="62"></progress>
                            </div>
                            <div>Damage</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.01 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="80"></progress>
                            </div>
                            <div>Crits</div>
                        </div>
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.2 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="86"></progress>
                            </div>
                            <div>final blows</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="0"></progress>
                            </div>
                            <div>Healing</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0.5 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="85"></progress>
                            </div>
                            <div>obj. kills</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">6.25 seconds</div>
                            <div>
                                <progress class="pbar" max="100" value="73"></progress>
                            </div>
                            <div>obj. time</div>
                        </div>
                    </div>
                </div>

                <div class="herostats">
                    <div class="heropic">
                        <img src="https://blzgdapipro-a.akamaihd.net/game/heroes/small/0x02E0000000000042.png" alt="">
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">20%</div>
                            <div>
                                <progress class="pbar" max="100" value="20"></progress>
                            </div>
                            <div>Accuracy</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1.4 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="7"></progress>
                            </div>
                            <div>K/d ratio</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1745 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="62"></progress>
                            </div>
                            <div>Damage</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.01 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="80"></progress>
                            </div>
                            <div>Crits</div>
                        </div>
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.2 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="86"></progress>
                            </div>
                            <div>final blows</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="0"></progress>
                            </div>
                            <div>Healing</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0.5 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="85"></progress>
                            </div>
                            <div>obj. kills</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">6.25 seconds</div>
                            <div>
                                <progress class="pbar" max="100" value="73"></progress>
                            </div>
                            <div>obj. time</div>
                        </div>
                    </div>
                </div>

                <div class="herostats">
                    <div class="heropic">
                        <img src="https://blzgdapipro-a.akamaihd.net/game/heroes/small/0x02E0000000000004.png" alt="">
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">20%</div>
                            <div>
                                <progress class="pbar" max="100" value="20"></progress>
                            </div>
                            <div>Accuracy</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1.4 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="7"></progress>
                            </div>
                            <div>K/d ratio</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">1745 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="62"></progress>
                            </div>
                            <div>Damage</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.01 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="80"></progress>
                            </div>
                            <div>Crits</div>
                        </div>
                    </div>
                    <div class="row1">
                        <div class="mainstat">
                            <div style="font-weight:bold;">2.2 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="86"></progress>
                            </div>
                            <div>final blows</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="0"></progress>
                            </div>
                            <div>Healing</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">0.5 <label style="color:grey; text-transform: none;">per
                                    min</label></div>
                            <div>
                                <progress class="pbar" max="100" value="85"></progress>
                            </div>
                            <div>obj. kills</div>
                        </div>
                        <div class="mainstat">
                            <div style="font-weight:bold;">6.25 seconds</div>
                            <div>
                                <progress class="pbar" max="100" value="73"></progress>
                            </div>
                            <div>obj. time</div>
                        </div>
                    </div>
                </div>

                <br>
                <a href="" style="color:black; float:right;">Kuva rohkem...</a>
                <br>

                <div id="profileline">
                    <p id="teine">Most played Heroes</p>
                </div>

                <div id="mainblock">
                    <div id="avatars">
                        <?php
                        $data = mysqli_query($conn, "SELECT * FROM wp_heroes where tag = '$tag' ORDER BY percentage DESC limit 10 ");
                        while ($info = mysqli_fetch_array($data))
                            echo '<img class="avs" src="' . $info['image'] . '"> <br>';
                        ?>
                    </div>
                    <div id="name">
                        <?php
                        $data = mysqli_query($conn, "SELECT * FROM wp_heroes where tag = '$tag' ORDER BY percentage DESC limit 10 ");
                        while ($info = mysqli_fetch_array($data))
                            echo '<div class="roww"><p class="heronames">' . $info['name'] . '</p> </div>';
                        ?>
                    </div>
                    <div id="percentage">
                        <?php
                        $data = mysqli_query($conn, "SELECT * FROM wp_heroes where tag = '$tag' ORDER BY percentage DESC limit 10 ");
                        while ($info = mysqli_fetch_array($data))
                            echo '<div class="roww"><progress class="bar" max="1" value="' . $info['percentage'] . '"></progress></div> ';
                        ?>
                    </div>
                    <div id="hours">
                        <?php
                        $data = mysqli_query($conn, "SELECT * FROM wp_heroes where tag = '$tag' ORDER BY percentage DESC limit 10 ");
                        while ($info = mysqli_fetch_array($data))
                            echo '<div class="roww"><p class="heronames">' . $info['playtime'] . '</p> </div>';
                        ?>
                    </div>
                </div>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
        <?php get_footer(); ?>
    </div>
    <div id="message2"></div>





