<?php
/*
Template Name: Heroes list
*/
?>

<?php get_header(); ?>

    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div class="blog-post">
                    <h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </div>

                <?php
                echo '<br>';
                echo '<table id="rank" class="rank">';
                echo '<tr><th>#</th><th>Avatar</th><th>Nimi</th><th>Level</th><th>Rank</th><th></th></tr>';
                global $wpdb;
                $i = 0;
                $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC;");
                foreach ($result as $print) {
                    $i++;
                    echo ' <td>' . $i . '</td>';
                    echo ' <td><img class="avatar" src="' . $print->avatar . '" alt=""></td>';
                    echo '<td class="tabl">  <a href="http://localhost/overwatch.ee/profiil/?battletag='.$print->battletag.'&submit=Esita">' . $print->nimi . '</a></td>';
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