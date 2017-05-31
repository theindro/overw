<style>
    #single-post-header {
        width: 100%;
        min-height: 350px;
        height: auto;
        background-color: #1f1f1f !important;
    }

    .dark-overlay-post {
        background-color: rgba(0, 0, 0, 0.70);
        border-radius: 6px;
        position: relative;
        transition: all 0.15s ease-in-out;
        min-height: 350px;
    }


</style>
<?php $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
echo '<div id="single-post-header" style="background: url(' . $url . ') no-repeat; background-size:100%; ">'; ?>
<div class="dark-overlay-post">
    <div class="container">
        <div class="col-sm-12" style="margin-top:100px;">
            <h1 style="font-family:overwatch; color:white; font-size:60px;"><?php the_title(); ?></h1>
        </div>
        <div class="col-sm-12">
            <p class="blog-post-meta"><?php the_date(); ?> by <a href="#"><?php the_author(); ?></a></p>
        </div>
    </div>
</div>
</div>


<div class="container">
    <div class="blog-post" style="    padding-top:30px;
    line-height: 1.9;
    word-wrap: break-word;
    padding-bottom: 200px;
    font-family: 'Open Sans';">
        <?php the_content(); ?>
    </div><!-- /.blog-post -->
</div>