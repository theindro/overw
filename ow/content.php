<div class="blog-post">

    <?php if (has_post_thumbnail()) : ?>
        <div class="row">
            <a style="color:white!important;" href="<?php the_permalink(); ?>">
                <div class="col-md-4">
                    <div class="dark-overlay">
                        <?php the_post_thumbnail('thumbnail'); ?>
                        <div class="post-info">
                            <p class="post-info-text"><?php the_date(); ?> by <?php the_author(); ?></p>
                        </div>
                        <div class="post-text-container">
                            <h2 class="blog-post-title-main-page"><?php the_title(); ?></h2>
                            <span class="read-more">
                               <?php the_excerpt(); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endif; ?>

</div>

<!-- /.blog-post -->
