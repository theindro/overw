<?php
/*
Template Name: List kogu overwatch herodest
*/
?>

<?php get_header(); ?>

    <div class="container">
        <div class="row">

            <div class="col-sm-12">
                <div id="filterrank">
                    Heroes
                </div>

                <?php

                global $wpdb;
                $i = 0;
                echo '<ul id="herolist">';
                $result = $wpdb->get_results("SELECT * FROM wp_heroesall where role = 'Offense' ORDER BY Role DESC;");
                foreach ($result as $print) {
                    $i++;
                    echo '<li class="listt"><img class="avatar2" src="' . $print->image . '" alt=""> <br><p>' . $print->name . '</p></li>';
                }
                echo '</ul>';

                ?>
                <?php

                global $wpdb;
                $i = 0;
                echo '<ul id="herolist">';
                $result = $wpdb->get_results("SELECT * FROM wp_heroesall where role = 'Defense' ORDER BY Role DESC;");
                foreach ($result as $print) {
                    $i++;
                    echo '<li class="listt"><img class="avatar2" src="' . $print->image . '" alt=""> <br><p>' . $print->name . '</p></li>';
                }
                echo '</ul>';

                ?>
                <?php

                global $wpdb;
                $i = 0;
                echo '<ul id="herolist">';
                $result = $wpdb->get_results("SELECT * FROM wp_heroesall where role = 'Tank' ORDER BY Role DESC;");
                foreach ($result as $print) {
                    $i++;
                    echo '<li class="listt"><img class="avatar2" src="' . $print->image . '" alt=""> <br><p>' . $print->name . '</p></li>';
                }
                echo '</ul>';

                ?>
                <?php

                global $wpdb;
                $i = 0;
                echo '<ul id="herolist">';
                $result = $wpdb->get_results("SELECT * FROM wp_heroesall where role = 'Support' ORDER BY Role DESC;");
                foreach ($result as $print) {
                    $i++;
                    echo '<li class="listt"><img class="avatar2" src="' . $print->image . '" alt=""> <br><p>' . $print->name . '</p></li>';
                }
                echo '</ul>';

                ?>

                <br>
                <br>
                <br>
            </div> <!-- /.col -->
        </div> <!-- /.row -->
    </div>
<?php get_footer(); ?>