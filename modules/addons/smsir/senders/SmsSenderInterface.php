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
 
interface SmsSenderInterface
{
    /**
     * SmsSenderInterface constructor.
     * @param string $message
     */
    public function __construct($message);

    /**
     * @return mixed
     */
    public function send($numsarray);

    /**
     * @return mixed
     */
    public function balance();
}