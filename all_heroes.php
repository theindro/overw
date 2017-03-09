<?php
/*
Template Name: All heroes list
*/
?>

<?php get_header(); ?>
<?php
global $wpdb;
$i = 0;
$offense_heroes = $wpdb->get_results("SELECT * FROM wp_heroesall WHERE role = 'Offense' ORDER BY Role DESC;");
$defense_heroes = $wpdb->get_results("SELECT * FROM wp_heroesall WHERE role = 'Defense' ORDER BY Role DESC;");
$tank_heroes = $wpdb->get_results("SELECT * FROM wp_heroesall WHERE role = 'Tank' ORDER BY Role DESC;");
$support_heroes = $wpdb->get_results("SELECT * FROM wp_heroesall WHERE role = 'Support' ORDER BY Role DESC;")


?>
    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div id="filterrank">
                    <h3 class="page-header-text">Heroes</h3>
                </div>
                <div id="hero_list_container">
                    <ul class="herolist">
                        <h3 class="hero-header-text" style="color:#c60000;">Offense</h3>
                        <?php foreach ($offense_heroes as $offense_hero): ?>
                            <li class="listt"><a href="/hero/?id=<?= $offense_hero->hero_id ?>"><img class="avatar2"
                                                                                                     src="<?= $offense_hero->image ?>"
                                                                                                     alt=""></a></li>
                        <?php endforeach ?>
                    </ul>

                    <ul class="herolist">
                        <h3 class="hero-header-text" style="color:#ff9a3c;">Defense</h3>
                        <?php foreach ($defense_heroes as $defense_hero): ?>
                            <li class="listt"><a href="/hero/?id=<?= $defense_hero->hero_id ?>"><img class="avatar2"
                                                                                                     src="<?= $defense_hero->image ?>"
                                                                                                     alt=""> </a></li>
                        <?php endforeach ?>
                    </ul>
                    <ul class="herolist">
                        <h3 class="hero-header-text" style="color:#4ab549;">Tank</h3>
                        <?php foreach ($tank_heroes as $tank_hero): ?>
                            <li class="listt"><a href="/hero/?id=<?= $tank_hero->hero_id ?>"><img class="avatar2"
                                                                                                  src="<?= $tank_hero->image ?>"
                                                                                                  alt=""> </a></li>
                        <?php endforeach ?>
                    </ul>
                    <ul class="herolist">
                        <h3 class="hero-header-text" style="color:#337ab7;">Support</h3>
                        <?php foreach ($support_heroes as $support_hero): ?>
                            <li class="listt"><a href="/hero/?id=<?= $support_hero->hero_id ?>"><img class="avatar2"
                                                                                                     src="<?= $support_hero->image ?>"
                                                                                                     alt=""></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
    </div>
<?php get_footer(); ?>