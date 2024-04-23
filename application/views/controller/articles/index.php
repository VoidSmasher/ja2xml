<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 27.08.14
 * Time: 17:18
 */
?>
<div class="container">
	<div class="col-md-8">
		<?php echo $breadcrumbs; ?>

		<h1><?php echo __('menu.articles'); ?></h1>

		<?php if ($articles_count > 0): ?>
			<?php foreach ($articles as $article): ?>
				<div class="blog-preview">
					<h2>
						<a href="<?php echo $article->get_path('show'); ?>"><?php echo $article->title; ?></a>
					</h2>

					<p>
						<span class="glyphicon glyphicon-time"></span> <?php echo Force_Date::factory($article->created_at)->humanize(); ?>
					</p>
					<?php if (!empty($article->image)): ?>
						<img class="img-responsive" src="<?php echo $article->get_image_large(); ?>" alt="<?php echo $article->title; ?>">
					<?php endif; ?>

					<p><?php echo $article->description; ?></p>
					<a class="btn btn-primary" href="<?php echo $article->get_path('show'); ?>">Читать дальше
						<span class="glyphicon glyphicon-chevron-right"></span></a>
				</div>
			<?php endforeach; ?>
			<?php /*
				<!-- Pager -->
				<ul class="pager">
					<li class="previous">
						<a href="#">&larr; Новые</a>
					</li>
					<li class="next">
						<a href="#">Старые &rarr;</a>
					</li>
				</ul>
*/
			?>
		<?php else: ?>
			<p>Ни одной статьи не найдено.</p>
		<?php endif; ?>

	</div>
	<!-- Blog Sidebar Widgets Column -->
	<div class="col-md-4">

		<?php echo $search; ?>

		<?php echo $tags; ?>
		<?php /*
			<!-- Side Widget Well -->
			<div class="well">
				<h4>Side Widget Well</h4>

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore, perspiciatis adipisci accusamus
					laudantium odit aliquam repellat tempore quos aspernatur vero.</p>
			</div>
*/
		?>
	</div>
</div>
