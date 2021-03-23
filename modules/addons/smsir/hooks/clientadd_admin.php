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
    'hook' => 'ClientAdd',
    'function' => 'ClientAdd_admin',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_ClientAdd_admin'),
        'english' => Lang::trans('smsir_hook_title_ClientAdd_admin')
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'کاربر جدیدی در سایت ثبت نام کرد.',
    'variables' => ''
);
if (!function_exists('ClientAdd_admin')) {
    function ClientAdd_admin($args)
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

		$class->setGsmnumber(json_encode($admingsm));
		$class->setUserid(0);
		$class->setMessage($template['template']);
		$class->send($admingsm);
    }
}
return $hook;