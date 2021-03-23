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
 
if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

require_once("smsclass.php");
require_once("senders/SmsSenderInterface.php");
$class = new SMSIR();
$hooks = $class->getHooks();

foreach ($hooks as $hook) {
    add_hook($hook['hook'], 1, $hook['function'], "");
}