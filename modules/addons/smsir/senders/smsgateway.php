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

use WHMCS\Database\Capsule;

class smsgateway extends SMSIR implements SmsSenderInterface
{

	/**
	 * gets API Customer Club Send To Categories Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getAPICustomerClubSendToCategoriesUrl()
	{
		return "api/CustomerClub/SendToCategories";
	}

	/**
	 * gets API Message Send Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getAPIMessageSendUrl()
	{
		return "api/MessageSend";
	}

	/**
	 * gets API Customer Club Add Contact And Send Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getAPICustomerClubAddAndSendUrl()
	{
		return "api/CustomerClub/AddContactAndSend";
	}

	/**
	 * gets API credit Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getAPIcreditUrl()
	{
		return "api/credit";
	}

	/**
	 * gets API Verification Code Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getAPIVerificationCodeUrl()
	{
		return "api/VerificationCode";
	}

	/**
	 * gets Api Token Url.
	 *
	 * @return string Indicates the Url
	 */
	protected function getApiTokenUrl()
	{
		return "api/Token";
	}

	/**
	 * gets Module Main Page.
	 *
	 * @return string Indicates the Url
	 */
	protected function ModuleMainPage()
	{
		return "addonmodules.php?module=smsir";
	}

	/**
	 * gets Module Name and Version.
	 *
	 * @return string Indicates the Module Name and Version
	 */
	protected function ModuleNameVersion()
	{
		return "whmcs_v_2_1";
	}

	/**
	 * gets System Name.
	 *
	 * @return string Indicates the System Name
	 */
	protected function SystemName()
	{
		return "whmcs";
	}

	/**
	 * gets domain Name.
	 *
	 * @return string Indicates the domain Name
	 */
	protected function DomainName()
	{
		$domain = strtolower($_SERVER['SERVER_NAME']);
		return $domain;
	}

	/**
	 * gets Encryption Keys.
	 *
	 * @return string Indicates the Encryption Keys
	 */
	protected function EncryptionKeys()
	{
		$obj = (object) array(
			'key' => 'jE2QduaIVtBpn4gZ44iAP1/RsTSAHQXnnbsbvlnaakY=',
			'iv' => '5IdVihSe08G1/CT25JVunKLx2Na0HTZNpf9JdVIx9MY='
		);
		return $obj;
	}

	/**
	 *
	 * @return void
	 */
	public function __construct($message)
	{
		$this->message = $message;
	}

	public function send($numsarray)
	{
		try {
			$number = $numsarray;
			$message = $this->message;

			$result = $this->SendSMS($number);
			$log[] = "Request send message: " . $message . 'to number: ' . json_encode($number);
			$log[] = "smsGateway server response returned: " . $result;

			if ($result == true) {
				$this->addLog("Message was sent Status: true");
				$log[] = "Message sent Status: true";
			} else {
				$log[] = "Unable to send message. error : false";
				$error[] = "An error occurred while sending messages.";
			}
			return array(
				'log' => $log,
				'error' => $error
			);
		} catch (Exeption $e) {
			echo 'Error send : ' . $e->getMessage();
		}
	}

	public function balance()
	{
		try {
			$device_info = $this->GetCredit();
			if ($device_info) {
				return $device_info;
			} else {
				return false;
			}
		} catch (Exeption $e) {
			echo 'Error balance : ' . $e->getMessage();
		}
	}

	/**
	 * Send SMS.
	 *
	 * @param number[] $number array structure of numbers
	 * @return boolean
	 */
	public function SendSMS($number)
	{
		try {

			if ($number) {
				$params = $this->getParams();

				foreach ($number as $key => $value) {

					if (($this->is_mobile($value)) || ($this->is_mobile_withz($value))) {
						$numberr[] = doubleval($value);
					}
				}

				@$numbers = array_unique($numberr);

				if (is_array($numbers) && $numbers) {
					foreach ($numbers as $key => $value) {
						$Messages[] = $this->message;
					}
				}

				date_default_timezone_set('Asia/Tehran');

				$SendDateTime = date("Y-m-d") . "T" . date("H:i:s");

				if ($params->iscustomerclub == 'on') {

					foreach ($numbers as $num_keys => $num_vals) {
						$contacts[] = array(
							"Prefix" => "",
							"FirstName" => "",
							"LastName" => "",
							"Mobile" => $num_vals,
							"BirthDay" => "",
							"CategoryId" => "",
							"MessageText" => $this->message
						);
					}

					$CustomerClubInsertAndSendMessage = $this->CustomerClubInsertAndSendMessage($contacts);

					if ($CustomerClubInsertAndSendMessage == true) {
						return true;
					} else {
						return false;
					}
				} else {

					$SendMessage = $this->SendMessage($numbers, $Messages, $SendDateTime);

					if ($SendMessage == true) {
						return true;
					} else {
						return false;
					}
				}
			}
		} catch (Exeption $e) {
			echo 'Error SendSMS : ' . $e->getMessage();
		}
	}

