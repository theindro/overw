<?php
/*
Template Name: UserProfile
*/
?>

<?php get_header(); ?>

<?php

global $wpdb;
$input_battle_tag = $_GET['battletag'];

// Check if input isnt empty
if (empty($input_battle_tag)) {
    echo "<p class='viga'>Error: 404, Palun sisesta battletag! 
            <br>
            <a href='http://localhost/overwatch.ee/'>Tagasi</a>
          </p>";
    exit();
}

$battle_tag = $wpdb->get_results("SELECT * FROM wp_ranking where battle_tag = '$input_battle_tag'");

// Get data for battle tag from API
if (empty($battle_tag)) {
    $options = array('http' => array('user_agent' => 'custom user agent string'));
    $context = stream_context_create($options);
    $response = @file_get_contents("https://owapi.net/api/v3/u/$input_battle_tag/blob", false, $context);
    $parsed_json = json_decode($response);

    $overall_stats = $parsed_json->eu->stats->competitive->overall_stats;
    $average_stats = $parsed_json->eu->stats->competitive->average_stats;
    $game_stats = $parsed_json->eu->stats->competitive->game_stats;

    $parsed_to_array = json_decode($response, true);

    //$all_heroes_stats = $parsed_to_array['eu']['heroes']['stats']['competitive'];
    //var_dump($all_heroes_stats);

    // Check if battletag exists in API
    $check = $parsed_json->eu;
    if (empty($check)) {
        echo "<div class=\"container\">
            <header class=\"page-header\">
            <h1 class=\"page-title\">Error 404 - Sellist battletagi ei eksisteeri: $input_battle_tag</h1>
            </header>
            <a href='http://localhost/overwatch.ee/''>Tagasi</a>
            </div>
            ";
        exit();
    }

    preg_match("/(.*?)(?=[-])/", $input_battle_tag, $name);

    // Insert API data to db.
    $overall = array(
        'battle_tag' => $input_battle_tag,
        'name' => $name['0'],
        'lvl' => ($overall_stats->prestige * 100 + $overall_stats->level),
        'avatar' => $overall_stats->avatar,
        'rank' => $overall_stats->comprank,
        'tier' => $overall_stats->tier,
        'wins' => $overall_stats->wins,
        'lost' => $overall_stats->losses,
        'ties' => $overall_stats->ties,
        'played' => $overall_stats->games
    );

    $insert_overall = $wpdb->insert('wp_ranking', $overall);
    $battle_tag_id = $wpdb->insert_id;

    $obj_time = gmdate('H:i:s', floor($average_stats->objective_time_avg * 3600));
    $timeonfire = gmdate('H:i:s', floor($average_stats->time_spent_on_fire_avg * 3600));

    // Insert API user stats to db.
    $player_stats = array(
        'battle_tag_id' => $battle_tag_id,
        'melee_final_blows' => $average_stats->melee_final_blows_avg,
        'time_spent_on_fire' => $timeonfire,
        'solo_kills' => $average_stats->solo_kills_avg,
        'objective_time' => $obj_time,
        'objective_kills' => $average_stats->objective_kills_avg,
        'healing_done' => $average_stats->healing_done_avg,
        'final_blows' => $average_stats->final_blows_avg,
        'deaths' => $average_stats->deaths_avg,
        'damage_done' => $average_stats->damage_done_avg,
        'eliminations' => $average_stats->eliminations_avg
    );

    $insert_average_stats = $wpdb->insert('wp_average_stats', $player_stats);

    $player_medals = array(
        'battle_tag_id' => $battle_tag_id,
        'gold' => $game_stats->medals_gold,
        'silver' => $game_stats->medals_silver,
        'bronze' => $game_stats->medals_bronze,
    );

    $insert_medals = $wpdb->insert('wp_medals', $player_medals);

    // Add heroes playtime to database
    $all_heroes_playtime = $parsed_to_array['eu']['heroes']['playtime']['competitive'];

    foreach ($all_heroes_playtime as $hero_name => $playtime) {
        $insert_heroes = $wpdb->insert('wp_heroes', ['hero_name' => $hero_name, 'playtime' => $playtime, 'battle_tag_id' => $battle_tag_id]);
    }

    // Add heroes stats to database
    //$all_heroes_stats = $parsed_to_array['eu']['heroes']['stats']['competitive'];
    //var_dump($all_heroes_stats);
}
// Get current user id
$battle_tag_id = $wpdb->get_var("SELECT battle_tag_id FROM wp_ranking WHERE battle_tag = '$input_battle_tag'");

