<!-- modules / cl4-jquery-mobile / cl4/orm_form_mobile.php -->
<div class="responsive_form">
<?php echo $form_open_tag; ?>

<?php echo implode(EOL, $form_fields_hidden) . EOL; ?>
<?php
// display the search specfic stuff
if ($mode == 'search') { ?>

<fieldset data-role="controlgroup" data-type="horizontal">
	<legend>Search with:</legend>
	<?php echo $search_type_html; ?>
</fieldset>

<fieldset data-role="controlgroup" data-type="horizontal">
	<legend>Search method:</legend>
 	<?php echo $like_type_html; ?>
</fieldset>

<?php } // if
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
	if ($form_options['display_buttons']) {
		// the buttons
		echo '<div data-role="controlgroup" data-type="horizontal" class="cl4_buttons">' . implode('', $form_buttons) . '</div>' . EOL;
	}

// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

echo $form_close_tag . EOL;
?>
</div>