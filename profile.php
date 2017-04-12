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

$bg = array('rotate1.jpg', 'rotate2.jpg', 'rotate3.jpg', 'rotate4.jpg', 'rotate5.jpg', 'rotate6.jpg'); // array of filenames

$i = rand(0, count($bg) - 1); // generate random number size of the array
$selectedBg = "$bg[$i]"; // set variable equal to which random filename was chosen
?>
<style type="text/css">
    #profile-header {
        background: url("https://www.overwatch.ee/wp-content/themes/ow/imgs/<?= $selectedBg ?>") no-repeat center;
        background-position: 20% 30%;
        background-color: #161616;
    }

</style>
<div id="profile-header">
    <div class="container">
        <div class="contain-main-info row">
            <div class="col-sm-2">
                <img style="width:auto; height: 128px; border:solid 1px rgba(255, 255, 255, 0.50);" src="<?= $user[0]['avatar'] ?>"
                     alt="">
            </div>
            <div class="col-sm-6">
                <a href="" class="main-info-tag"
                   data-battletag_id="<?= $user[0]['battle_tag_id'] ?>"><?= $user[0]['battle_tag'] ?></a>
                <br>
                <span class="main-info-level">Level <?= $user[0]['lvl'] ?></span>
            </div>
            <div class="col-sm-2 main-info-tag" style="margin-top:20px; text-align:right"><?= $user[0]['rank'] ?><br>
                <p style="font-size:24px; font-family:overwatch; margin-top:-20px;">Skill Rating</p></div>
            <div class="col-sm-2">
                <img class="rank-image" src="<?= $user[0]['rank_image'] ?>" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2" style="margin-top:9px;">
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
            <div class="col-sm-10">
                <span style="color: #cdcdcd; font-size:12px; font-family:'Open Sans';"><?= $winrate ?>%</span>
                <progress max="<?= $user[0]['played'] ?>" value="<?= $user[0]['wins'] ?>"
                          class="main-winrate-bar"></progress>
                <p style="font-size:12px; margin-top:5px; color:#cdcdcd;">
                    <?= $user[0]['wins'] ?>
                    <span style="color:#87d100; font-weight:bold;">W</span>
                    - <?= $user[0]['lost'] ?>
                    <span style="color:#de0100; font-weight:bold;">L</span>
                    - <?= $user[0]['ties'] ?>
                    <span style="color:#de7102; font-weight:bold;">T</span>

                </p>
            </div>
        </div>

    </div>

</div>

