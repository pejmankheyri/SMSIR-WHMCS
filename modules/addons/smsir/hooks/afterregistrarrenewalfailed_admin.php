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
    'hook' => 'AfterRegistrarRenewalFailed',
    'function' => 'AfterRegistrarRenewalFailed_admin',
    'description' => array(
        'farsi' => Lang::trans('smsir_hook_title_AfterRegistrarRenewalFailed_admin'),
        'english' => Lang::trans('smsir_hook_title_AfterRegistrarRenewalFailed_admin')
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'هنگام تمدید دامنه خطایی رخ داده است. {domain}',
    'variables' => '{domain}'
);
if (!function_exists('AfterRegistrarRenewalFailed_admin')) {
    function AfterRegistrarRenewalFailed_admin($args)
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
        $replaceto = array($args['params']['sld'] . "." . $args['params']['tld']);
        $message = str_replace($replacefrom, $replaceto, $template['template']);

		$class->setGsmnumber(json_encode($admingsm));
		$class->setUserid(0);
		$class->setMessage($message);
		$class->send($admingsm);
    }
}

return $hook;