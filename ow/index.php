<?php get_header(); ?>
<div class="container">

	<div class="lisa">
		<section class="webdesigntuts-workshop">
			<form action="http://localhost/overwatch.ee/profiil/" method="get" id="inputform">
				<input type="search"  placeholder="BattleTag-2413" name="nimi" >
				<input type="submit" name="submit" class="button"></button>
			</form>
		</section>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-sm-8 blog-main">
			<?php
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				get_template_part( 'content', get_post_format() );
			endwhile; ?>
				<nav>
					<ul class="pager">
						<li><?php next_posts_link( 'Previous' ); ?></li>
						<li><?php previous_posts_link( 'Next' ); ?></li>

					</ul>
				</nav>
				<?php endif; ?>
		</div>	<!-- /.blog-main -->
		<?php get_sidebar(); ?>
	</div> 	<!-- /.row -->
	</div>
<?php get_footer(); ?>