	/**
	 * Customer Club Send To Categories.
	 *
	 * @param Messages[] $Messages array structure of messages
	 * @param contactsCustomerClubCategoryIds[] $contactsCustomerClubCategoryIds array structure of contacts Customer Club Category Ids
	 * @param string $SendDateTime Send Date Time
	 * @return string Indicates the sent sms result
	 */
	public function SendSMStoCustomerclubContacts($Messages)
	{
		try {
			$contactsCustomerClubCategoryIds = array();
			$params = $this->getParams();
			$token = $this->GetToken($params->apikey, $params->secretkey);
			if ($token != false) {
				$postData = array(
					'Messages' => $Messages,
					'contactsCustomerClubCategoryIds' => $contactsCustomerClubCategoryIds,
					'SendDateTime' => '',
					'CanContinueInCaseOfError' => 'false'
				);

				$url = $params->apidomain . $this->getAPICustomerClubSendToCategoriesUrl();
				$CustomerClubSendToCategories = $this->execute($postData, $url, $token);
				$object = json_decode($CustomerClubSendToCategories);

				if (is_object($object)) {
					$array = get_object_vars($object);
					if (is_array($array)) {
						if ($array['IsSuccessful'] == true) {
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				header("location: " . $this->ModuleMainPage());
				exit;
			}
		} catch (Exeption $e) {
			echo 'Error SendSMStoCustomerclubContacts : ' . $e->getMessage();
		}
	}


	/**
	 * Verification Code.
	 *
	 * @param string $Code Code
	 * @param string $MobileNumber Mobile Number
	 * @return string Indicates the sent sms result
	 */
	public function SendSMSforVerification($Code, $MobileNumber)
	{
		try {
			$params = $this->getParams();
			$token = $this->GetToken($params->APIKey, $params->SecretKey);
			if ($token != false) {
				$postData = array(
					'Code' => $Code,
					'MobileNumber' => $MobileNumber,
				);

				$url = $params->apidomain . $this->getAPIVerificationCodeUrl();
				$VerificationCode = $this->execute($postData, $url, $token);
				$object = json_decode($VerificationCode);

				if (is_object($object)) {
					$array = get_object_vars($object);
					if (is_array($array)) {
						$result = $array['Message'];
					} else {
						$result = false;
					}
				} else {
					$result = false;
				}
			} else {
				header("location: " . $this->ModuleMainPage());
				exit;
			}
			return $result;
		} catch (Exeption $e) {
			echo 'Error SendSMSforVerification : ' . $e->getMessage();
		}
	}

	/**
	 * Get Credit.
	 *
	 * @return string Indicates the sent sms result
	 */
	public function GetCredit()
	{
		try {
			$params = $this->getParams();
			$postData = array(
				'UserApiKey' => $params->apikey,
				'SecretKey' => $params->secretkey,
				'System' => $this->ModuleNameVersion()
			);
			$postString = json_encode($postData);

			$ch = curl_init($params->apidomain . $this->getApiTokenUrl());
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

			$result = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($result);

			if (is_object($response)) {
				$resultVars = get_object_vars($response);
				if (is_array($resultVars)) {
					@$IsSuccessful = $resultVars['IsSuccessful'];
					if ($IsSuccessful == true) {
						@$TokenKey = $resultVars['TokenKey'];
						$token = $TokenKey;
					} else {
						$token = false;
					}
				}
			}

			if ($token != false) {

				$url = $params->apidomain . $this->getAPIcreditUrl();
				$GetCredit = $this->executeCredit($url, $token);

				$object = json_decode($GetCredit);

				if (is_object($object)) {
					$array = get_object_vars($object);

					if (is_array($array)) {
						if ($array['IsSuccessful'] == true) {
							$result = $array['Credit'];
						} else {
							$result = $array['Message'];
						}
					} else {
						$result = false;
					}
				} else {
					$result = false;
				}
			} else {
				$result = false;
			}
			return $result;
		} catch (Exeption $e) {
			echo 'Error GetCredit : ' . $e->getMessage();
		}
	}

	/**
	 * send sms.
	 *
	 * @param MobileNumbers[] $MobileNumbers array structure of mobile numbers
	 * @param Messages[] $Messages array structure of messages
	 * @param string $SendDateTime Send Date Time
	 * @return string Indicates the sent sms result
	 */
	public function SendMessage($MobileNumbers, $Messages, $SendDateTime = '')
	{
		try {
			$params = $this->getParams();
			$token = $this->GetToken($params->apikey, $params->secretkey);

			if ($token != false) {
				$postData = array(
					'Messages' => $Messages,
					'MobileNumbers' => $MobileNumbers,
					'LineNumber' => $params->senderid,
					'SendDateTime' => $SendDateTime,
					'CanContinueInCaseOfError' => 'false'
				);

				$url = $params->apidomain . $this->getAPIMessageSendUrl();
				$SendMessage = $this->execute($postData, $url, $token);
				$object = json_decode($SendMessage);

				if (is_object($object)) {
					$array = get_object_vars($object);
					if (is_array($array)) {
						if ($array['IsSuccessful'] == true) {
							$result = true;
						} else {
							$result = false;
						}
					} else {
						$result = false;
					}
				} else {
					$result = false;
				}
			} else {
				header("location: " . $this->ModuleMainPage());
				exit;
			}
			return $result;
		} catch (Exeption $e) {
			echo 'Error SendMessage : ' . $e->getMessage();
		}
	}

	/**
	 * Customer Club Insert And Send Message.
	 *
	 * @param data[] $data array structure of contacts data
	 * @return string Indicates the sent sms result
	 */
	public function CustomerClubInsertAndSendMessage($data)
	{
		try {
			$params = $this->getParams();
			$token = $this->GetToken($params->apikey, $params->secretkey);
			if ($token != false) {
				$postData = $data;

				$url = $params->apidomain . $this->getAPICustomerClubAddAndSendUrl();
				$CustomerClubInsertAndSendMessage = $this->execute($postData, $url, $token);
				$object = json_decode($CustomerClubInsertAndSendMessage);

				if (is_object($object)) {
					$array = get_object_vars($object);
					if (is_array($array)) {
						if ($array['IsSuccessful'] == true) {
							$result = true;
						} else {
							$result = false;
						}
					} else {
						$result = false;
					}
				} else {
					$result = false;
				}
			} else {
				header("location: " . $this->ModuleMainPage());
				exit;
			}
			return $result;
		} catch (Exeption $e) {
			echo 'Error CustomerClubInsertAndSendMessage : ' . $e->getMessage();
		}
	}

	/**
	 * gets token key for all web service requests.
	 *
	 * @return string Indicates the token key
	 */
	private function GetToken()
	{
		try {
			$params = $this->getParams();
			$postData = array(
				'UserApiKey' => $params->apikey,
				'SecretKey' => $params->secretkey,
				'System' => $this->ModuleNameVersion()
			);
			$postString = json_encode($postData);

			$ch = curl_init($params->apidomain . $this->getApiTokenUrl());
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

			$result = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($result);

			if (is_object($response)) {
				$resultVars = get_object_vars($response);
				if (is_array($resultVars)) {
					@$IsSuccessful = $resultVars['IsSuccessful'];
					if ($IsSuccessful == true) {
						@$TokenKey = $resultVars['TokenKey'];
						$resp = $TokenKey;
					} else {
						$resp = false;
					}
				}
			}

			return $resp;
		} catch (Exeption $e) {
			echo 'Error GetToken : ' . $e->getMessage();
		}
	}

	/**
	 * executes the main method.
	 *
	 * @param postData[] $postData array of json data
	 * @param string $url url
	 * @param string $token token string
	 * @return string Indicates the curl execute result
	 */
	private function execute($postData, $url, $token)
	{
		try {
			$postString = json_encode($postData);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'x-sms-ir-secure-token: ' . $token
			));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		} catch (Exeption $e) {
			echo 'Error execute : ' . $e->getMessage();
		}
	}
	/**
	 * executes the main method.
	 *
	 * @param string $url url
	 * @param string $token token string
	 * @return string Indicates the curl execute result
	 */
	private function executeCredit($url, $token)
	{
		try {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'x-sms-ir-secure-token: ' . $token
			));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		} catch (Exeption $e) {
			echo 'Error executeCredit : ' . $e->getMessage();
		}
	}

	/**
	 * check if mobile number is valid.
	 *
	 * @param string $mobile mobile number
	 * @return boolean Indicates the mobile validation
	 */
	public function is_mobile($mobile)
	{
		if (preg_match('/^09(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * check if mobile with zero number is valid.
	 *
	 * @param string $mobile mobile with zero number
	 * @return boolean Indicates the mobile with zero validation
	 */
	public function is_mobile_withz($mobile)
	{
		if (preg_match('/^9(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
			return true;
		} else {
			return false;
		}
	}
}

return array(
	'value' => 'smsgateway',
	'label' => 'SMS Gateway',
	'fields' => array(
		'apikey', 'secretkey'
	)
);
