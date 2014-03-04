<div class="messages">

<?php  if ( ! empty($messages)) { ?>
<?php /* ?>
	<ul data-role="listview" data-inset="true">
		<?php
		foreach ($messages as $message) {
			if ($level_to_class[$message['level']] == 'error') {
				$theme = 'a'; // black
			} else if ($level_to_class[$message['level']] == 'warning') {
				$theme = 'e'; // yellow
			} else if ($level_to_class[$message['level']] == 'notice') {
				$theme = 'e'; // d is white
			} else {
				$theme = 'c'; // grey
			}
			echo '<li class="' . $level_to_class[$message['level']] . '" data-theme="' . $theme . '">' . $message['message'] . '</li>' . EOL;
		} // foreach
		?>
	</ul>
<?php */ ?>

<?php
foreach ($messages as $message) {
	if ($level_to_class[$message['level']] == 'error') {
		$theme = 'e'; // red
	} else if ($level_to_class[$message['level']] == 'warning') {
		$theme = 'c'; // yellow
	} else if ($level_to_class[$message['level']] == 'notice') {
		$theme = 'd'; // green
	} else {
		$theme = 'a'; // grey
	}
	echo '<div class="ui-bar ui-bar-' . $theme . ' ' . $level_to_class[$message['level']] . '">' . $message['message'] . '</div>' . EOL;
} // foreach
?>

<?php } // if ?>
<?php /*
<h3 class="ui-bar ui-bar-a">Test Theme A</h3>
<h3 class="ui-bar ui-bar-b">Test Theme B</h3>
<h3 class="ui-bar ui-bar-c">Test Theme C</h3>
<h3 class="ui-bar ui-bar-d">Test Theme D</h3>
<h3 class="ui-bar ui-bar-e">Test Theme E</h3>
 <?php */ ?>

</div>