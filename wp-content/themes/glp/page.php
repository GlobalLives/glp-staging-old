<?php get_template_part('templates/page-excerpt'); ?>
<div class="page-container static-page-container container">
	<div class="row">
		<div class="col-md-3">
			<?php get_template_part('templates/nav','local'); ?>
		</div>
		<div class="col-md-9">
			<?php get_template_part('templates/content', get_post_type()); ?>
		</div>
	</div>
</div>