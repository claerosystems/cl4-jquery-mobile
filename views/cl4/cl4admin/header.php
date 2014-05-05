<?php if (0) { ?>

<?php echo Form::open($form_action, array(
	'class' => 'js_cl4_model_select_form',
	'method' => 'get',
)); ?>
<div data-role="controlgroup" data-type="horizontal" class="cl4_model_select_container">
	<?php echo $model_select; ?>
	<input type="button" value="Go" class="js_cl4_model_select_go">
<?php echo Form::close(); ?>

<?php } ?>