<div class="container">
    <div class="row">
        <div class="col-sm-8">

            <div class="profileline">
                <p class="stats-header-bar">Player average stats</p>
            </div>

            <div class="average-stats col-sm-12" style="clear:both;">

                <div class="col-sm-4"><span><span style="font-weight:bold;"><?= $user[0]['eliminations']; ?></span>
                    <progress title="Your average Eliminations compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['eliminations']; ?>"
                              value="<?= $user[0]['eliminations']; ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Eliminations</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['deaths'] ?></span>
                    <progress title="Your average Deaths compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['deaths']; ?>"
                              value="<?= $user[0]['deaths'] ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Deaths</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['damage_done'] ?></span>
                    <progress title="Your average Damage compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['damage_done']; ?>"
                              value="<?= $user[0]['damage_done'] ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Damage Done</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['healing_done'] ?></span>
                    <progress title="Your average Healing compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['healing_done']; ?>"
                              value="<?= $user[0]['healing_done'] ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Healing Done</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['solo_kills'] ?></span>
                    <progress title="Your average Solo kills compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['solo_kills']; ?>"
                              value="<?= $user[0]['solo_kills'] ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Solo kills</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['objective_kills'] ?></span>
                    <br>
                    <progress title="Your average Objective kills compared to other players on overwatch.ee"
                              class="avgbar"
                              max="<?= $max_avg[0]['objective_kills']; ?>"
                              value="<?= $user[0]['objective_kills'] ?>"></progress>
                    <span style="text-transform: uppercase; font-size:12px;">Objective kills</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['objective_time'] ?> <br></span>
                    <span style="text-transform: uppercase; font-size:12px;">Objective time</span>
                </div>
                <div class="col-sm-4"><span style="font-weight:bold;"><?= $user[0]['time_spent_on_fire'] ?></span> <br>
                    <span style="text-transform: uppercase; font-size:12px;">Time spent on fire</span>
                </div>
            </div>

            <div style="clear:both;">
            </div>


            <div class="profileline">
                <p class="stats-header-bar">hero stats</p>
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
                    if ($hero['Role'] == 'Support') {
                        $role_color = 'support';
                    } elseif ($hero['Role'] == 'Defense') {
                        $role_color = 'defense';
                    } elseif ($hero['Role'] == 'Tank') {
                        $role_color = 'tank';
                    } else {
                        $role_color = 'offense';
                    }
                    ?>
                    <div class="herostats">
                        <a href="../../heroes/hero/?id=<?= $hero['hero_id'] ?>">
                            <div class="heropic">
                                <img style="background: #ffffff;" src="<?= $hero['image'] ?>" alt="">
                            </div>
                        </a>
                        <div class="hero-main-row">
                            <div>
                                <p class="hero-main-name"><?= $hero['hero_name'] ?>
                                    <span class="hero-main-role"> - <?= $hero['Role'] ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="hero-main-support-row">
                                <span style="font-size:12px; margin-top:5px; color:#cdcdcd;">
                                    <?php if (!empty($hero['games_won']) && !empty($hero['games_played'])) {
                                        echo number_format(($hero['games_won'] / ($hero['games_played'])) * 100, 1);
                                    } else {
                                        echo 'NaN';
                                    } ?>
                                    %
                                </span>
                            <progress class="wrate <?= $role_color ?>" max="<?= $hero['games_played'] ?>"
                                      value="<?= $hero['games_won'] ?>"></progress>
                            <p style="font-size:12px; margin-top:5px; color:#cdcdcd;">
                                <?= $hero['games_won'] ?>
                                <span style="color:#87d100; font-weight:bold;">W</span>
                                - <?= $hero['games_lost'] ?>
                                <span style="color:#de0100; font-weight:bold;">L</span>

                            </p>
                        </div>
                        <div class="row1">
                            <?php if (!empty($hero['weapon_accuracy'])): ?>
                                <div class="mainstat">
                                    <div style="font-weight:bold;"><?= $hero['weapon_accuracy'] * 100 ?>%</div>
                                    <div>
                                        <progress class="pbar <?= $role_color ?>"
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
                                    <progress class="pbar <?= $role_color ?>"
                                              title="Your K/d ratio compared to other players on Overwatch.ee"
                                              max="<?= $max[0]['eliminations_per_life']; ?>"
                                              value="<?= $hero['eliminations_per_life'] ?>"></progress>
                                </div>
                                <div>K/d ratio</div>
                            </div>
                            <div class="mainstat">
                                <div style="font-weight:bold;"><?= $hero['damage_done_average'] ?> </div>
                                <div>
                                    <progress class="pbar <?= $role_color ?>"
                                              title="Your Damage done compared to other players on Overwatch.ee"
                                              max="<?= $max[0]['damage_done_average'] ?>"
                                              value="<?= $hero['damage_done_average'] ?>"></progress>
                                </div>
                                <div>Damage done</div>
                            </div>

                        </div>
                        <div class="row1">
                            <div class="mainstat">
                                <div style="font-weight:bold;"><?= $hero['final_blows_average'] ?></div>
                                <div>
                                    <progress class="pbar <?= $role_color ?>"
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
                                        <progress class="pbar <?= $role_color ?>"
                                                  title="Your healing done compared to other players on Overwatch.ee"
                                                  max="<?= $max[0]['healing_done_average'] ?>"
                                                  value="<?= $hero['healing_done_average'] ?>"></progress>
                                    </div>
                                    <div>Healing</div>
                                <?php else: ?>
                                    <div style="font-weight:bold;"><?= $hero['solo_kills_average'] ?></div>
                                    <div>
                                        <progress class="pbar <?= $role_color ?>"
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
                                        <progress class="pbar <?= $role_color ?>"
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
                                    <progress class="pbar <?= $role_color ?>"
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

        <div class="col-sm-4">

            <div class=" profileline">
                <p class="stats-header-bar">Ranking</p>
            </div>

            <?php $total_playtime = $wpdb->get_var("
                  SELECT SUM(playtime) FROM wp_heroes LEFT JOIN wp_heroesall USING (hero_name)
                  WHERE battle_tag_id = $battle_tag_id
                  AND playtime != 0 ORDER BY playtime DESC");
            $total_playtime = json_decode(json_encode($total_playtime), true);
            ?>
            <?php $user_placement = $wpdb->get_results("SELECT *
                    FROM (SELECT t.battle_tag_id,
                    t.battle_tag,
                    @rownum := @rownum + 1 AS position
                    FROM wp_ranking t
                   JOIN (SELECT @rownum := 0) r
                    ORDER BY t.rank DESC) x
                    WHERE x.battle_tag_id = $battle_tag_id");
            $user_placement = json_decode(json_encode($user_placement), true);
            ?>
            <div class="col-sm-12">
                <div class="col-sm-12"><p class="sidebar-text">Rank
                        <strong>#<?= $user_placement[0]['position'] ?></strong></p></div>
                <div class="col-sm-12"><p class="sidebar-text">Winrate: <span
                            style="color:<?= $color ?>; font-weight: bold;"><?= $winrate ?>%</span></p></div>
                <div class="col-sm-12"><p class="sidebar-text"> Playtime: <strong><?= round($total_playtime, 2) ?>
                            Hours</strong></p>
                </div>
            </div>
            <div class=" profileline">
                <p class="stats-header-bar">Medals</p>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-4">
                    <p class="sidebar-text"><img width="20" height="20"
                                                 src="https://overwatch.ee/wp-content/themes/ow/imgs/medals/gold-medal.png"
                                                 alt="">
                        <?= $user[0]['gold']; ?></p></div>
                <div class="col-sm-4"><p class="sidebar-text">
                        <img width="20" height="20"
                             src="https://overwatch.ee/wp-content/themes/ow/imgs/medals/silver-medal.png"
                             alt=""><?= $user[0]['silver']; ?></p></div>
                <div class="col-sm-4"><p class="sidebar-text">
                        <img width="20" height="20"
                             src="https://overwatch.ee/wp-content/themes/ow/imgs/medals/bronze-medal.png"
                             alt=""><?= $user[0]['bronze']; ?></p></div>
            </div>

            <div class=" profileline">
                <p class="stats-header-bar">Most played Heroes</p>
            </div>

            <?php
            $herolist = $wpdb->get_results("SELECT * FROM wp_heroes LEFT JOIN wp_heroesall USING (hero_name) 
                                                where battle_tag_id = $battle_tag_id 
                                                AND playtime != 0 ORDER BY playtime DESC limit 10");
            $herolist = json_decode(json_encode($herolist), true);
            ?>
            <?php foreach ($herolist as $hero): ?>
                <div class="most-played-heroes col-sm-12">
                    <a href="../../heroes/hero/?id=<?= $hero['hero_id'] ?>">
                        <div class="col-sm-3"><img class="avs" src="<?= $hero['image'] ?>"></div>
                    </a>
                    <div class="col-sm-6">
                        <progress class="bar"
                                  title="You have played <?= $hero['playtime'] ?> hours with <?= $hero['hero_name'] ?>"
                                  max="<?= $wpdb->get_var("SELECT MAX(playtime) FROM wp_heroes 
                                                                    where battle_tag_id = $battle_tag_id"); ?>"
                                  value="<?= $hero['playtime'] ?>"></progress>
                    </div>
                    <div class="col-sm-3"><p class="heroplaytime"><?= round($hero['playtime'], 1) ?> Hours</p></div>
                </div>

            <?php endforeach; ?>
        </div>
    </div> <!-- /.row -->
    <?php get_footer(); ?>
</div>


<?php if ($last_updated_day < date('Y-m-d H:i:s', current_time('timestamp', 0))): ?>
    <script>
        $(function () {
            $("#uuenda").trigger("click");
        });
    </script>
<?php endif; ?>
