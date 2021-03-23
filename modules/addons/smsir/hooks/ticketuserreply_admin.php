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
    'hook' => 'TicketUserReply',
    'function' => 'TicketUserReply_admin',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_TicketUserReply_admin'),
        'english' => Lang::trans('smsir_hook_title_TicketUserReply_admin')
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'تیکت ({subject}) از جانب مشتری پاسخ داده شد.',
    'variables' => '{subject}'
);

if (!function_exists('TicketUserReply_admin')) {
    function TicketUserReply_admin($args)
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
        $admingsm = explode(",", $template['admingsm']);

        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom = explode(",", $template['variables']);
        $replaceto = array($args['subject']);
        $message = str_replace($replacefrom, $replaceto, $template['template']);

		$class->setGsmnumber(json_encode($admingsm));
		$class->setUserid(0);
		$class->setMessage($message);
		$class->send($admingsm);
    }
}

return $hook;
