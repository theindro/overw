<?php
/*
Template Name: Tournament iframe link
*/
?>
<?php get_header(); ?>

<?php
global $wpdb;

$tournaments = $wpdb->get_results("SELECT * FROM wp_tournaments");
$tournaments = json_decode(json_encode($tournaments), true);

$current_user = wp_get_current_user();
$tournament_id = $_GET['id'];
if (empty($_GET['id'])) {
    $tournament_id = $wpdb->get_var("SELECT tournament_id FROM wp_tournaments ORDER BY tournament_id DESC");
}

$current_tournament = $wpdb->get_results("SELECT * FROM wp_tournaments where tournament_id = $tournament_id");
$current_tournament = json_decode(json_encode($current_tournament), true);
?>
<div class="container">
    <?php if ($current_user->roles[0] == 'administrator') : ?>
        <div class="AdminBox">

            <h2 style="display:inline-block;">Add new tournament </h2><span
                style="color:red; margin-left:5px; display:inline-block;"> [Admin only]</span>
            <input style="margin-bottom:10px;display:inline-block;" type="text" class="t-name form-control"
                   placeholder="Tournament name">
            <br>
            <input style="margin-bottom:10px; display:inline-block;" type="text" class="t-link form-control"
                   placeholder="iframe link example: challonge.com/4a3oc0p0/module">
            <br>
            <input type="button" class="btn btn-primary add-tournament" value="Sisesta">
        </div>
        <br>
    <?php endif; ?>
    <div id="show-tournament">
        <h3 class="page-header-text" style="float:left; display:inline-block;">
            #<?= $current_tournament[0]['tournament_id']; ?> <?= $current_tournament[0]['name']; ?>
            - <?= $current_tournament[0]['date']; ?></h3>
        <select name="" class="form-control" id="choose-tournament" style="margin-bottom:16px;">
            <?php foreach ($tournaments as $tournament): ?>
                <option
                    value="<?= $tournament['tournament_id'] ?>"
                    <?php if ($tournament_id == $tournament['tournament_id']): ?>
                        selected="selected"
                    <?php endif; ?>>
                    #<?= $tournament['tournament_id'] ?> <?= $tournament['name'] ?> - <?= $tournament['date'] ?>
                </option>
            <?php endforeach ?>
        </select>
        <div class="row">
            <iframe src="https://<?= $current_tournament[0]['link']; ?>" width="100%" height="500" frameborder="0"
                    scrolling="auto" allowtransparency="true"></iframe>
        </div>
    </div>

    <?php get_footer(); ?>

    <script>
        $(document).ready(function () {
            $('.add-tournament').on('click', function () {
                $.post("<?= get_site_url()?>/admin ", {
                    tournament_name: $('.t-name').val(),
                    tournament_link: $('.t-link').val()
                }, function (res) {
                    location.reload();
                });
            });

            $('select').on('change', function (e) {
                var tournament_id = $("#choose-tournament").val();
                window.location.replace('?id=' + tournament_id);
            });
        });
    </script>

