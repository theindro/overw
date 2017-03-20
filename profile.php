<?php
/*
Template Name: UserProfile
*/
?>

<?php get_header(); ?>

<?php

global $wpdb;
$input_battle_tag = trim($_GET['battletag']);
$encoded_battletag = urlencode($input_battle_tag);

// Check if input isnt empty
if (empty($input_battle_tag)) {
    echo "<p class='viga'>Error: 404, Palun sisesta battletag! 
            <br>
            <a href='www.overwatch.ee'>Tagasi</a>
          </p>";
    exit();
}

$battle_tag = $wpdb->get_results("SELECT * FROM wp_ranking where battle_tag = '$input_battle_tag'");

// Get data for battle tag from API
if (empty($battle_tag)) {
    $options = array('http' => array('user_agent' => 'custom user agent string'));
    $context = stream_context_create($options);
    $response = @file_get_contents("https://owapi.net/api/v3/u/$encoded_battletag/blob", false, $context);
    $parsed_json = json_decode($response);

    $overall_stats = $parsed_json->eu->stats->competitive->overall_stats;
    $average_stats = $parsed_json->eu->stats->competitive->average_stats;
    $game_stats = $parsed_json->eu->stats->competitive->game_stats;

    $parsed_to_array = json_decode($response, true);

    // Check if battletag exists in API
    $check = $parsed_json->eu;
    if (empty($check)) {
        echo "<div class=\"container\">
            <header class=\"page-header\">
            <h1 class=\"page-title\">Error 404 - Sellist battletagi ei eksisteeri: $input_battle_tag</h1>
            </header>
            <a href='http://www.overwatch.ee/'>Tagasi</a>
            </div>
            ";
        exit();
    }

    preg_match("/(.*?)(?=[-])/", $input_battle_tag, $name);

    $ip_address = $_SERVER['REMOTE_ADDR'];

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
        'played' => $overall_stats->games,
        'last_updated' => date('Y-m-d H:i:s', current_time('timestamp', 0)),
        'ip_address' => $ip_address
    );

    $insert_overall = $wpdb->insert('wp_ranking', $overall);
    $battle_tag_id = $wpdb->insert_id;

    if (!empty($battle_tag_id)) {

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
        $all_heroes_stats = $parsed_to_array['eu']['heroes']['stats']['competitive'];
        foreach ($all_heroes_stats as $hero_name => $hero_stats) {
            $data = array(
                'battle_tag_id' => $battle_tag_id,
                'hero_name' => $hero_name,
                'objective_time_average' => $hero_stats['average_stats']['objective_time_average'],
                'objective_kills_average' => $hero_stats['average_stats']['objective_kills_average'],
                'deaths_average' => $hero_stats['average_stats']['deaths_average'],
                'eliminations_average' => $hero_stats['average_stats']['eliminations_average'],
                'final_blows_average' => $hero_stats['average_stats']['final_blows_average'],
                'damage_done_average' => $hero_stats['average_stats']['damage_done_average'],
                'healing_done_average' => $hero_stats['average_stats']['healing_done_average'],
                'solo_kills_average' => $hero_stats['average_stats']['solo_kills_average'],
                'weapon_accuracy' => $hero_stats['general_stats']['weapon_accuracy'],
                'eliminations_per_life' => $hero_stats['general_stats']['eliminations_per_life'],
                'games_played' => $hero_stats['general_stats']['games_played'],
                'games_won' => $hero_stats['general_stats']['games_won'],
                'games_lost' => $hero_stats['general_stats']['games_lost'],
            );
            $insert_heroes = $wpdb->insert('wp_hero_avg_stats', $data);
        }
    } else {
        echo 'Andmeid ei saa sisestada';
    }

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
if (!empty($user[0]['wins'] || $user[0]['played'])) {
    $winrate = number_format(($user[0]['wins'] / ($user[0]['played'] - $user[0]['ties'])) * 100, 1);
}
if (($winrate >= 1) && ($winrate <= 49.99))
    $color = "#c60000";
else if (($winrate >= 50) && ($winrate <= 100))
    $color = "#009c06";


