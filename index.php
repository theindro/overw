<?php get_header(); ?>

<style>
    .adsbygoogle {
        display: block;
        background: #ddd;
        border-radius: 3px;
        width: 100%;
        margin: 10px 0 -20px 0;
    }
</style>

<div id="main-page-header">
    <div class="container">
        <div class="lisa">
            <div id="inputform">
                <span
                    style="font-size: 12px;display: block;color: #d0d0d0; font-family: 'Open Sans'; margin-bottom:15px;">Sisesta oma battletag, et näha ennast edetabelis ning oma profiili statistikat</span>
                <input type="search" placeholder="BattleTag-2413" name="battletag" class="form-control input-tag">
                <button class="btn btn-primary btn-esita">Otsi
                    <div id="uuenda-loading" style="display:none;"></div>
                </button>
                <p style="padding-top: 10px;">Loevad ka suured ja väiksed tähed ning kasutage '#' asemel
                    '-'</p>
                <div id="ajax_call_return" style="display:none;">
                    <!-- Display Name Error Here  -->
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-sm-8 blog-main">
            <?php
            if (have_posts()) : while (have_posts()) : the_post();
                get_template_part('content', get_post_format());
            endwhile; ?>
                <nav>
                    <ul class="pager">
                        <li><?php next_posts_link('Previous'); ?></li>
                        <li><?php previous_posts_link('Next'); ?></li>

                    </ul>
                </nav>
            <?php endif; ?>
        </div>    <!-- /.blog-main -->
        <?php get_sidebar(); ?>
    </div>    <!-- /.row -->

</div>
<?php get_footer(); ?>

