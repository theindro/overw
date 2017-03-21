<?php
/*
Template Name: Est players leaderboard
*/
get_header();

global $wpdb;
$i = 1;
$players = $wpdb->get_results("SELECT * FROM wp_ranking 
LEFT JOIN wp_ranks USING (tier) 
LEFT JOIN wp_average_stats USING (battle_tag_id) ORDER BY rank DESC
;");
$players = json_decode(json_encode($players), true);
?>

<style>

    #ranking-table td {
        height: 60px !important;
    }

    .btn-season {
        width: 100px;
        display: inline-block;
        margin-left: 10px;
        float: right;
        margin-top: 20px;
    }
</style>

<div class="container">

    <div style="width: 100%; display:inline-block;">
        <h3 class="page-header-text" style="float:left; display:inline-block;">Eesti Ranking</h3>
        <!--
        <a href="season/3"><input type="button" class="btn btn-primary btn-season" value="Season 3"></a>
        <a href="season/2"><input type="button" class="btn btn-primary btn-season" value="Season 2"></a>
        <a href="season/1"><input type="button" class="btn btn-primary btn-season" value="Season 1"></a>
        -->
    </div>

    <table id="ranking_table" style="font-family: 'Roboto';" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th style="width:10px;">#</th>
            <th style="width:10px;">Avatar</th>
            <th>Nimi</th>
            <th style="width:10px;">KDA</th>
            <th style="width:10px;">Level</th>
            <th style="width:10px;">Rank</th>
            <th style="width:10px;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($players as $player): ?>
            <?php if(!empty($player['eliminations'] || $player['deaths'])) : ?>
            <?php $kda = round($player['eliminations'] / $player['deaths'], 1) ?>
            <?php endif; ?>
            <tr>
                <td style="padding-top:20px;"><?= $i++; ?></td>
                <td style="padding:0!important"><img class="avatar" src="<?= $player['avatar'] ?>" alt=""></td>
                <td style="padding-top:20px;"><a style="font-size:16px;"
                        href="../profiil/<?= $player['battle_tag'] ?>"><?= $player['name'] ?></a></td>
                <td style="padding-top:20px;"><?= $kda ?></td>
                <td style="padding-top:20px;"><?= $player['lvl'] ?></td>
                <td style="padding-top:20px;"><?= $player['rank'] ?></td>
                <td style="padding:0!important; "><img class="avatar" src="<?= $player['rank_image'] ?>" alt=""></td>
            </tr>
        <?php endforeach ?>
        </tbody>

    </table>
</div> <!-- /.col -->


<?php get_footer(); ?>

<script>
    $(document).ready(function () {
        $('#ranking_table').dataTable({
            responsive: true,
            "bPaginate": false,
            "columnDefs": [
                { "orderable": false, "targets": [1,6] }
            ]
        });
    });
</script>
