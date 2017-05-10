<?php
/*
Template Name: Single hero page
*/
?>
<?php get_header(); ?>

<?php
global $wpdb;


$hero_id = $_GET['id'];

if (empty($_GET['id'])) {
    $hero_id = 8;
}

$hero = $wpdb->get_results("SELECT * FROM wp_heroesall where hero_id = $hero_id");
$hero = json_decode(json_encode($hero), true);
$players = $wpdb->get_results("SELECT *,wp_heroes.playtime as hero_time_played FROM wp_heroesall
                                LEFT JOIN wp_heroes using(hero_name)
                                LEFT JOIN wp_ranking using(battle_tag_id)
                                LEFT JOIN wp_hero_avg_stats using(battle_tag_id, hero_name)
                                where hero_id = $hero_id 
                                AND battle_tag_id != 0 
                                AND damage_done_average != 0
                                AND wp_heroes.playtime > 0.2
                                 ORDER BY hero_time_played DESC");

$players = json_decode(json_encode($players), true);
?>

<style>


    #single_hero_table_wrapper {
        padding-bottom: 100px;
        margin-top:10px;
    }

    #single_hero_table_info {
        min-height:200px;
    }
</style>

<div id="heroes-page-header">
    <div class="container">
        <div class="col-sm-12" style="margin-top:100px;">
            <div class="col-sm-2">
                <img id="hero-image" src="<?= $hero[0]['image'] ?>" alt="">
            </div>
            <div class="hero-page-header-text col-sm-10">
                <?= $hero[0]['hero_name'] ?>
                <span style="font-size:30px; color:grey;">
                    <?= $hero[0]['Role'] ?>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="container">

    <table id="single_hero_table" style="font-family:'Roboto'; background-color:white;"
           class="table" cellspacing="0" width="100%">
        <thead>
        <tr style="font-size:12px;">
            <th class="hidden">Avatar</th>
            <th>Avatar</th>
            <th>Name</th>
            <th>Playtime (hours)</th>
            <th>Accuracy</th>
            <th>Average eliminations</th>
            <th>Average deaths</th>
            <th>Average damage done</th>
            <?php if ($hero[0]['Role'] == 'Support'): ?>
                <th>Healing done</th>
            <?php else: ?>
                <th>Average solo kills</th>
            <?php endif; ?>
            <th>Winrate</th>
        </tr>
        </thead>
        <tbody style="font-size: 12px;">
        <?php foreach ($players as $player_hero): ?>
            <?php if (!empty($player_hero['battle_tag'])): ?>
                <?php
                // Show red color for under 50% winrate and green for higher than 50%
                $color = "#000000";
                $won = $player_hero['games_won'];
                $lost = $player_hero['games_lost'];
                $games_played = ($won + $lost);

                if (empty($lost)) {
                    $winrate = '100';
                } elseif (empty($won)) {
                    $winrate = '0';
                } else {
                    $winrate = number_format(($won / $games_played) * 100, 1);
                }

                if (($winrate >= 1) && ($winrate <= 49.99))
                    $color = "#c60000";
                else if (($winrate >= 50) && ($winrate <= 100))
                    $color = "#009c06;";
                else
                    $color = "grey";
                ?>
                <tr>
                    <td class="hidden"><?= round($player_hero['hero_time_played'], 1) ?></td>
                    <td style="padding:0!important"><img class="avatar" src="<?= $player_hero['avatar'] ?>" alt=""></td>
                    <td style="padding-top:20px;"><a style="font-size:16px;"
                                                     href="../../profiil/<?= $player_hero['battle_tag'] ?>"><?= $player_hero['name'] ?></a>
                    </td>
                    <td style="padding-top:20px;"><?= round($player_hero['hero_time_played'], 1) ?></td>
                    <td style="padding-top:20px;"><?= $player_hero['weapon_accuracy'] * 100 ?>%</td>
                    <td style="padding-top:20px;"><?= $player_hero['eliminations_average'] ?></td>
                    <td style="padding-top:20px;"><?= $player_hero['deaths_average'] ?></td>
                    <td style="padding-top:20px;"><?= $player_hero['damage_done_average'] ?></td>
                    <?php if ($player_hero['Role'] == 'Support') : ?>
                        <td style="padding-top:20px;"><?= $player_hero['healing_done_average'] ?></td>
                    <?php else: ?>
                        <td style="padding-top:20px;"><?= $player_hero['solo_kills_average'] ?></td>
                    <?php endif; ?>
                    <td style="padding-top:20px;"><span style="color:<?= $color ?>; font-weight:bold;"><?= $winrate ?>
                            %</span> <progress max="100" value="<?= $winrate ?>" class="winratebar"></progress></td>
                </tr>
            <?php endif; ?>
        <?php endforeach ?>
        </tbody>

    </table>
</div> <!-- /.col -->


<?php get_footer(); ?>

<script>
    $(document).ready(function () {
        $('#single_hero_table').dataTable({
            responsive: true,
            "bPaginate": false,
            "order": [[0, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [1]}
            ]
        });

        $('.dataTables_filter input').addClass("form-control");
    });
</script>

