<?php
/*
Template Name: All heroes list
*/
?>

<?php get_header(); ?>

<style>
    #hero_ranking_table_filter {
        margin-top:10px;
    }
    #hero_ranking_table_info {
        min-height: 250px;
    }
</style>

<?php
global $wpdb;
$i = 0;

$season = $_GET['season'];
if (empty($season)) {
    $season = '5';
}


$all_heroes = $wpdb->get_results("SELECT *, sum(wp_heroes.playtime) AS hero_total_playtime
FROM wp_heroesall
  LEFT JOIN wp_heroes USING (hero_name)
  LEFT JOIN wp_ranking USING (battle_tag_id)
  WHERE season = $season
GROUP BY hero_name
ORDER BY hero_total_playtime DESC");

$all_heroes_max_playtime = $wpdb->get_var("SELECT sum(playtime) AS hero_total_playtime FROM wp_heroesall 
LEFT JOIN wp_heroes USING (hero_name) 
GROUP BY hero_name 
ORDER BY hero_total_playtime DESC LIMIT 1;");

?>

<div id="heroes-page-header">
    <div class="container">
        <div class="col-sm-12" style="margin-top:100px;">
            <h1 style="font-family:overwatch; color:white; font-size:60px;">Heroes</h1>
        </div>
    </div>
</div>


<div class="container">
    <div style="float:left; margin-top:5px;">
        <label style="display:inline-block; font-weight:normal" for="season-select">Vali hooaeg:</label>
        <select class="form-control season-select" name="" id=""
                style="margin-top:15px; margin-bottom:10px; width:auto; display:inline-block">
            <?php if ($season == '4'): ?>
                <option selected="selected" value="4">Season 4</option>
            <?php else: ?>
                <option value="4">Season 4</option>
            <?php endif; ?>
            <?php if ($season == '5'): ?>
                <option selected="selected" value="5">Season 5</option>
            <?php else: ?>
                <option value="5">Season 5</option>
            <?php endif; ?>
        </select>
    </div>

    <table id="hero_ranking_table" style="font-family: 'Roboto'; background-color:white;"
           class="table" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th style="width:10px;">#</th>
            <th style="width:10px;">Image</th>
            <th style="width:10px;">Hero name</th>
            <th>Popularity</th>
            <th style="width:100px;">Playtime (hours)</th>
            <th style="width:10px;">Role</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; ?>
        <?php foreach ($all_heroes as $hero): ?>
            <tr>
                <td style="padding-top:20px;"><?= $i++; ?></td>
                <td style="padding:0!important"><a href="../heroes/hero/?id=<?= $hero->hero_id ?>"><img class="avatar" src="<?= $hero->image ?>" alt=""></a></td>
                <td style="padding-top:20px;"><a style="font-size:16px;"
                                                 href="../heroes/hero/?id=<?= $hero->hero_id ?>"><?= $hero->hero_name ?></a>
                </td>
                <td style="padding-top:20px;">
                    <progress title="This hero popularity compared to other heroes." style="margin-top:5px;" class="main-winrate-bar" max="<?= $all_heroes_max_playtime ?>"
                              value="<?= $hero->hero_total_playtime ?>"></progress>
                </td>
                <td style="padding-top:20px;"><?= round($hero->hero_total_playtime, 1) ?></td>
                <td style="padding-top:20px;"><?= $hero->Role ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
        </tfoot>

    </table>
</div> <!-- /.col -->

<script>
    $(document).ready(function () {

        $('#hero_ranking_table').dataTable({
            responsive: true,
            "bPaginate": false
        });

        $('.dataTables_filter input').addClass("form-control");

        $('select').on('change', function (e) {
            var tournament_id = $(".season-select").val();
            window.location.replace('?season=' + tournament_id);
        });
    });
</script>

<?php get_footer(); ?>

