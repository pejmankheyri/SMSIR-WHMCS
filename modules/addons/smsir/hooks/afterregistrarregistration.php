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
    'hook' => 'AfterRegistrarRegistration',
    'function' => 'AfterRegistrarRegistration',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_AfterRegistrarRegistration'),
        'english' => Lang::trans('smsir_hook_title_AfterRegistrarRegistration')
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => '{firstname} {lastname} عزیز، دامنه جدید با موفقیت ثبت شد. ({domain})',
    'variables' => '{firstname},{lastname},{domain}'
);
if (!function_exists('AfterRegistrarRegistration')) {
    function AfterRegistrarRegistration($args)
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

        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
			FROM `tblclients` as `a`
			JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
			WHERE `a`.`id` = '" . $args['params']['userid'] . "'
			AND `b`.`fieldid` = '" . $settings['gsmnumberfield'] . "'
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