<?php

if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
	echo '<div class="cl4_buttons cl4_buttons_top">' . implode('', $form_buttons) . '</div>' . EOL;
}

if ($any_visible) { ?>
	<ul data-role="listview">
		<?php
		foreach ($display_order as $column) {
			if (isset($form_field_html[$column])) { ?>
				<li data-role="fieldcontain">
					<?php echo $field_html['label']; ?>
					<?php echo $field_html['field']; ?>
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
	echo '<div class="cl4_buttons">' . implode('', $form_buttons) . '</div>';
}