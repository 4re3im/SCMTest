<?php 
/**
*
* Proccesses pending users to add HM products and classes
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
ini_set('log_errors', 1);

class CupInitiateCronsPendingsSyncNew extends Job {
	
	public function getJobName() {
		return t("Initiate Crons for Pending HM Provisions");
	}
	
	public function getJobDescription() {
		return t("Initiate Crons for Pending HM Provisions");
	}
	
	public function run() {
        $msg = "Provisioning Crons Initiated!";
        shell_exec(CRON_HM_PRODUCTS);

        return t($msg);
	}
}

?>