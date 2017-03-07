<div class="col-sm-4 blog-sidebar" style="margin-top: 20px;">

    <div class="sidebar-module">
        <div id="top3">
            <a href="/eesti-ranking">
                <div id="sidebar-top3-image"></div>
            </a>
            <?php global $wpdb;
            $toplist = $wpdb->get_results("SELECT * FROM wp_ranking LEFT JOIN wp_ranks USING (tier) ORDER BY rank DESC LIMIT 3");
            $i = 1;
            ?>
            <table>
                <?php foreach ($toplist as $top): ?>
                    <tr>
                        <td style="width: 30px;"><?= $i++ ?>#</td>
                        <td><a href="profiil/<?= $top->battle_tag ?>"><?= $top->name ?></a></td>
                        <td style="text-align:right;"><?= $top->rank ?></td>
                        <td style="text-align: right; width: 40px;"><img id="top3size" src="<?= $top->rank_image ?>"
                                                                         alt=""></td>
                    </tr>
                <?php endforeach ?>
            </table>
            </p>
        </div>
        <a href="/heroes">
            <div id="sidebar-heroes"></div>
        </a>
        <a href="https://www.reddit.com/r/Overwatch/">
            <div id="sidebar-reddit"></div>
        </a>
        <a href="http://discord.gg/cfhqKbc">
            <div id="sidebar-discord"></div>
        </a>
        <?php if (is_active_sidebar('home_right_1')) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
                <?php dynamic_sidebar('home_right_1'); ?>
            </div><!-- #primary-sidebar -->
        <?php endif; ?>

    </div>
</div><!-- /.blog-sidebar -->


