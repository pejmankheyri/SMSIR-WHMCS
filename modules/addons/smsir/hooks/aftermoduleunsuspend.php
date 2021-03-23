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
    'hook' => 'AfterModuleUnsuspend',
    'function' => 'AfterModuleUnsuspend',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_AfterModuleUnsuspend'),
        'english' => Lang::trans('smsir_hook_title_AfterModuleUnsuspend')
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => '{firstname} {lastname} عزیز، سرویس شما مجددا فعال شد. ({domain})',
    'variables' => '{firstname},{lastname},{domain}'
);
if (!function_exists('AfterModuleUnsuspend')) {
    function AfterModuleUnsuspend($args)
    {
        $type = $args['params']['producttype'];

        if ($type == "hostingaccount") {
            $class = new SMSIR();
            $template = $class->getTemplateDetails(__FUNCTION__);
            if ($template['active'] == 0) {
                return null;
            }
            $settings = $class->getSettings();
            if (!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield']) {
                return null;
            }
        } else {
            return null;
        }

        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
			FROM `tblclients` as `a`
			JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
			WHERE `a`.`id`  = '" . $args['params']['clientsdetails']['userid'] . "'
			AND `b`.`fieldid` = '" . $settings['gsmnumberfield'] . "'
			LIMIT 1";

        $result = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['domain']);
            $message = str_replace($replacefrom, $replaceto, $template['template']);

			$nums[] = $UserInformation['gsmnumber'];
            $class->setGsmnumber(json_encode($UserInformation['gsmnumber']));
            $class->setUserid($args['params']['clientsdetails']['userid']);
            $class->setMessage($message);
            $class->send($nums);
        }
    }
}
return $hook;