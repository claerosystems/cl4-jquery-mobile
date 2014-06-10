<a title="<?php echo URL_ROOT; ?>" data-ajax="false" href="/"><img width="240" src="http://claero.com/wp-content/uploads/2012/10/claero_logo_and_text-300x511.png"></a>
<p><?php echo date('F d, Y'); ?></p>
<?php if ( ! empty($user) && $user->loaded()) { ?>
	<p>Welcome <a data-ajax="false" href="<?php echo Base::get_url('private', array('controller' => 'account')); ?>"><?php echo $user->name(); ?></a></p>
<?php } ?>
<div class="clearfix"></div>
<ul data-role="listview" class="ui-listview">
	<?php echo View::factory('themes' . '/' . APP_THEME . '/menu')->set($kohana_view_data)->render(); ?>
</ul>