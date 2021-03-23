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
    'hook' => 'ClientAreaPageDomainDNSManagement',
    'function' => 'ClientAreaPageDomainDNSManagement',
    'type' => 'client',
);

if (!function_exists('ClientAreaPageDomainDNSManagement')) {
    function ClientAreaPageDomainDNSManagement($args){

		$class = new SMSIR();
		$settings = $class->getSettings();
		$apiparams = json_decode($settings['apiparams']);
		$apiparamsarray = get_object_vars($apiparams);

		$type = 'clientareadomain';
		if($apiparamsarray['clientareadomainwithverification'] == "on"){
			
			$userid = $_SESSION['uid'];
			$adminid = $_SESSION['adminid'];
			if($adminid){
				return;
			} 
			$user_gid = $apiparamsarray['clientgroups'];
			$select_user_group = mysql_query("SELECT `id` FROM `tblclients` WHERE id = '$userid' AND `groupid` = '$user_gid' ORDER BY id DESC LIMIT 1");
			$numrow_user_group = mysql_num_rows($select_user_group);
			if($user_gid == ""){$numrow_user_group = 0;}
			if($numrow_user_group == 0){
				if($userid){
					if($user_gid){
						$clientgroups = "AND `a`.`groupid` != '".intval($user_gid)."'";
					} else {
						$clientgroups = "";
					}
					
					$mobilenumber = $settings['gsmnumberfield'];
					$userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
						FROM `tblclients` as `a`
						JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
						WHERE `a`.`id` = '" . $userid . "'
						".$clientgroups."
						AND `b`.`fieldid` = '" . $mobilenumber . "'
						LIMIT 1";

					$result = mysql_query($userSql);
					$num_rows = mysql_num_rows($result);
					
					if ($num_rows == 1) {
						$UserInformation = mysql_fetch_assoc($result);
						$user_mobile_int = $UserInformation['gsmnumber'];
						$user_mobile_int = $class->ConvertFarsiNumToEnglish($user_mobile_int);

						if($user_mobile_int){
							$user_mobile = doubleval($user_mobile_int);
							if(preg_match('/^9(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $user_mobile)){

								$random_number = mt_rand(100000, 999999);

								date_default_timezone_set('Asia/Tehran');
								$nowtime = time();
								
								$select_user = mysql_query("SELECT `add_time`,`status` FROM `mod_smsir_verifications` WHERE `user_id` = '$userid' AND mobile = '$user_mobile' ORDER BY id DESC LIMIT 1");
								$numrow_user = mysql_num_rows($select_user);
								
								if($numrow_user == 1){
									$fetch_user = mysql_fetch_array($select_user);
									$ver_status = $fetch_user['status'];
									
									if($ver_status == 'active'){
										$user_added = intval($fetch_user['add_time']);
										$validateday = intval($apiparamsarray['validateday']);
										$expiredate = $user_added + ($validateday * 86400);

										if($nowtime < $expiredate){
											return;
										} else {
											$class->setGsmnumber(json_encode($user_mobile));
											$class->setUserid($userid);
											$sendverification = $class->sendverification($user_mobile,$random_number);
											if($sendverification == true){
												$update_active = mysql_query("UPDATE mod_smsir_verifications SET code = '$random_number',add_time = '$nowtime',status = 'pending' WHERE `user_id` = '$userid' AND mobile = '$user_mobile'");
												if($update_active){
													header("Location: clientverification.php?verify=renew_active&type=".$type);
													exit;
												}
											}
										}
									} elseif($ver_status == 'pending'){
										$class->setGsmnumber(json_encode($user_mobile));
										$class->setUserid($userid);
										$sendverification = $class->sendverification($user_mobile,$random_number);
										if($sendverification == true){
											$update_pending = mysql_query("UPDATE mod_smsir_verifications SET code = '$random_number',add_time = '$nowtime' WHERE `user_id` = '$userid' AND mobile = '$user_mobile'");
											if($update_pending){
												header("Location: clientverification.php?verify=renew_pending&type=".$type);
												exit;
											}
										}
									} else {
										$class->setGsmnumber(json_encode($user_mobile));
										$class->setUserid($userid);
										$sendverification = $class->sendverification($user_mobile,$random_number);
										if($sendverification == true){
											$update_active = mysql_query("UPDATE mod_smsir_verifications SET code = '$random_number',add_time = '$nowtime',status = 'pending' WHERE `user_id` = '$userid' AND mobile = '$user_mobile'");
											if($update_active){
												header("Location: clientverification.php?verify=renew_nostatus&type=".$type);
												exit;
											}
										}
									}
								} else {
									$select_user_rep = mysql_query("SELECT `user_id` FROM `mod_smsir_verifications` WHERE `user_id` = '$userid' ORDER BY id DESC LIMIT 1");
									$numrow_user_rep = mysql_num_rows($select_user_rep);
									
									if($numrow_user_rep == 0){
										$class->setGsmnumber(json_encode($user_mobile));
										$class->setUserid($userid);
										$sendverification = $class->sendverification($user_mobile,$random_number);
										if($sendverification == true){
											$result_in = mysql_query("INSERT INTO mod_smsir_verifications() VALUES('','$userid','$user_mobile','$random_number','pending','$nowtime')");
											if($result_in){
												header("Location: clientverification.php?verify=register&type=".$type);
												exit;
											}
										}
									} else {
										$class->setGsmnumber(json_encode($user_mobile));
										$class->setUserid($userid);
										$sendverification = $class->sendverification($user_mobile,$random_number);
										if($sendverification == true){
											$update_in = mysql_query("UPDATE mod_smsir_verifications SET mobile = '$user_mobile',code = '$random_number',add_time = '$nowtime',status = 'pending' WHERE `user_id` = '$userid'");
											if($update_in){
												header("Location: clientverification.php?verify=updatedUser&type=".$type);
												exit;
											}
										}
									}
								}
							} else {
								header("Location: clientverification.php?verify=novalidmobile&type=".$type);
								exit;
							}
						} else {
							header("Location: clientverification.php?verify=nomobile&type=".$type);
							exit;
						}
					} else {
						header("Location: clientverification.php?verify=nouser&type=".$type);
						exit;
					}
				} else {
					header("Location: clientarea.php");
					exit;
				}
			}
		}
    }
}

return $hook;
