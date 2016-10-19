<?php
/*
Template Name: Heroes list
*/
?>

<?php get_header(); ?>

    <div class="container">
        <div class="row">

            <div class="col-sm-12">

                <?php
                if (isset($_POST['submit'])) {
                    global $wpdb;
                    $tablename = $wpdb->prefix . 'battletag';
                    $data = array(
                        'battletag' => $_POST['battletag'],
                    );
                    $wpdb->insert($tablename, $data);
                }
                ?>
                <div class="lisa">
                    <form action="" method="post">
                        <input class="lisakasutaja" placeholder="BattleTag#2413" name="battletag" type="text">
                        <input type="submit" name="submit" class="nupplisa">
                    </form>
                </div>

                <?php
                echo '<br>';
                echo '<table id="rank" class="rank">';
                echo '<tr><th>Avatar</th><th>Nimi</th><th>Level</th><th>Rank</th><th></th></tr>';
                global $wpdb;
                $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC;");
                foreach ($result as $print) {

                    echo ' <td><img class="avatar" src="' . $print->avatar . '" alt=""></td>';
                    echo '<td class="tabl">' . $print->nimi . '</td>';
                    echo ' <td class="tabl">' . $print->lvl . '</td>';
                    echo '<td class="tabl";>' . $print->rank . '</td>';
                    echo '<td><img class="avatar" src="' . $print->pilt . '" alt=""></td></tr>';
                }
                echo '</table>';
                ?>

                <br>
                <br>
                <br>
            </div> <!-- /.col -->

        </div> <!-- /.row -->
    </div>
<?php get_footer(); ?>