// Show data from database to given battle tag.
$battle_tag_info = $wpdb->get_results("SELECT * FROM wp_ranking 
                                              LEFT JOIN wp_average_stats USING (battle_tag_id)
                                              LEFT JOIN wp_medals USING (battle_tag_id)
                                              LEFT JOIN wp_ranks USING (tier)
                                              where battle_tag = '$input_battle_tag'");
$user = json_decode(json_encode($battle_tag_info), true);

// Show red color for under 50% winrate and green for higher than 50%
$color = "#000000";
$winrate = number_format(($user[0]['wins'] / $user[0]['played']) * 100, 1);

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
                    <img src="<?= $user[0]['avatar'] ?>" alt="">

                    <p><a href=""><?= $user[0]['battle_tag'] ?></a></p>

                    <p>(<?= $user[0]['lvl'] ?>)</p>
                    <img class="pilt" src="<?= $user[0]['rank_image'] ?>" alt="">
                    <div id="div1">
                        <?= $user[0]['rank'] ?> <br>SKILL RATING
                    </div>

                    <input type='submit' id="uuenda" value="Uuenda"/>
                </div>

                <table class="overall_stats">
                    <tr>
                        <th colspan="5">Overall games</th>
                    </tr>
                    <tr style="background">
                        <td>Winrate: <?php echo "<span style=\"color: $color\">$winrate%</span>" ?></td>
                        <td>Total: <?= $user[0]['played'] ?> </td>
                        <td>Wins: <span style="color:#009c06"><?= $user[0]['wins'] ?> </span></td>
                        <td>Lost: <span style="color:#c60000"><?= $user[0]['lost'] ?> </span></td>
                        <td>Ties: <span style="color:#ff9a3c"><?= $user[0]['ties'] ?> </span></td>
                    </tr>
                    <tr>
                        <th colspan="5">Average stats</th>
                    </tr>
                    <tr>
                        <td>Eliminations: <?= $user[0]['eliminations']; ?><br>
                            <progress title="Your average Eliminations compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(eliminations) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['eliminations']; ?>"></progress>
                        </td>
                        <td>Deaths: <?= $user[0]['deaths'] ?><br>
                            <progress title="Your average Deaths compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(deaths) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['deaths'] ?>"></progress>
                        <td>Damage done: <?= $user[0]['damage_done'] ?>
                            <br>
                            <progress title="Your average Damage compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(damage_done) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['damage_done'] ?>"></progress>
                        </td>
                        <td>Healing done: <?= $user[0]['healing_done'] ?>
                            <br>
                            <progress title="Your average Healing compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(healing_done) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['healing_done'] ?>"></progress>
                        </td>
                        <td>Solo kills: <?= $user[0]['solo_kills'] ?>
                            <br>
                            <progress title="Your average Solo kills compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(solo_kills) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['solo_kills'] ?>"></progress>
                        </td>
                    </tr>
                    <tr>

                        <td>Objective kills: <?= $user[0]['objective_kills'] ?>
                            <br>
                            <progress title="Your average Objective kills compared to other players" class="avgbar"
                                      max="<?= $wpdb->get_var("SELECT MAX(objective_kills) FROM wp_average_stats"); ?>"
                                      value="<?= $user[0]['objective_kills'] ?>"></progress>
                        </td>
                        <td>Objective time: <br><?= $user[0]['objective_time'] ?></td>
                        <td>Time on fire: <br><?= $user[0]['time_spent_on_fire'] ?></td>
                    </tr>
                    <tr>
                        <th colspan="5">Medals</th>
                    </tr>
                    <tr>
                        <td>Gold: <?= $user[0]['gold']; ?></td>
                        <td>Silver: <?= $user[0]['silver']; ?></td>
                        <td>Bronze: <?= $user[0]['bronze']; ?></td>
                        <td></td>
                    </tr>
                </table>

                <div id="profileline">
                    <p id="teine">Most played Heroes</p>
                </div>

                <?php
                $herolist = $wpdb->get_results("SELECT * FROM wp_heroes LEFT JOIN wp_heroesall USING (hero_name) where battle_tag_id = $battle_tag_id ORDER BY playtime DESC limit 10");
                $herolist = json_decode(json_encode($herolist), true);
                ?>
                <table id="hero_playtime" cellspacing="0" width="100%">
                    <tr>
                        <th style="width:7%"></th>
                        <th style="width:10%"></th>
                        <th style="width:73%"></th>
                        <th style="width:10%"></th>
                    </tr>
                    <?php foreach ($herolist as $hero): ?>
                        <tr>
                            <td><img class="avs" src="<?= $hero['image'] ?>"></td>
                            <td><p class="heronames"><?= $hero['hero_name'] ?></p></td>
                            <td>
                                <progress class="bar"
                                          max="<?= $wpdb->get_var("SELECT MAX(playtime) FROM wp_heroes 
                                                                    where battle_tag_id = $battle_tag_id"); ?>"
                                          value="<?= $hero['playtime'] ?>"></progress>
                            </td>
                            <td><p class="heronames"><?= $hero['playtime'] ?> Hours</p></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div id="profileline">
                    <p id="teine">hero stats</p>
                </div>

                <?php $heroes = $wpdb->get_results("SELECT * FROM wp_heroes where battle_tag_id = '$input_battle_tag' ORDER BY percentage DESC LIMIT 3;"); ?>
                <?php foreach ($heroes as $hero) : ?>
                    <div class="herostats">
                        <div class="heropic">
                            <img src="<?php echo $hero->image; ?>" alt="">
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
                <?php endforeach; ?>
                <br>
                <a href="" style="color:black; float:right;">Kuva rohkem...</a>
                <br>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
        <?php get_footer(); ?>
    </div>
    <div id="message2"></div>

    <script>

        $(document).ready(function () {

        });

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
        });

        $(document).ajaxStop(function () {
            window.location.reload();
        });
    </script>



