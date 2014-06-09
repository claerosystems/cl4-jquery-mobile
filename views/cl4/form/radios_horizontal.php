<fieldset data-role="controlgroup" data-type="horizontal">
<?php
foreach ($fields as $num => $field) {
	if ($num > 0) echo '&nbsp;&nbsp;&nbsp;';
	echo $field['radio'] . $field['label_tag'] . $field['label'] . '</label>';
}
?>
</fieldset>