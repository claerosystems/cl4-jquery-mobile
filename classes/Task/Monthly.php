<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Performs monthly maintenance.
 * To use:
 *
 *      sudo -u shell /usr/bin/php /home/shell/site/html/index.php --task=monthly
 *
 * or crontab for shell user:

# Use the hash sign to prefix a comment
# +---------------- minute (0 - 59)
# |  +------------- hour (0 - 23)
# |  |  +---------- day of month (1 - 31)
# |  |  |  +------- month (1 - 12)
# |  |  |  |  +---- day of week (0 - 7) (Sunday=0 or 7)
# |  |  |  |  |
# *  *  *  *  *  command to be executed

# monthly tasks
0  5  15  *  * /usr/bin/php /home/shell/site/html/index.php --task=monthly

 *
 *
 * @package    CL4 Mobile
 * @category   Cron
 * @author     Claero Systems
 * @copyright  (c) 2014 Claero Systems
 */
class Task_Monthly extends Minion_Task {
	public $auth_required = TRUE;

	public $db_info;
	public $email_log = NULL;
	public $error_flag = FALSE;

	/**
	 * Run the task.
	 *
	 * @return void
	 */
	protected function _execute(array $params) {
		require_once(ABS_ROOT . '/application/vendor/proby/Proby.php');

		Minion_CLI::write(EOL . '-- Start Monthly Maintenance Task --');

		$proby_task_id = Kohana::$config->load('proby.' . DATABASE_DEFAULT . '.daily');
		if ( ! empty($proby_task_id)) {
			Proby::setApiKey(Kohana::$config->load('proby.api_key'));
			Proby::sendStartNotification($proby_task_id);
			Minion_CLI::write('sent proby start notification for task id: ' . $proby_task_id);
		}

		try {
			// get the database connection information
			$this->db_info = Kohana::$config->load('database.' . DATABASE_DEFAULT . '.connection');

			// INSERT STUFF TO DO HERE
			// INSERT STUFF TO DO HERE
			// INSERT STUFF TO DO HERE




		} catch (Exception $e) {
			if ( ! empty($proby_task_id)) {
				Proby::sendFinishNotification($proby_task_id, TRUE, Kohana_Exception::text($e));
				Minion_CLI::write('sent proby finish notification for task id: ' . $proby_task_id);
			}
			Minion_CLI::write(EOL . '-- ERROR: Maintenance Task Failed: ' . Kohana_Exception::text($e) . ' --');
			Timeportal::deliver_email(SEND_ERRORS_TO, APP_SHORT_NAME . ' Daily ' . date('Y-m-d') . ' batch job failed', 'The maintenance cron job / daily task failed: ' . Kohana_Exception::text($e));
		}

		if ( ! empty($proby_task_id)) {
			Proby::sendFinishNotification($proby_task_id);
			Minion_CLI::write('sent proby finish notification for task id: ' . $proby_task_id);
		}
		Minion_CLI::write(EOL . '-- End Monthly Maintenance Task --');
	}

	public function update_status($status) {
		// update email message
		$this->email_log .= HEOL . nl2br($status) . EOL;

		// send status to stdout
		Minion_CLI::write($status);
	}
}