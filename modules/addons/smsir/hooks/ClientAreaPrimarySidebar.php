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
    'hook' => 'ClientAreaPrimarySidebar',
    'function' => 'ClientAreaPrimarySidebar',
    'type' => 'client',
);

if (!function_exists('ClientAreaPrimarySidebar')) {
    function ClientAreaPrimarySidebar($args){

		$userid = $_SESSION['uid'];
		
		if($userid){
			$class = new SMSIR();
			$settings = $class->getSettings();
			$apiparams = json_decode($settings['apiparams']);
			$apiparamsarray = get_object_vars($apiparams);

			if($apiparamsarray['showinsidebar'] == 'on'){
				
				$select_user = mysql_query("SELECT `status` FROM `mod_smsir_verifications` WHERE user_id = '$userid'");
				$numrow_user = mysql_num_rows($select_user);

				if($numrow_user == 1){
					$fetch_user = mysql_fetch_array($select_user);
					$status = $fetch_user['status'];

					if($status == 'active'){
						$icon = 'fa-check-circle-o';
						$color = 'green';
					} elseif($status == 'pending'){
						$icon = 'fa-times-circle-o';
						$color = 'red';
					} else {
						$icon = 'fa-window-minimize';
						$color = 'red';
					}
				} else {
					$status = 'pending';
					$icon = 'fa-window-minimize';
					$color = 'red';
				}
				
				$newMenu = $args->addChild(
					'uniqueMenuItemName',
					array(
						'name' => 'Home',
						'label' => Lang::trans('smsir_account_status'),
						'uri' => 'clientarea.php',
						'order' => 99,
						'icon' => 'fa-user-o',
					)
				);
				$newMenu->addChild(
					'uniqueSubMenuItemName',
					array(
						'name' => 'Item Name 1',
						'label' => Lang::trans('smsir_account_status').' - <span style="color: '.$color.';">'.Lang::trans('smsir_'.$status).'</span>',
						'uri' => 'clientverification.php',
						'order' => 10,
						'icon' => $icon,
					)
				);
			}
		}
    }
}

return $hook;
