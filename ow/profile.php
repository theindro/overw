<?php
/*
Template Name: UserProfile
*/
?>
<?php get_header(); ?>

<div class="container">
    <div class="row">

        <div class="col-sm-12">

            <?php
            if (isset($_GET['submit'])) {
                $username = $_GET['nimi'];
                global $wpdb;
                $result = $wpdb->get_results("SELECT * FROM wp_ranking where battletag = '$username';");
                foreach ($result as $print) {
                    $tag = $print->battletag;
                    $nimi = $print->nimi;
                    $avatar = $print->avatar;
                    $pilt = $print->pilt;
                    $lvl = $print->lvl;
                    $rank = $print->rank;
                }
            } else {
                echo 'Sisestatud nimi ei ole andmebaasis';
            }
            ?>
            <div id="userhead">
                <img src="<?php echo $avatar ?>" alt="">
                <p><a href=""><?php echo $tag ?></a></p>
                <p>(<?php echo $lvl ?>)</p>
                <img class="pilt" src="<?php echo $pilt ?>" alt="">
                <div>
                    <?php echo $rank ?> <br>SKILL RATING
                </div>
            </div>
        </div>
    </div> <!-- /.col -->
</div> <!-- /.row -->
<?php get_footer(); ?>

