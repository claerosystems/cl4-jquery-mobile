<div class="cl4_delete_confirm_message">
	<?php echo Form::open(Request::current()); ?>
	Are you sure you want to delete the following item from <?php echo HTML::chars($object_name); ?>?
	<div data-role="controlgroup" data-type="horizontal">
		<?php
		echo Form::submit('cl4_delete_confirm', __('Yes'));
		echo Form::submit('cl4_delete_confirm', __('No'));
		?>
	</div>
	<?php echo Form::close(); ?>
</div>