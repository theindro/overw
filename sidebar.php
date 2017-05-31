<style>
    .sidebar-main-btn {
        font-family: overwatch;
        font-size: 30px;
        width: 100%;
        border: none;
        transition: all 0.15s ease-in-out;
        background-color: #0090e6;
    }

    .sidebar-main-btn:hover {
        background-color: rgb(54, 177, 237);
        transition: all 0.15s ease-in-out;
    }

    .sidebar-reddit-btn {
        background-color: #8ad600;
    }

    .sidebar-reddit-btn:hover {
        background-color: #b4e600;
    }


    .sidebar-top-btn {
        background-color: #323232;
    }

    .sidebar-top-btn:hover {
        background-color: #4d4d4d;
    }
</style>

<div class="col-sm-4 blog-sidebar" style="margin-top: 20px;">
    <div class="sidebar-module">
        <div id="top3">
            <a href="/eesti-ranking">
                <input type="button" class="btn btn-primary sidebar-top-btn sidebar-main-btn" value="eesti Top 5">
            </a>
            <?php global $wpdb;

            $current_season = 5;
            $toplist = $wpdb->get_results("SELECT * FROM wp_ranking LEFT JOIN wp_ranks USING (tier) WHERE season = $current_season ORDER BY rank DESC LIMIT 5");
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
            <input type="button" class="btn btn-primary sidebar-main-btn" value="Heroes">
        </a>
        <a href="https://www.reddit.com/r/Overwatch/">
            <button style="margin-top:15px;" type="button" class="btn btn-success sidebar-reddit-btn sidebar-main-btn">Reddit</button>
        </a>
        <?php if (is_active_sidebar('home_right_1')) : ?>
            <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
                <?php dynamic_sidebar('home_right_1'); ?>
            </div><!-- #primary-sidebar -->
        <?php endif; ?>

    </div>
</div><!-- /.blog-sidebar -->


