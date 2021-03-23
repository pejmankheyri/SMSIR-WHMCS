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
    'hook' => 'InvoiceCreationPreEmail',
    'function' => 'InvoiceCreationPreEmail',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_InvoiceCreationPreEmail'),
        'english' => Lang::trans('smsir_hook_title_InvoiceCreationPreEmail')
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => '{firstname} {lastname} عزیز، فاکتور با سررسید {duedate} به مبلغ {total} ایجاد شده است. شماره فاکتور: {invoiceid} می باشد. برای مشاهده جزئیات لطفا به وبسایت مراجعه نمایید.',
    'variables' => '{firstname}, {lastname}, {duedate}, {total}, {invoiceid}'
);
if (!function_exists('InvoiceCreationPreEmail')) {
    function InvoiceCreationPreEmail($args)
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

        $userSql = "SELECT `a`.`id`,`a`.`total`,`a`.`duedate`,`b`.`id` as `userid`,`b`.`firstname`,`b`.`lastname`,`c`.`value` as `gsmnumber` 
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
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['duedate'], $UserInformation['total'], $args['invoiceid']);
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