$last_updated = $user[0]['last_updated'];
$updated_time = date("Y-m-d H:i:s", strtotime($last_updated) + 0.25 * 3600);
$last_updated_day = date("Y-m-d H:i:s", strtotime($last_updated) + 24 * 3600);


$max_avg = $wpdb->get_results("SELECT MAX(eliminations) AS eliminations, 
                        MAX(deaths) AS deaths, 
                        MAX(damage_done) AS damage_done, 
                        MAX(healing_done) AS healing_done, 
                        MAX(solo_kills) AS solo_kills, 
                        MAX(objective_kills) AS objective_kills FROM wp_average_stats");
$max_avg = json_decode(json_encode($max_avg), true);

$bg = array('rotate1.jpg', 'rotate2.jpg', 'rotate3.jpg', 'rotate4.jpg'); // array of filenames

$i = rand(0, count($bg)-1); // generate random number size of the array
$selectedBg = "$bg[$i]"; // set variable equal to which random filename was chosen
?>
<style type="text/css">
    #userhead{
        background: url("<?= get_site_url() ?>/wp-content/themes/ow/imgs/<?= $selectedBg; ?>") no-repeat;
    }
</style>
<div id="hide">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div id="userhead" <?if ($user[0]['battle_tag'] == 'deathwish-22634') :?> class="userhead-background"<?php endif;?>>
                    <img src="<?= $user[0]['avatar'] ?>" alt="">

                    <p><a href=""><?= $user[0]['battle_tag'] ?></a></p>

                    <p>(<?= $user[0]['lvl'] ?>)</p>
                    <img class="pilt" src="<?= $user[0]['rank_image'] ?>" alt="">
                    <div id="div1">
                        <?= $user[0]['rank'] ?> <br>SKILL RATING
                    </div>


                    <button id="uuenda"
                        <?php if ($updated_time > date('Y-m-d H:i:s', current_time('timestamp', 0))) : ?>
                            title="Profiil on uuendatud vähem kui 15 minutit tagasi." disabled="disabled"
                            style="cursor: not-allowed;">Uuendatud
                        <?php else: ?>
                            >Uuenda
                        <?php endif; ?>

                        <div id="uuenda-loading" style="display:none;"></div>
                    </button>

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
                            <progress title="Your average Eliminations compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['eliminations']; ?>"
                                      value="<?= $user[0]['eliminations']; ?>"></progress>
                        </td>
                        <td>Deaths: <?= $user[0]['deaths'] ?><br>
                            <progress title="Your average Deaths compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['deaths']; ?>"
                                      value="<?= $user[0]['deaths'] ?>"></progress>
                        <td>Damage done: <?= $user[0]['damage_done'] ?>
                            <br>
                            <progress title="Your average Damage compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['damage_done']; ?>"
                                      value="<?= $user[0]['damage_done'] ?>"></progress>
                        </td>
                        <td>Healing done: <?= $user[0]['healing_done'] ?>
                            <br>
                            <progress title="Your average Healing compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['healing_done']; ?>"
                                      value="<?= $user[0]['healing_done'] ?>"></progress>
                        </td>
                        <td>Solo kills: <?= $user[0]['solo_kills'] ?>
                            <br>
                            <progress title="Your average Solo kills compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['solo_kills']; ?>"
                                      value="<?= $user[0]['solo_kills'] ?>"></progress>
                        </td>
                    </tr>
                    <tr>

                        <td>Objective kills: <?= $user[0]['objective_kills'] ?>
                            <br>
                            <progress title="Your average Objective kills compared to other players on overwatch.ee"
                                      class="avgbar"
                                      max="<?= $max_avg[0]['objective_kills']; ?>"
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
                $herolist = $wpdb->get_results("SELECT * FROM wp_heroes LEFT JOIN wp_heroesall USING (hero_name) 
                                                where battle_tag_id = $battle_tag_id 
                                                AND playtime != 0 ORDER BY playtime DESC limit 10");
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
                            <td><p class="heronames"><a
                                        href="../../heroes/hero/?id=<?= $hero['hero_id'] ?>"><?= $hero['hero_name'] ?></a>
                                </p></td>
                            <td>
                                <progress class="bar"
                                          title="You have played <?= $hero['playtime'] ?> hours with <?= $hero['hero_name'] ?>"
                                          max="<?= $wpdb->get_var("SELECT MAX(playtime) FROM wp_heroes 
                                                                    where battle_tag_id = $battle_tag_id"); ?>"
                                          value="<?= $hero['playtime'] ?>"></progress>
                            </td>
                            <td><p class="heroplaytime"><?= round($hero['playtime'], 1) ?> Hours</p></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div id="profileline">
                    <p id="teine">hero stats</p>
                </div>

                <?php $all_hero_stats = $wpdb->get_results("SELECT * FROM wp_hero_avg_stats
                                                                LEFT JOIN wp_heroesall USING (hero_name)
                                                                LEFT JOIN wp_heroes USING (battle_tag_id, hero_name)
                                                                where wp_hero_avg_stats.battle_tag_id = '$battle_tag_id'
                                                                AND playtime != 0 AND wp_hero_avg_stats.games_played > 3
                                                                GROUP BY wp_hero_avg_stats.hero_name
                                                                ORDER BY playtime DESC");
                $heroes = json_decode(json_encode($all_hero_stats), true);
                ?>
                <?php if (empty($heroes)): ?>
                    <span style="margin-bottom:20px; color:grey; padding-left:10px;">Pole piisavalt mängitud, et kuvada infot.</span>
                <?php else: ?>
                    <?php foreach ($heroes as $hero) : ?>
                        <?php
                        $name = $hero['hero_name'];
                        $max = $wpdb->get_results("SELECT MAX(weapon_accuracy) as weapon_accuracy,
                                                            MAX(eliminations_per_life) as eliminations_per_life,
                                                             MAX(damage_done_average) as damage_done_average,
                                                             MAX(final_blows_average) as final_blows_average,
                                                             MAX(healing_done_average) as healing_done_average,
                                                             MAX(solo_kills_average) as solo_kills_average,
                                                             MAX(objective_kills_average) as objective_kills_average,
                                                             MAX(objective_time_average) as objective_time_average
                                                             FROM wp_hero_avg_stats WHERE hero_name = '$name' AND games_played > 3");
                        $max = json_decode(json_encode($max), true);
                        ?>
                        <div class="herostats">
                            <div class="heropic">
                                <img src="<?= $hero['image'] ?>" alt="">
                            </div>
                            <div class="row1">
                                <?php if (!empty($hero['weapon_accuracy'])): ?>
                                    <div class="mainstat">
                                        <div style="font-weight:bold;"><?= $hero['weapon_accuracy'] * 100 ?>%</div>
                                        <div>
                                            <progress class="pbar"
                                                      title="Your accuracy compared to other players on Overwatch.ee"
                                                      max="<?= $max[0]['weapon_accuracy'] ?>"
                                                      value="<?= $hero['weapon_accuracy'] ?>"></progress>
                                        </div>
                                        <div>Accuracy</div>
                                    </div>
                                <?php endif; ?>
                                <div class="mainstat">
                                    <div style="font-weight:bold;"><?= $hero['eliminations_per_life'] ?> </div>
                                    <div>
                                        <progress class="pbar"
                                                  title="Your K/d ratio compared to other players on Overwatch.ee"
                                                  max="<?= $max[0]['eliminations_per_life']; ?>"
                                                  value="<?= $hero['eliminations_per_life'] ?>"></progress>
                                    </div>
                                    <div>K/d ratio</div>
                                </div>
                                <div class="mainstat">
                                    <div style="font-weight:bold;"><?= $hero['damage_done_average'] ?> </div>
                                    <div>
                                        <progress class="pbar"
                                                  title="Your Damage done compared to other players on Overwatch.ee"
                                                  max="<?= $max[0]['damage_done_average'] ?>"
                                                  value="<?= $hero['damage_done_average'] ?>"></progress>
                                    </div>
                                    <div>Damage done</div>
                                </div>
                                <div class="mainstat">
                                    <div
                                        style="font-weight:bold;"><?php if (!empty($hero['games_won']) && !empty($hero['games_played'])) {
                                            echo number_format(($hero['games_won'] / ($hero['games_played'])) * 100, 1);
                                        } else {
                                            echo 'No info';
                                        } ?>
                                        %
                                    </div>
                                    <div>
                                        <progress class="pbar" title="Your Winrate on scale of 100%"
                                                  max="<?= $hero['games_played'] ?>"
                                                  value="<?= $hero['games_won'] ?>"></progress>
                                    </div>
                                    <div>Winrate</div>
                                </div>
                            </div>
                            <div class="row1">
                                <div class="mainstat">
                                    <div style="font-weight:bold;"><?= $hero['final_blows_average'] ?></div>
                                    <div>
                                        <progress class="pbar"
                                                  title="Your final blows compared to other players on Overwatch.ee"
                                                  max="<?= $max[0]['final_blows_average']; ?>"
                                                  value="<?= $hero['final_blows_average'] ?>"></progress>
                                    </div>
                                    <div>final blows</div>
                                </div>
                                <div class="mainstat">
                                    <?php if ($hero['Role'] == 'Support'): ?>
                                        <div style="font-weight:bold;"><?= $hero['healing_done_average'] ?></div>
                                        <div>
                                            <progress class="pbar"
                                                      title="Your healing done compared to other players on Overwatch.ee"
                                                      max="<?= $max[0]['healing_done_average'] ?>"
                                                      value="<?= $hero['healing_done_average'] ?>"></progress>
                                        </div>
                                        <div>Healing</div>
                                    <?php else: ?>
                                        <div style="font-weight:bold;"><?= $hero['solo_kills_average'] ?></div>
                                        <div>
                                            <progress class="pbar"
                                                      title="Your solo kills compared to other players on Overwatch.ee"
                                                      max="<?= $max[0]['solo_kills_average'] ?>"
                                                      value="<?= $hero['solo_kills_average'] ?>"></progress>
                                        </div>
                                        <div>Solo kills</div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($hero['objective_kills_average'])): ?>
                                    <div class="mainstat">
                                        <div style="font-weight:bold;"><?= $hero['objective_kills_average'] ?></div>
                                        <div>
                                            <progress class="pbar"
                                                      title="Your objective kills compared to other players on Overwatch.ee"
                                                      max="<?= $max[0]['objective_kills_average'] ?>"
                                                      value="<?= $hero['objective_kills_average'] ?>"></progress>
                                        </div>
                                        <div>obj. kills</div>
                                    </div>
                                <?php endif; ?>
                                <div class="mainstat">
                                    <div
                                        style="font-weight:bold;"><?= gmdate('H:i:s', floor($hero['objective_time_average'] * 3600)); ?></div>
                                    <div>
                                        <progress class="pbar"
                                                  title="Your objective time compared to other players on Overwatch.ee"
                                                  max="<?= $max[0]['objective_time_average'] ?>"
                                                  value="<?= $hero['objective_time_average'] ?>"></progress>
                                    </div>
                                    <div>Obj. time</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
        <?php get_footer(); ?>
    </div>
</div>


<script>

    $(document).ready(function () {
        $('#uuenda').on('click', function () {
            console.log('wat');
            $('#uuenda-loading').css("display", "inline-block");
            $("#uuenda").attr("disabled", true);
            $.post("<?= get_site_url()?>/update/ ", {
                battle_tag: '<?= $input_battle_tag ?>',
                battle_tag_id: <?= $battle_tag_id ?>
            }, function (res) {
                if (res == 'Ok') {
                    location.reload();
                }
                else {
                    alert("Uuendamine ebaõnnestus!");
                }
            });
        })
    });


</script>

<?php if ($last_updated_day < date('Y-m-d H:i:s', current_time('timestamp', 0))): ?>
    <script>
        $(function () {
            $("#uuenda").trigger("click");
        });
    </script>
<?php endif; ?>

