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

class SMSIR
{
    var $sender;

    public $params;
    public $gsmnumber;
    public $message;

    public $userid;
    var $errors = array();
    var $logs = array();

    /**
     * @param mixed $gsmnumber
     */
    public function setGsmnumber($gsmnumber)
    {
        $this->gsmnumber = $this->util_gsmnumber($gsmnumber);
    }

    /**
     * @return mixed
     */
    public function getGsmnumber()
    {
        return $this->gsmnumber;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param int $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $settings = $this->getSettings();
        $params = json_decode($settings['apiparams']);
        return $params;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        $settings = $this->getSettings();

        if (!$settings['api']) {
            $this->addError("invalid api");
            $this->addLog("invalid api");
            return false;
        } else {
            return $settings['api'];
        }
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $sql = "SELECT * FROM `mod_smsir_settings`";
        $result = mysql_query($sql);
        return mysql_fetch_array($result);
    }

    function send($numsarray)
    {
				
        $sender_function = strtolower($this->getSender());
        if ($sender_function == false) {
            return false;
        } else {
            $params = $this->getParams();
            $message = $this->message;
            $message .= " " . $params->signature;

			$this->addLog("Params: " . json_encode($params));
            $this->addLog("To: " . json_encode($numsarray));
            $this->addLog("Message: " . $message);
            $this->addLog("SenderClass: " . $sender_function);

            include_once("senders/" . $sender_function . ".php");
            $sender = new $sender_function(trim($message));
            $result = $sender->send($numsarray);

			foreach ($result['log'] as $log) {
                $this->addLog($log);
            }
            if ($result['error']) {
                foreach ($result['error'] as $error) {
                    $this->addError($error);
                }

                $this->saveToDb(null, 'error', $this->getErrors(), $this->getLogs());
                return false;
            } else {
                $this->saveToDb(null, 'success', null, $this->getLogs());
                return true;
            }
        }
    }

	function sendtocustomerclub($message){
		$sender_function = strtolower($this->getSender());
		include_once("senders/" . $sender_function . ".php");
		if(class_exists($sender_function)){
			$sender = new $sender_function($message);
			$result = $sender->SendSMStoCustomerclubContacts($message);
			return $result;
		}
	}
	
	function sendverification($user_mobile,$code){
		$sender_function = strtolower($this->getSender());
		include_once("senders/" . $sender_function . ".php");
		if(class_exists($sender_function)){
			$sender = new $sender_function($message);
			$result = $sender->SendSMSforVerification($code,$user_mobile);
			return $result;
		}
	}

	function sendtoclients($message){
	
        $where = array("value" => $template);
        $result = select_query("tblcustomfieldsvalues", "value", '');
        while($row = mysql_fetch_assoc($result)){
			if($row['value']){
				$nums[] = doubleval($row['value']);
			}
		}
		
		@$numsarray = array_unique($nums);
		
		$sender_function = strtolower($this->getSender());
		include_once("senders/" . $sender_function . ".php");
		$sender = new $sender_function(trim($message));
		$result = $sender->send($numsarray);
		
		foreach ($result['log'] as $log) {
			$this->addLog($log);
		}
		if ($result['error']) {
			foreach ($result['error'] as $error) {
				$this->addError($error);
			}

			$this->saveToDb(null, 'error', $this->getErrors(), $this->getLogs());
			return false;
		} else {
			$this->saveToDb(null, 'success', null, $this->getLogs());
			return true;
		}
	}

    function getBalance()
    {
        $sender_function = strtolower($this->getSender());
        if ($sender_function == false) {
            return false;
        } else {
			include_once("senders/" . $sender_function . ".php");
			if(class_exists($sender_function)){
				$sender = new $sender_function("", "");
				return $sender->balance();
			}
        }
    }

    function getSenders()
    {
        if ($handle = opendir(dirname(__FILE__) . '/senders')) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, strlen($entry) - 4, strlen($entry)) == ".php") {
                    $file[] = require_once('senders/' . $entry);
                }
            }
            closedir($handle);
        }
        return $file;
    }

    function getHooks()
    {
        if ($handle = opendir(dirname(__FILE__) . '/hooks')) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, strlen($entry) - 4, strlen($entry)) == ".php") {
                    $file[] = require_once('hooks/' . $entry);
                }
            }
            closedir($handle);
        }
        return $file;
    }

    function saveToDb($msgid, $status, $errors = null, $logs = null)
    {
        $now = date("Y-m-d H:i:s");
        $table = "mod_smsir_messages";
        $values = array(
            "sender" => $this->getSender(),
            "to" => $this->getGsmnumber(),
            "text" => $this->getMessage(),
            "msgid" => $msgid,
            "status" => $status,
            "errors" => $errors,
            "logs" => $logs,
            "user" => $this->getUserid(),
            "datetime" => $now
        );
        insert_query($table, $values);

        $this->addLog("message saved on database");
    }

    /* Default number format */
    function util_gsmnumber($number)
    {
        $replacefrom = array('-', '(', ')', '.', ',', '+', ' ');
        $number = str_replace($replacefrom, '', $number);

        return $number;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function addLog($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $res = '<div class="errorbox"><br>';
        foreach ($this->errors as $d) {
            $res .= "<li>$d</li>";
        }
        $res .= '</div>';
        return $res;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        $res = '<pre><p><strong>Debug Result</strong><ul>';
        foreach ($this->logs as $d) {
            $res .= "<li>$d</li>";
        }
        $res .= '</ul></p></pre>';
        return $res;
    }

    /*
     * Runs at addon install/update
     * This function controls that if there is any change at hooks files. Such as new hook, variable changes at hooks.
     */
    function checkHooks($hooks = null)
    {
        if ($hooks == null) {
            $hooks = $this->getHooks();
        }

        $i = 0;
        foreach ($hooks as $hook) {
            $sql = "SELECT `id` FROM `mod_smsir_templates` WHERE `name` = '" . $hook['function'] . "' AND `type` = '" . $hook['type'] . "' LIMIT 1";
            $result = mysql_query($sql);
            $num_rows = mysql_num_rows($result);
            if ($num_rows == 0) {
                if ($hook['type']) {
                    $values = array(
                        "name" => $hook['function'],
                        "type" => $hook['type'],
                        "template" => $hook['defaultmessage'],
                        "variables" => $hook['variables'],
                        "extra" => $hook['extra'],
                        "description" => json_encode(@$hook['description']),
                        "active" => 1
                    );
                    insert_query("mod_smsir_templates", $values);
                    $i++;
                }
            } else {
                $values = array(
                    "variables" => $hook['variables']
                );
                update_query("mod_smsir_templates", $values, "name = '" . $hook['name'] . "'");
            }
        }
        return $i;
    }

    function getTemplateDetails($template = null)
    {
        $where = array("name" => $template);
        $result = select_query("mod_smsir_templates", "*", $where);
        $data = mysql_fetch_assoc($result);

        return $data;
    }
	
	function ConvertFarsiNumToEnglish($string) {
		$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

		$num = range(0, 9);
		$englishNumbersOnly = str_replace($persian, $num, $string);

		return $englishNumbersOnly;
	}
}
