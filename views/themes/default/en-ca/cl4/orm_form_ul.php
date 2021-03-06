<!-- modules / cl4-jquery-mobile / themes/cl4/orm_form_ul.php -->
<?php echo $form_open_tag; ?>

<?php echo implode(EOL, $form_fields_hidden) . EOL; ?>

<?php
// display the search specfic stuff
if ($mode == 'search') { ?>
	<fieldset class="cl4_tools">
		Search with: <?php echo $search_type_html; ?><br />
		Search method: <?php echo $like_type_html; ?>
	</fieldset>
<?php } // if

if ($any_visible) {
	if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
		// the buttons
		echo '<div data-role="controlgroup" data-type="horizontal">' . implode('', $form_buttons) . '</div>' . EOL;
	}
	?>
	<div class="clearfix"></div>
	<ul class="cl4_form">
		<?php
		foreach ($display_order as $column) {
			if (isset($form_field_html[$column])) { ?>
				<li>
					<ul>
						<li class="field_label"><?php echo $form_field_html[$column]['label']; ?></li>
						<li class="field_value"><?php echo $form_field_html[$column]['field'], $form_field_html[$column]['help']; ?></li>
					</ul>
				</li>
			<?php } // if
		} // foreach ?>
	</ul>
	<div class="clearfix"></div>

	<?php
	if ($form_options['display_buttons']) {
		// the buttons
		echo '<div data-role="controlgroup" data-type="horizontal">' . implode('', $form_buttons) . '</div>' . EOL;
	}
// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

echo $form_close_tag;