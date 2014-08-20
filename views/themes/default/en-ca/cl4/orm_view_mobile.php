<!-- modules / cl4-jquery-mobile / orm_view_mobile.php -->
<?php

if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
	echo '<div data-role="controlgroup" data-type="horizontal" class="cl4_buttons">' . implode('', $form_buttons) . '</div>' . EOL;
}

if ($any_visible) { ?>
	<ul data-role="listview">
		<?php
		foreach ($display_order as $column) {
			if (isset($form_field_html[$column])) { ?>
				<li data-role="fieldcontain">
					<?php echo $form_field_html[$column]['label']; ?>
					<?php echo $form_field_html[$column]['field']; ?>
				</li>
			<?php 	} // if
		} // foreach ?>
	</ul>
<?php
// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

if ($form_options['display_buttons']) {
	echo '<div data-role="controlgroup" data-type="horizontal">' . implode('', $form_buttons) . '</div>';
}