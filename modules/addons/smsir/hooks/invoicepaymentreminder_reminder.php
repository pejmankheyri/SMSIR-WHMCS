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
    'hook' => 'InvoicePaymentReminder',
    'function' => 'InvoicePaymentReminder_Reminder',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_InvoicePaymentReminder_Reminder'),
        'english' => Lang::trans('smsir_hook_title_InvoicePaymentReminder_Reminder')
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => '{firstname} {lastname} عزیز، فاکتور به تاریخ {duedate} نیاز به پرداخت دارد. برای جزئیات بیشتر به وبسایت ما مراجعه نمایید',
    'variables' => '{firstname}, {lastname}, {duedate}'
);

if (!function_exists('InvoicePaymentReminder_Reminder')) {
    function InvoicePaymentReminder_Reminder($args)
    {

        if ($args['type'] == "reminder") {
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
            return false;
        }

        $userSql = "SELECT `a`.`id`,`a`.`duedate`,`b`.`id` as `userid`,`b`.`firstname`,`b`.`lastname`,`c`.`value` as `gsmnumber` 
			FROM `tblinvoices` as `a`
			JOIN `tblclients` as `b` ON `b`.`id` = `a`.`userid`
			JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`userid`
			WHERE `a`.`id` = '" . $args['invoiceid'] . "'
			AND `c`.`fieldid` = '" . $settings['gsmnumberfield'] . "'
			LIMIT 1";

        $result = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['duedate']);
            $message = str_replace($replacefrom, $replaceto, $template['template']);

			$nums[] = $UserInformation['gsmnumber'];
            $class->setGsmnumber(json_encode($UserInformation['gsmnumber']));
            $class->setMessage($message);
            $class->setUserid($UserInformation['userid']);
            $class->send($nums);
        }
    }
}

return $hook;