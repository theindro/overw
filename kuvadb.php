<?php
/*
Template Name: Kuva andmebaasist andmeid ajaxiga
*/
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php
            $uus = $_GET['battletag'];
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM wp_ranking where battletag = '$uus';");
            foreach ($result as $print) {
                $tag = $print->battletag;
                $nimi = $print->nimi;
                $avatar = $print->avatar;
                $pilt = $print->pilt;
                $lvl = $print->lvl;
                $rank = $print->rank;
                $wins = $print->wins;
                $lost = $print->lost;
                $played = $print->played;
                $playtime = $print->playtime;
            }

            ?>
            <?php
            $winrate = number_format(($wins / $played) * 100, 1);
            ?>

            <div id="userhead">
                <img src="<?php echo $avatar ?>" alt="">


                <p><a href=""><?php echo $tag ?></a></p>


                <p>(<?php echo $lvl ?>)</p>
                <img class="pilt" src="<?php echo $pilt ?>" alt="">
                <div id="div1">
                    <?php echo $rank ?> <br>SKILL RATING
                </div>


                <form action="" method="POST">
                    <input type="hidden" name="battletag" value="<?php echo $tag ?>">
                    <input type="submit" name="submit" id="uuenda" value="Uuenda">
                </form>
            </div>


            <div id="profileline">
                <p>Competitive Stats</p>
            </div>


            <hr class="hr">
            <div id="first">
                <p class="compstats"><?php echo $winrate; ?>% Winrate <br></p>
                <p class="compstats"><?php echo $wins ?> Võitu <br></p>
                <p class="compstats"><?php echo $played ?> Mängu kokku <br></p>
                <p class="compstats"> <?php echo $playtime ?> tundi</p>
            </div>
            <div id="second">
                <p class="compstats"><?php echo $winrate; ?>% Winrate <br></p>
                <p class="compstats"><?php echo $wins ?> Võitu <br></p>
                <p class="compstats"><?php echo $played ?> Mängu kokku <br></p>
                <p class="compstats"> <?php echo $playtime ?> tundi</p>
            </div>
            <div id="third">
                <p class="compstats"><?php echo $winrate; ?>% Winrate <br></p>
                <p class="compstats"><?php echo $wins ?> Võitu <br></p>
                <p class="compstats"><?php echo $played ?> Mängu kokku <br></p>
                <p class="compstats"> <?php echo $playtime ?> tundi</p>
            </div>
            <div id="profileline">
                <p>Kõige rohkem mängitud kangelased</p>
            </div>


            <hr class="hr">


        </
        >
    </div> <!-- /.col -->
</div> <!-- /.row -->
<?php get_footer(); ?>
