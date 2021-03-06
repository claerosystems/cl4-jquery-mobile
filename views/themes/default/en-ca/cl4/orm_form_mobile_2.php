<?php echo $form_open_tag; ?>

<?php echo implode(EOL, $form_fields_hidden) . EOL; ?>
<?php
// display the search specfic stuff
if ($mode == 'search') { ?>
	<fieldset class="cl4_tools">
		Search with: <?php echo $search_type_html; ?><br />
		Search method: <?php echo $like_type_html; ?>
	</fieldset>
<?php }
if ($any_visible) {
	if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
		// the buttons
		echo '<div class="cl4_buttons cl4_buttons_top">' . implode('', $form_buttons) . '</div>' . EOL;
	}
	?>
	<div data-mini="true">
		<?php
		foreach ($display_order as $column) {
			if (isset($form_field_html[$column])) { ?>
				<?php echo $form_field_html[$column]['label']; ?>
				<?php echo $form_field_html[$column]['field'], $form_field_html[$column]['help']; ?>
			<?php
			}
		}
		?>
	</div>
	<div class="clear"></div>

	<?php
	if ($form_options['display_buttons']) {
		// the buttons
		echo '<div data-role="controlgroup" data-type="horizontal">' . implode('', $form_buttons) . '</div>' . EOL;
	}

// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

echo $form_close_tag . EOL;
?>