<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 27.08.14
 * Time: 17:18
 */
?>
<div class="container">
	<div class="col-lg-8">
		<?php echo $breadcrumbs; ?>

		<h1><?php echo $article->title; ?></h1>

		<br>

		<p>
			<span class="glyphicon glyphicon-time"></span> <?php echo Force_Date::factory($article->created_at)
				->humanize(); ?>
		</p>
		<?php if (!empty($article->image)): ?>
			<img class="img-responsive" src="<?php echo $article->get_image_large(); ?>" alt="<?php echo $article->title; ?>">
		<?php endif; ?>

		<hr>

		<p class="lead"><?php echo $article->description; ?></p>

		<?php foreach ($content as $data) {
			$type = Arr::get($data, 'type');
			$value = Arr::get($data, 'value');
			switch ($type) {
				case 'image':
					$title = Arr::get($data, 'title');
					echo '<p class="image">';
					echo Helper_Image::get_image($value, 'article_image_large',$title);
					echo '</p>';
					break;
				case 'markdown':
					echo $value;
					break;
				case 'youtube':
					echo '<div class="news-card-video"><div class="video">';
					echo '<iframe width="640" height="360" src="' . $value . '" frameborder="0" allowfullscreen></iframe>';
					echo '</div></div>';
			}
		} ?>

		<?php /*
			<hr>

			<!-- Blog Comments -->

			<!-- Comments Form -->
			<div class="well">
				<h4>Leave a Comment:</h4>

				<form role="form">
					<div class="form-group">
						<textarea class="form-control" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>

			<hr>

			<!-- Posted Comments -->

			<!-- Comment -->
			<div class="media">
				<a class="pull-left" href="#">
					<img class="media-object" src="http://placehold.it/64x64" alt="">
				</a>

				<div class="media-body">
					<h4 class="media-heading">Start Bootstrap
						<small>August 25, 2014 at 9:30 PM</small>
					</h4>
					Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo.
					Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
					vulputate fringilla. Donec lacinia congue felis in faucibus.
				</div>
			</div>

			<!-- Comment -->
			<div class="media">
				<a class="pull-left" href="#">
					<img class="media-object" src="http://placehold.it/64x64" alt="">
				</a>

				<div class="media-body">
					<h4 class="media-heading">Start Bootstrap
						<small>August 25, 2014 at 9:30 PM</small>
					</h4>
					Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo.
					Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi
					vulputate fringilla. Donec lacinia congue felis in faucibus.
					<!-- Nested Comment -->
					<div class="media">
						<a class="pull-left" href="#">
							<img class="media-object" src="http://placehold.it/64x64" alt="">
						</a>

						<div class="media-body">
							<h4 class="media-heading">Nested Start Bootstrap
								<small>August 25, 2014 at 9:30 PM</small>
							</h4>
							Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin
							commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce
							condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
						</div>
					</div>
					<!-- End Nested Comment -->
				</div>
			</div>
*/
		?>

	</div>
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