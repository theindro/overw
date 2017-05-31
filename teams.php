<?php
/*
Template Name: Eesti tiimid
*/

get_header();
?>

<?php

global $wpdb;
$i = 1;
$teams = $wpdb->get_results("SELECT *, avg(rank) AS team_rank FROM wp_teams
  LEFT JOIN wp_ranking USING (team_id)
GROUP BY team_name
ORDER BY team_rank DESC
 ");

$top_heroes = $wpdb->get_results("SELECT * FROM wp_heroes 
LEFT JOIN wp_heroesall USING (hero_name) 
ORDER BY playtime DESC");
$team_players = $wpdb->get_results("SELECT * FROM wp_ranking 
LEFT JOIN wp_teams USING (team_id) 
WHERE wp_ranking.team_id IS NOT NULL 
ORDER BY rank DESC");


?>

<style>

    .team-member {
        display: block;
        color: #2d2d2d;
        background-color: white;
        border: solid 1px #e7e7e7;
        font-family: 'Open Sans';
        margin-bottom: 10px;
    }

    .team-header {
        display: block;
        background-color: #2d2d2d;
        border-radius: 6px;
        color: white;
        margin-bottom: 10px;
    }

    .team-name-header {
        font-size: 42px;
        font-family: overwatch2;
        margin-top: 20px;
    }

</style>

<div id="teams-page-header">
    <div class="container">
        <div class="col-sm-12" style="margin-top:100px;">
            <h1 style="font-family:overwatch; color:white; font-size:60px;">Eesti tiimid</h1>
        </div>
    </div>
</div>

<?php $current_user = wp_get_current_user(); ?>

<div class="container" style="margin-top:20px; min-height:400px;">
    <?php if ($current_user->roles[0] == 'administrator') : ?>
        <input type="button" class="btn btn-primary new-team" value="Add new team" style="margin-bottom:10px;">
    <?php endif; ?>

    <?php $r = 1; ?>
    <?php foreach ($teams as $team): ?>
        <div class="row">
            <div class="col-sm-12 team-header">
                <div class="col-sm-1"><img style="padding:10px; margin-left:-30px;" height="100"
                                           src="<?= $team->team_logo ?>" alt=""></div>
                <div class="col-sm-8"><p class="team-name-header"><?= $team->team_name ?></p></div>

                <div class="col-sm-2" style="margin-top:20px; text-align:center;"><span
                        class="team-name-header"><?= round($team->team_rank, 0) ?></span>
                    <br>
                    <p style="margin-top:-10px; font-size:12px;">AVERAGE SR</p>
                </div>

                <div class="col-sm-1"><p class="team-name-header">#<?= $r++; ?></p></div>
            </div>
        </div>

        <?php if ($current_user->roles[0] == 'administrator') : ?>
            <input type="button" class="btn btn-success open_modal" data-team_id="<?= $team->team_id ?>"
                   style="margin-bottom:10px;" value="Add player to this team">

        <?php endif; ?>
        <div class="row">
            <div class="col-sm-12 ">
                <?php foreach ($team_players as $player): ?>
                    <?php if ($player->team_id == $team->team_id) : ?>
                        <?php
                        if (!empty($player->wins || $player->played)) {
                            $winrate = number_format(($player->wins / ($player->played - $player->ties)) * 100, 1);
                        }
                        ?>
                        <div class="col-sm-4 team-member">
                            <div class="row">

                                <div class="col-sm-3"><img style="padding:10px; margin-left:-15px;" width="100"
                                                           height="100"
                                                           src="<?= $player->avatar ?>" alt=""></div>
                                <div class="col-sm-9" style="margin-top:20px;"><strong><a
                                            href="../profiil/<?= $player->battle_tag ?>"><?= $player->name ?></a></strong>
                                </div>
                                <div class="col-sm-3">Skill: <strong><?= $player->rank ?></strong></div>
                                <div class="col-sm-3">Level: <strong><?= $player->lvl ?></strong></div>
                                <div class="col-sm-3">Winrate: <strong><?= $winrate ?>%</strong></div>
                            </div>
                            <div class="col-sm-12" style="margin-left:-15px;"><strong>Most played heroes</strong></div>
                            <div class="row" style="padding-bottom:10px;">
                                <?php $i = 0; ?>
                                <?php foreach ($top_heroes as $hero): ?>
                                    <?php if ($player->battle_tag_id == $hero->battle_tag_id) : ?>
                                        <div class="col-sm-4">
                                            <div class="col-sm-2">
                                                <img src="<?= $hero->image ?>" height="40" width="40" alt=""
                                                     style="border-radius: 100%;"></div>
                                            <div class="col-sm-8"><a
                                                    href="../heroes/hero/?id=<?= $hero->hero_id ?>"><?= $hero->hero_name ?></a>
                                            </div>
                                        </div>
                                        <?php if (++$i > 2) break; ?>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </div>
                            <?php if ($current_user->roles[0] == 'administrator') : ?>
                                <div class="row"><input type="button"
                                                        class="btn btn-danger btn-xs delete_player pull-right"
                                                        value="Remove" data-player_id="<?= $player->battle_tag_id ?>">
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endforeach ?>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add player</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control player_name" placeholder="BattleTag-21234">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-player" data-dismiss="modal">Save</button>
            </div>
        </div>

    </div>
</div>


<!-- Modal -->
<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add new team</h4>
            </div>
            <div class="modal-body">
                <p>Team name</p>
                <input type="text" class="form-control team_name" placeholder="team name">
                <p>Logo</p>
                <input type="text" class="form-control team_logo" placeholder="logo link">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-team">Save</button>
            </div>
        </div>

    </div>
</div>


<?php get_footer(); ?>

<script>
    $(document).ready(function () {

        $('.open_modal').on('click', function () {
            $('#myModal').modal('show');
            var team_id = $(this).data('team_id');
            $('.save-player').attr('data-team_id', team_id);
        });

        $('.save-player').on('click', function () {
            console.log('wat');
            var player_battletag = $('.player_name').val();
            var team_id = $(this).data('team_id');
            $.post(ajaxurl, {
                action: 'add_player_to_team',
                data: {battletag: player_battletag, team_id: team_id}
            }, function (res) {
                if (res == 'Ok') {
                    location.reload();
                } else {
                    alert("Kasutaja lisamine tiimi ebaõnnestus");
                }
            });
        });


        $('.delete_player').on('click', function () {
            var battletag_id = $(this).data('player_id');

            $.post(ajaxurl, {
                action: 'remove_player_from_team',
                battletag_id: battletag_id
            }, function (res) {
                if (res == 'Ok') {
                    location.reload();
                } else {
                    alert("Kasutaja eemaldamine ebaõnnestus");
                }
            });
        });


        $('.new-team').on('click', function () {
            console.log('asd');
            $('#myModal2').modal('show');
        });


        $('.save-team').on('click', function () {
            var team_name = $('.team_name').val();
            var team_logo = $('.team_logo').val();

            $.post(ajaxurl, {
                action: 'add_new_team',
                data: {team_name: team_name, team_logo: team_logo}
            }, function (res) {
                if (res == 'Ok') {
                    location.reload();
                } else {
                    alert("Kasutaja eemaldamine ebaõnnestus");
                }
            });
        });

    });
</script>

