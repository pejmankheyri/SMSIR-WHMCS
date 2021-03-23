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

include_once( dirname( __FILE__ ) . '/modules/addons/smsir/smsclass.php' );
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
 
 
define('CLIENTAREA', true);
//define('FORCESSL', true); // Uncomment to force the page to use https://
 
require __DIR__ . '/init.php';
 
$ca = new ClientArea();
$ca->setPageTitle($whmcs->get_lang('smsir_clientverification_pagetitle'));
 
$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('clientverification.php', $whmcs->get_lang('smsir_clientverification_pagetitle'));
 
$ca->initPage();

$type = $_REQUEST['type'];

switch($type){
	case 'order':
		$callback = "cart.php?a=checkout";
		break;
	case 'login':
		$callback = "clientarea.php";
		break;
	case 'clientareacart':
		$callback = "clientarea.php";
		break;
	case 'clientareaemail':
		$callback = "clientarea.php";
		break;
	case 'clientareaproduct':
		$callback = "clientarea.php?action=services";
		break;
	case 'clientareadomain':
		$callback = "clientarea.php?action=domains";
		break;
	case 'clientareainvoice':
		$callback = "clientarea.php?action=invoices";
		break;
	case 'clientareaaddfunds':
		$callback = "clientarea.php";
		break;
	default:
		$callback = "index.php";
		break;
}
$ca->assign('type', $type);

if ($ca->isLoggedIn()) {
	
	$class = new SMSIR();
	$settings = $class->getSettings();
	$apiparams = json_decode($settings['apiparams']);
	$apiparamsarray = get_object_vars($apiparams);
		
	
	$user_id = $ca->getUserID();
	$adminid = $_SESSION['adminid'];
	if($adminid){
		return;
	} 
	
	$user_gid = $apiparamsarray['clientgroups'];
	$select_user_group = mysql_query("SELECT `id` FROM `tblclients` WHERE id = '$user_id' AND `groupid` = '$user_gid' ORDER BY id DESC LIMIT 1");
	$numrow_user_group = mysql_num_rows($select_user_group);
	if($user_gid == ""){$numrow_user_group = 0;}
	if($numrow_user_group == 0){
		
		if($user_gid){
			$clientgroups = "AND `a`.`groupid` != '".intval($user_gid)."'";
		} else {
			$clientgroups = "";
		}

		$sel_custom = mysql_query("SELECT gsmnumberfield FROM mod_smsir_settings");
		$gsmnumberfield = mysql_fetch_assoc($sel_custom)['gsmnumberfield'];
		$userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
			FROM `tblclients` as `a`
			JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
			WHERE `a`.`id` = '" . $user_id . "'
			".$clientgroups."
			AND `b`.`fieldid` = '" . $gsmnumberfield . "'
			LIMIT 1";
		$result = mysql_query($userSql);
		$num_rows = mysql_num_rows($result);
		if ($num_rows == 1) {
			$UserInformation = mysql_fetch_assoc($result);
			$user_mobile = doubleval($UserInformation['gsmnumber']);
			$user_mobile = $class->ConvertFarsiNumToEnglish($user_mobile);

			if($user_mobile){
				if(preg_match('/^9(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $user_mobile)){
					$ca->assign('user_mobile', $user_mobile);
					date_default_timezone_set('Asia/Tehran');
					$nowtime = time();
					$select_user = mysql_query("SELECT `status`,`add_time` FROM `mod_smsir_verifications` WHERE `user_id` = '$user_id' AND `mobile` = '$user_mobile' ORDER BY id DESC LIMIT 1");
					$numrow_user = mysql_num_rows($select_user);
					if($numrow_user == 1){
						$fetch_user = mysql_fetch_array($select_user);
						$ver_status = $fetch_user['status'];
						if($ver_status == 'active'){
							$user_added = intval($fetch_user['add_time']);
							$validateday = intval($apiparamsarray['validateday']);
							$expiredate = $user_added + ($validateday * 86400);
							if($nowtime < $expiredate){
								$ca->assign('mess_client_is_active', Lang::trans('smsir_your_account_is_active'));
							} else {
								$ca->assign('mess_resend_code', Lang::trans('smsir_your_account_is_expired'));
							}
						} elseif($ver_status == 'pending'){
							$ca->assign('mess_enter_code', Lang::trans('smsir_enter_verification_code'));
						} else {
							header("Location: ".$callback);
							exit;
						}
					} else {
						$select_user_rep = mysql_query("SELECT `user_id` FROM `mod_smsir_verifications` WHERE `user_id` = '$user_id' ORDER BY id DESC LIMIT 1");
						$numrow_user_rep = mysql_num_rows($select_user_rep);
						if($numrow_user_rep == 0){
							header("Location: ".$callback);
							exit;
						} else {
							$ca->assign('mess_updatedUser', Lang::trans('smsir_your_mobile_changed'));
						}
					}

					$verificationcode = $_POST['verificationcode'];
					$submited = $_POST['submited'];
						
					if($submited == "ok"){
						if($verificationcode){
							if(strlen($verificationcode) == 6){

								$sel_code = mysql_query("SELECT code FROM mod_smsir_verifications WHERE user_id = '$user_id' AND mobile = '$user_mobile' LIMIT 1")or die(mysql_error());
								$code = mysql_fetch_array($sel_code)['code'];

								if($code == $verificationcode){
									$result_up = mysql_query("UPDATE `mod_smsir_verifications` SET `status` = 'active',`add_time` = '$nowtime' WHERE `user_id` = '$user_id' AND `mobile` = '$user_mobile' LIMIT 1")or die(mysql_error());
									if($result_up){
										$ca->assign('mess_success', Lang::trans('smsir_your_account_activated'));
										header("Location: ".$callback);
										exit;
									}
								} else {
									$ca->assign('mess_code_nomatch', Lang::trans('smsir_code_nomatch'));
								}
							} else {
								$ca->assign('mess_code_length', Lang::trans('smsir_code_lenght'));
							}
						} else {
							$ca->assign('mess_code_null', Lang::trans('smsir_code_null'));
						}
					} elseif($submited == "resend"){
						$random_number = mt_rand(100000, 999999);
						$class->setGsmnumber(json_encode($user_mobile));
						$class->setUserid($user_id);
						$sendverification = $class->sendverification($user_mobile,$random_number);
						if($sendverification == true){
							$update_pending = mysql_query("UPDATE `mod_smsir_verifications` SET `code` = '$random_number',`add_time` = '$nowtime',`status` = 'pending' WHERE `user_id` = '$user_id' AND `mobile` = '$user_mobile'");
							if($update_pending){
								$ca->assign('mess_code_resend_success', Lang::trans('smsir_code_resend_successfull'));
							}
						}
					} else {
						//$result_upd = mysql_query("UPDATE `tblclients` SET `status` = 'Closed' WHERE `id` = '$user_id'");
					}
				} else {
					$ca->assign('mess_novalidmobile', Lang::trans('smsir_novalidmobile'));
				}
			} else {
				$ca->assign('mess_no_mobile', Lang::trans('smsir_no_mobile'));
			}
		} else {
			$ca->assign('mess_no_user', Lang::trans('smsir_no_user'));
		}
	}
	/**
	 * Setup the primary and secondary sidebars
	 *
	 * @link http://docs.whmcs.com/Editing_Client_Area_Menus#Context
	 */
	Menu::primarySidebar('clientView');

	# Define the template filename to be used without the .tpl extension

	$ca->setTemplate('clientverification');
	 
	$ca->output();

} else {
 
    // User is not logged in
    // $ca->assign('clientname', 'Random User');
	header("Location: clientarea.php");
	exit;
}
 
