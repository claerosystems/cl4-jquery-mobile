
<div class="ui-grid-a ui-responsive">
	<div class="ui-block-a">
		<div class="ui-body ui-body-d">
			<h2>Edit Profile</h2>
			<?php echo $edit_fields; ?>
		</div>
	</div>
	<div class="ui-block-b">
		<div class="ui-body ui-body-d">
			<h2>Change Password</h2>

			<?php echo Form::open(URL::site(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'password'))));
			echo Form::hidden('form', 'password');
			?>

			<label for="pass1">Your Current Password</label>
			<?php echo Form::password('current_password', '', array('id' => 'pass1', 'data-mini' => 'true', 'class' => 'text', 'size' => 30, 'maxlength' => 255)); ?>

			<label for="pass2">New Password</label>
			<?php echo Form::password('new_password', '', array('id' => 'pass2', 'data-mini' => 'true', 'class' => 'text', 'size' => 30, 'maxlength' => 255)); ?>


			<label for="pass3">Confirm New Password</label>
			<?php echo Form::password('new_password_confirm', '', array('id' => 'pass3', 'data-mini' => 'true', 'class' => 'text', 'size' => 30, 'maxlength' => 255)); ?>

			<div class="clear"></div>

			<div data-role="controlgroup" data-type="horizontal">
				<?php echo Form::submit('cl4_submit', 'Change Password', array()); ?>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

