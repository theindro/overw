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

    .dataTables_filter {
        margin-top:20px;
    }

    #ranking_table_info {
        min-height:250px;
    }

</style>

<div id="ranking-page-header">
    <div class="container">
        <div class="col-sm-12" style="margin-top:100px;">
            <h1 style="font-family:overwatch; color:white; font-size:60px;">Eesti ranking</h1>
        </div>
    </div>
</div>

<div class="container">


    <table id="ranking_table" style="font-family: 'Roboto'; background-color:white;" class="table table-striped table-bordered" cellspacing="0" width="100%">
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

        $('.dataTables_filter input').addClass("form-control");
    });
</script>
