<div class="col-sm-3 col-sm-offset-1 blog-sidebar">

    <div class="sidebar-module">
        <div id="top3">
            <?php global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM wp_ranking ORDER BY rank DESC LIMIT 3");
            echo '<table><th colspan="3">Eesti top 3</th>';
            $i = 0;
            foreach ($result as $print) {
                $i++;
                echo '<tr><td>'.$i.'.</td><td><a href="profiil/'.$print->battletag.'">' . $print->nimi . '</a> </td><td style="text-align:right;"> ' . $print->rank . ' <img id="top3size" src="' . $print->rank_image . '" alt=""></td></tr>';
            }
            echo '</table>'?>
            </p>
        </div>
        <ol class="list-unstyled">
            <li class="sidebox"><a class="btn btn-front-page" href="<?= get_site_url()?>/eesti-ranking/">Eesti ranking</a></li>
            <li class="sidebox3"><a class="btn btn-front-page" href="<?= get_site_url()?>/heroes/">Heroes</a></li>
        </ol>

        <?php if ( is_active_sidebar( 'home_right_1' ) ) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
                <?php dynamic_sidebar( 'home_right_1' ); ?>
            </div><!-- #primary-sidebar -->
        <?php endif; ?>

    </div>
</div><!-- /.blog-sidebar -->


