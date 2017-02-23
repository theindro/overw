<div class="col-sm-3 col-sm-offset-1 blog-sidebar">

    <div class="sidebar-module">
        <div id="top3">
            <?php global $wpdb;
            $toplist = $wpdb->get_results("SELECT * FROM wp_ranking LEFT JOIN wp_ranks USING (tier) ORDER BY rank DESC LIMIT 3");
            $i = 1;
            ?>
            <table><th colspan="4">Eesti top 3</th>
            <?php foreach ($toplist as $top): ?>
            <tr>
                <td><?= $i++ ?>#</td>
                <td><a href="profiil/<?= $top->battle_tag?>"><?= $top->name ?></a></td>
                <td style="text-align:right;"><?=$top->rank ?></td>
                <td><img id="top3size" src="<?= $top->rank_image?>" alt=""></td>
            </tr>
            <?php endforeach ?>
            </table>
            </p>
        </div>
        <ol class="list-unstyled">
            <li class="sidebox"><a class="btn btn-front-page" href="<?= get_site_url() ?>/eesti-ranking/">Eesti
                    ranking</a></li>
            <li class="sidebox3"><a class="btn btn-front-page" href="<?= get_site_url() ?>/heroes/">Heroes</a></li>
        </ol>

        <?php if (is_active_sidebar('home_right_1')) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
                <?php dynamic_sidebar('home_right_1'); ?>
            </div><!-- #primary-sidebar -->
        <?php endif; ?>

    </div>
</div><!-- /.blog-sidebar -->


