<?php

/**
 * 
 * PHP version 5.6.x | 7.x | 8.x
 * 
 * @category  Addons
 * @package   WHMCS
 * @author Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
 
$hook = array(
    'hook' => 'AdminAreaClientSummaryPage',
    'function' => 'AdminAreaClientSummaryPage',
    'type' => 'admin',
    'extra' => '',
);
if (!function_exists('AdminAreaClientSummaryPage')) {
    function AdminAreaClientSummaryPage($args) {

		$class = new SMSIR();
		$settings = $class->getSettings();
		$apiparams = json_decode($settings['apiparams']);
		$apiparamsarray = get_object_vars($apiparams);

		if($apiparamsarray['showinsummary'] == 'on'){
			$userid = $args['userid'];
			$verify = $_GET['verify'];

			date_default_timezone_set('Asia/Tehran');
			$nowtime = time();
			$random_number = mt_rand(100000, 999999);
			
			if($verify == 'insertAndresend'){
				
				$gsmnumberfield = $settings['gsmnumberfield'];
				$userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
					FROM `tblclients` as `a`
					JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
					WHERE `a`.`id` = '" . $userid . "'
					AND `b`.`fieldid` = '" . $gsmnumberfield . "'
					LIMIT 1";

				$result = mysql_query($userSql);
				$UserInformation = mysql_fetch_assoc($result);
				$gsmnumber = doubleval($UserInformation['gsmnumber']);
				
				if ($gsmnumber) {
					
					$class->setGsmnumber(json_encode($gsmnumber));
					$class->setUserid($userid);
					$sendverification = $class->sendverification($gsmnumber,$random_number);
					if($sendverification == true){
						$result_in = mysql_query("INSERT INTO mod_smsir_verifications() VALUES('','$userid','$gsmnumber','$random_number','pending','$nowtime')");
						if($result_in){
							header("Location: clientssummary.php?userid=".$userid."&verify=sent");
							exit;
						}
					}
				} else {
					header("Location: clientssummary.php?userid=".$userid."&verify=nomobile");
					exit;
				}
			}
			
			
			if($verify == 'sent'){
				echo '<div class="successbox"><strong><span class="title">'.Lang::trans('smsir_code_resend_successfull').'</span></strong><br>'.Lang::trans('smsir_code_resend_successfull_desc').'<br></div>';
			}
			
			if($verify == 'nomobile'){
				echo '<div class="errorbox"><strong><span class="title">'.Lang::trans('smsir_code_resend_nomobile').'</span></strong><br>'.Lang::trans('smsir_code_resend_nomobile_desc').'<br></div>';
			}
			
			$select_user = mysql_query("SELECT `status`,`mobile`,`add_time` FROM `mod_smsir_verifications` WHERE user_id = '$userid' ORDER BY id DESC LIMIT 1");
			$numrow_user = mysql_num_rows($select_user);
			
			if($numrow_user == 1){
				
				$fetch_user = mysql_fetch_array($select_user);
				$status = $fetch_user['status'];
				$mobile = $fetch_user['mobile'];
				$add_time = $fetch_user['add_time'];
				
				if($verify == 'resend'){
					
					$class->setGsmnumber(json_encode($mobile));
					$class->setUserid($userid);
					$sendverification = $class->sendverification($mobile,$random_number);
					if($sendverification == true){
						$update_active = mysql_query("UPDATE mod_smsir_verifications SET code = '$random_number',add_time = '$nowtime',status = 'pending' WHERE `user_id` = '$userid' AND mobile = '$mobile'");
						if($update_active){
							header("Location: clientssummary.php?userid=".$userid."&verify=sent");
							exit;
						}
					} else {
						
					}
				}
				$resend_btn = "<a id='summary-login-as-client' href='clientssummary.php?userid=".$userid."&verify=resend'><img src='images/icons/navback.png' border='0' align='absmiddle'> ".Lang::trans('smsir_verification_code_resend')." </a>";
			} else {
				$status = 'nouser';
				$resend_btn = "<a id='summary-login-as-client' href='clientssummary.php?userid=".$userid."&verify=insertAndresend'><img src='images/icons/navback.png' border='0' align='absmiddle'> ".Lang::trans('smsir_verification_code_resend_insert')." </a>";
			}

			switch($status){
				case 'active':
					$img = 'tick';
					break;
				case 'pending':
					$img = 'accessdenied';
					break;
				case 'nouser':
					$img = 'disabled';
					break;
				default:
					
			}
			
			if($add_time){
				$date = date("Y/m/d-H:i:s",$add_time);
			} else {
				$date = "-";
			}
			
			$boxe = "<div class='col-lg-3 col-sm-6'><div class='clientssummarybox'><div class='title'>".Lang::trans('smsir_verify_infos')."</div><table class='clientssummarystats' cellspacing='0' cellpadding='2'><tbody><tr><td width='120'>".Lang::trans('smsir_status')."</td><td>".$status." <img src='images/icons/".$img.".png' title='".Lang::trans($status)."' /></td></tr><tr><td width='120'>".Lang::trans('smsir_mobile')."</td><td>".$mobile."</td></tr><tr><td width='120'>".Lang::trans('smsir_last_verify_code')."</td><td>".$date."</td></tr></tbody></table><ul><li>".$resend_btn."</li></ul></div></div><div class='clear'></div>";
				
			$vari = "'client-summary-panels'";
			echo '<script>
				$(document).ready(function () {
					$("div[class*='.$vari.']").prepend("'.$boxe.'");
				});
				</script>';	

		}
    }
}

return $hook;
