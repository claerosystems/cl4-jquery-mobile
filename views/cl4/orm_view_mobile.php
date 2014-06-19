<!-- modules / cl4-jquery-mobile / orm_view_mobile.php -->
<?php

if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
	echo '<div data-role="controlgroup" data-type="horizontal" class="cl4_buttons">' . implode('', $form_buttons) . '</div>' . EOL;
}

if ($any_visible) { ?>
	<div data-mini="true">
		<?php
		foreach ($display_order as $column) {
			if (isset($form_field_html[$column])) { ?>

				<div class="ui-field-contain">
					<?php echo $form_field_html[$column]['label']; ?>
					<?php echo $form_field_html[$column]['field'], $form_field_html[$column]['help']; ?>
				</div>
			<?php
			} // if
		} // foreach
		?>
	</div>
	<div class="clear"></div>
<?php
// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
} ?>
<p></p>
<?php if ($form_options['display_buttons']) {
	echo '<div data-role="controlgroup" data-type="horizontal" class="cl4_buttons">' . implode('', $form_buttons) . '</div>';
}