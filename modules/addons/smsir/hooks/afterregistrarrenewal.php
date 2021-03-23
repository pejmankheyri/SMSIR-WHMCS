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
    'hook' => 'AfterRegistrarRenewal',
    'function' => 'AfterRegistrarRenewal',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_AfterRegistrarRenewal'),
        'english' => Lang::trans('smsir_hook_title_AfterRegistrarRenewal')
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => '{firstname} {lastname} عزیز، تمدید دامنه شما با موفقیت انجام شد. ({domain})',
    'variables' => '{firstname},{lastname},{domain}'
);
if (!function_exists('AfterRegistrarRenewal')) {
    function AfterRegistrarRenewal($args)
    {
        $class = new SMSIR();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        if (!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield']) {
            return null;
        }

        $userSql = "SELECT `a`.`id`,`b`.`id` as `userid`,`b`.`firstname`, `b`.`lastname`, `c`.`value` as `gsmnumber`
			FROM `tbldomains` as `a`
			JOIN `tblclients` as `b` ON `b`.`id` = `a`.`userid`
			JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `b`.`id`
			WHERE `a`.`id` = '" . $args['params']['domainid'] . "'
			AND `c`.`fieldid` = '" . $settings['gsmnumberfield'] . "'
			LIMIT 1";

		$result = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['sld'] . "." . $args['params']['tld']);
            $message = str_replace($replacefrom, $replaceto, $template['template']);

			$nums[] = $UserInformation['gsmnumber'];
            $class->setGsmnumber(json_encode($UserInformation['gsmnumber']));
            $class->setUserid($args['params']['userid']);
            $class->setMessage($message);
            $class->send($nums);
        }
    }
}

return $hook;