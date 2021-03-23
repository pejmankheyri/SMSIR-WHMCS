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

use WHMCS\Database\Capsule;

function smsir_config() {
    $configarray = array(
        "name" => "SMSir",
        "description" => "sms.ir module for whmcs",
        "version" => "2.1",
        "author" => "Pejman kheyri",
        "language" => "farsi",
    );
    return $configarray;
}

function smsir_activate() {

    $query = "CREATE TABLE IF NOT EXISTS `mod_smsir_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`sender` varchar(40) NOT NULL,`to` text DEFAULT NULL,`text` text,`msgid` varchar(50) DEFAULT NULL,`status` varchar(10) DEFAULT NULL,`errors` text,`logs` text,`user` int(11) DEFAULT NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_smsir_settings` (`id` int(11) NOT NULL AUTO_INCREMENT,`api` varchar(40) CHARACTER SET utf8 NOT NULL,`apiparams` varchar(1000) CHARACTER SET utf8 NOT NULL,`gsmnumberfield` int(11) DEFAULT NULL,`version` varchar(6) CHARACTER SET utf8 DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    mysql_query($query);

    $query = "INSERT INTO `mod_smsir_settings` (`api`, `apiparams`, `gsmnumberfield`, `version`) VALUES ('', '', '', '');";
    mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_smsir_templates` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) CHARACTER SET utf8 NOT NULL,`type` enum('client','admin') CHARACTER SET utf8 NOT NULL,`admingsm` varchar(255) CHARACTER SET utf8 NOT NULL,`template` varchar(240) CHARACTER SET utf8 NOT NULL,`variables` varchar(500) CHARACTER SET utf8 NOT NULL,`active` tinyint(1) NOT NULL,`extra` varchar(3) CHARACTER SET utf8 NOT NULL,`description` text CHARACTER SET utf8,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_smsir_verifications` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) NOT NULL,`mobile` varchar(11) NOT NULL,`code` int(11) NOT NULL,`status` varchar(50) CHARACTER SET utf8 NOT NULL,`add_time` varchar(10) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";
    mysql_query($query);

    //Creating hooks
    require_once("smsclass.php");
    $class = new SMSIR();
    $class->checkHooks();

    return array('status' => 'success', 'description' => 'Smsir succesfully activated :)');
}

function smsir_deactivate() {

    //$query = "DROP TABLE `mod_smsir_templates`";
    //mysql_query($query);
    $query = "DROP TABLE `mod_smsir_settings`";
    mysql_query($query);
    //$query = "DROP TABLE `mod_smsir_messages`";
    //mysql_query($query);
    //$query = "DROP TABLE `mod_smsir_verifications`";
    //mysql_query($query);

    return array('status' => 'success', 'description' => 'Smsir succesfully deactivated :(');
}

function smsir_upgrade($vars) {
	//
}

function smsir_output($vars) {
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $LANG = $vars['_lang'];
    putenv("TZ=Asia/Tehran");

    $class = new SMSIR();

    $tab = $_GET['tab'];
    echo '
	<style type="text/css">
		.a_tab{
			padding: 5px 10px;
			border: 1px solid #cccccc;
		}
		#a_tab{
			background-color: #1A4D80;
			color: #ffffff;
		}
	</style> 
	<ul class="nav nav-tabs client-tabs" style="direction: rtl;float: right;">
		<li class="tab"><a class="clientTab-6" ' . (($tab == "verifications") ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&amp;tab=verifications">' . Lang::trans('smsir_verifications') . '</a></li>
		<li class="tab"><a class="clientTab-5" ' . (($tab == "messages") ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&amp;tab=messages">' . Lang::trans('smsir_messages') . '</a></li>
		<li class="tab"><a class="clientTab-4" ' . (($tab == "sendbulk") ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&tab=sendbulk">' . Lang::trans('smsir_sendsms') . '</a></li>
		<li class="tab"><a class="clientTab-3" ' . ((@$_GET['type'] == "admin") ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&tab=templates&type=admin">' . Lang::trans('smsir_adminsmstemplates') . '</a></li>
		<li class="tab"><a class="clientTab-2" ' . ((@$_GET['type'] == "client") ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&tab=templates&type=client">' . Lang::trans('smsir_clientsmstemplates') . '</a></li>
		<li class="tab"><a class="clientTab-1" ' . ((($tab == "settings") || ($tab == "")) ? "id='a_tab'" : "") . '" href="addonmodules.php?module=smsir&tab=settings">' . Lang::trans('smsir_settings') . '</a></li>
	</ul>
	<div class="clear"></div>
    ';
    if (!isset($tab) || $tab == "settings") {
        /* UPDATE SETTINGS */
        if ($_POST['params']) {
            $update = array(
                "api" => $_POST['api'],
                "apiparams" => json_encode($_POST['params']),
                'gsmnumberfield' => $_POST['gsmnumberfield']
            );
            update_query("mod_smsir_settings", $update, "");
        }
        /* UPDATE SETTINGS */

        $settings = $class->getSettings();
        $apiparams = json_decode($settings['apiparams']);
		
		$result = Capsule::table('tblcustomfields')->where([
			['fieldtype', '=', 'text'],
			['type', '=', 'client'],
		])->get();
		foreach($result as $customfield){
			if ($customfield->id == $settings['gsmnumberfield']) {
				$selected = 'selected="selected"';
			} else {
				$selected = "";
			}
			$gsmnumber .= '<option value="' . $customfield->id . '" ' . $selected . '>' . $customfield->fieldname . '</option>';
		}
		
        $classers = $class->getSenders();
		$classersfields = '';
		foreach($classers as $classersKey => $classersval){
			if(is_array($classersval)){
				foreach ($classersval['fields'] as $field) {
					if($field == 'secretkey'){
						$inputtype = 'password';
					} else {
						$inputtype = 'text';
					}
					$classersfields .=
						'<tr>
							<td class="fieldlabel" width="30%">' . Lang::trans('smsir_'.$field) . '</td>
							<td class=""><input class="form-control" type="'.$inputtype.'" name="params[' . $field . ']" size="40" value="' . $apiparams->$field . '"></td>
						</tr>';
						
				}			
			}
		}
		
		foreach (Capsule::table('tblclientgroups')->get() as $clientgroup) {
			if ($clientgroup->id == $apiparams->clientgroups) {
						$selected = 'selected="selected"';
					} else {
						$selected = "";
					}
					$clientgroups .= '<option value="' . $clientgroup->id . '" ' . $selected . '>' . $clientgroup->groupname . '</option>';
		}

		
		if($apiparams->iscustomerclub == "on"){
			$iscustomerclubchecked = 'checked';
		} else {
			$iscustomerclubchecked = '';
		}
		
		if($apiparams->registerwithverification == "on"){
			$registerwithverificationchecked = 'checked';
		} else {
			$registerwithverificationchecked = '';
		}

		if($apiparams->loginwithverification == "on"){
			$loginwithverificationchecked = 'checked';
		} else {
			$loginwithverificationchecked = '';
		}
		
		if($apiparams->clientareacartwithverification == "on"){
			$clientareacartwithverificationchecked = 'checked';
		} else {
			$clientareacartwithverificationchecked = '';
		}

		if($apiparams->clientareaemailwithverification == "on"){
			$clientareaemailwithverificationchecked = 'checked';
		} else {
			$clientareaemailwithverificationchecked = '';
		}

		if($apiparams->clientareaproductwithverification == "on"){
			$clientareaproductwithverificationchecked = 'checked';
		} else {
			$clientareaproductwithverificationchecked = '';
		}

		if($apiparams->clientareadomainwithverification == "on"){
			$clientareadomainwithverificationchecked = 'checked';
		} else {
			$clientareadomainwithverificationchecked = '';
		}

		if($apiparams->clientareainvoicewithverification == "on"){
			$clientareainvoicewithverificationchecked = 'checked';
		} else {
			$clientareainvoicewithverificationchecked = '';
		}

		if($apiparams->clientareaaddfundwithverification == "on"){
			$clientareaaddfundwithverificationchecked = 'checked';
		} else {
			$clientareaaddfundwithverificationchecked = '';
		}

		if($apiparams->showinsummary == "on"){
			$showinsummarychecked = 'checked';
		} else {
			$showinsummarychecked = '';
		}

		if($apiparams->showinsidebar == "on"){
			$showinsidebarchecked = 'checked';
		} else {
			$showinsidebarchecked = '';
		}

		if($apiparams->validateday){
			$validateday = $apiparams->validateday;
		} else {
			$validateday = 90;
		}
		
		if($class->getBalance() == false){
			$getBalance = '-';
		} else {
			$getBalance = $class->getBalance();
		}

        echo '
        <form action="" method="post" id="form">
        <input type="hidden" name="action" value="save" />
            <div style="text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
                <table dir="rtl" class="form" width="33%" border="0" cellspacing="2" cellpadding="3" style="height: 550px;float: right;">
                    <tbody>
                        <tr style="display: none;">
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_sender') . '</td>
                            <td class="">
                                <select name="api" id="api">
									<option value="smsgateway" selected="selected">SMS Gateway</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_apidomain') . '</td>
                            <td class=""><input class="form-control" type="text" name="params[apidomain]" size="40" value="' . $apiparams->apidomain . '"></td>
						</tr>  
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_senderid') . '</td>
                            <td class=""><input class="form-control" type="text" name="params[senderid]" size="40" value="' . $apiparams->senderid . '"></td>
                        </tr>
                        ' . $classersfields . '
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_iscustomerclub') . '</td>
                            <td class=""><input type="checkbox" id="iscustomerclub" name="params[iscustomerclub]" '.$iscustomerclubchecked.'  ><label for="iscustomerclub">'.Lang::trans('smsir_iscustomerclubdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_signature') . '</td>
                            <td class=""><input class="form-control" type="text" name="params[signature]" size="40" value="' . $apiparams->signature . '"> ' . Lang::trans('smsir_eg') . ' :  www.sms.ir</td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="50%">
								<a target="_blank" href="configcustomfields.php">' . Lang::trans('smsir_gsmnumberfield') . '</a>
							</td>
                            <td class="">
                                <select name="gsmnumberfield">
                                    ' . $gsmnumber . '
                                </select>
                            </td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_panelcredit') . '</td>
                            <td class="">'.$getBalance.' '.Lang::trans('smsir_credit').'</td>
                        </tr>
                    </tbody>
                </table>
				<table dir="rtl" class="form" width="33%" border="0" cellspacing="2" cellpadding="3" style="height: 550px;float: left;">
                    <tbody>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareadomainwithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareadomainwithverification" name="params[clientareadomainwithverification]" '.$clientareadomainwithverificationchecked.' ><label for="clientareadomainwithverification">'.Lang::trans('smsir_clientareadomainwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareainvoicewithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareainvoicewithverification" name="params[clientareainvoicewithverification]" '.$clientareainvoicewithverificationchecked.' ><label for="clientareainvoicewithverification">'.Lang::trans('smsir_clientareainvoicewithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareaaddfundwithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareaaddfundwithverification" name="params[clientareaaddfundwithverification]" '.$clientareaaddfundwithverificationchecked.' ><label for="clientareaaddfundwithverification">'.Lang::trans('smsir_clientareaaddfundwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_showinsummary') . '</td>
                            <td class=""><input type="checkbox" name="params[showinsummary]" '.$showinsummarychecked.' ><br>'.Lang::trans('smsir_showinsummarydesc').'</td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_showinsidebar') . '</td>
                            <td class=""><input type="checkbox" name="params[showinsidebar]" '.$showinsidebarchecked.' ><br>'.Lang::trans('smsir_showinsidebardesc').'</td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="50%">
								' . Lang::trans('smsir_clientgroups') . '
							</td>
                            <td class="">
                                <select name="params[clientgroups]">
									<option value=""></option>
                                    ' . $clientgroups . '
                                </select>
                            </td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_validateday') . '</td>
                            <td class=""><input class="form-control" type="text" name="params[validateday]" size="40" value="' . $validateday . '"></td>
                        </tr>
                    </tbody>
                </table>
                <table dir="rtl" class="form" width="33%" border="0" cellspacing="2" cellpadding="3" style="height: 550px;">
                    <tbody>
						<tr>
                            <td class="fieldlabel" width="50%"><h2>' . Lang::trans('smsir_verifications') . '</h2></td>
                            <td class="">' . Lang::trans('smsir_verificationsdesc') . '</td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_registerwithverification') . '</td>
                            <td class=""><input type="checkbox" id="registerwithverification" name="params[registerwithverification]" '.$registerwithverificationchecked.' ><label for="registerwithverification">'.Lang::trans('smsir_registerwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_loginwithverification') . '</td>
                            <td class=""><input type="checkbox" id="loginwithverification" name="params[loginwithverification]" '.$loginwithverificationchecked.' ><label for="loginwithverification">'.Lang::trans('smsir_loginwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareacartwithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareacartwithverification" name="params[clientareacartwithverification]" '.$clientareacartwithverificationchecked.' ><label for="clientareacartwithverification">'.Lang::trans('smsir_clientareacartwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareaemailwithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareaemailwithverification" name="params[clientareaemailwithverification]" '.$clientareaemailwithverificationchecked.' ><label for="clientareaemailwithverification">'.Lang::trans('smsir_clientareaemailwithverificationdesc').'</label></td>
                        </tr>
						<tr>
                            <td class="fieldlabel" width="50%">' . Lang::trans('smsir_clientareaproductwithverification') . '</td>
                            <td class=""><input type="checkbox" id="clientareaproductwithverification" name="params[clientareaproductwithverification]" '.$clientareaproductwithverificationchecked.' ><label for="clientareaproductwithverification">'.Lang::trans('smsir_clientareaproductwithverificationdesc').'</label></td>
                        </tr>
                    </tbody>
                </table>                
            </div>
            <p align="center"><input class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_save') . '" class="button" /></p>
        </form>
		<div class="clear"></div>
        ';
    } elseif ($tab == "templates") {
        if ($_POST['submit']) {
            $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
            $result = select_query("mod_smsir_templates", "*", $where);
            while ($data = mysql_fetch_array($result)) {
                if ($_POST[$data['id'] . '_active'] == "on") {
                    $tmp_active = 1;
                } else {
                    $tmp_active = 0;
                }
                $update = array(
                    "template" => $_POST[$data['id'] . '_template'],
                    "active" => $tmp_active
                );

                if (isset($_POST[$data['id'] . '_extra'])) {
                    $update['extra'] = trim($_POST[$data['id'] . '_extra']);
                }
                if (isset($_POST[$data['id'] . '_admingsm'])) {
                    $update['admingsm'] = $_POST[$data['id'] . '_admingsm'];
                    $update['admingsm'] = str_replace(" ", "", $update['admingsm']);
                }
                update_query("mod_smsir_templates", $update, "id = " . $data['id']);
            }
        }

        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div style="text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
                <table dir="rtl" class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
                    <tbody>';
        $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
        $result = select_query("mod_smsir_templates", "*", $where);

        while ($data = mysql_fetch_array($result)) {
			if(($data['name'] != 'OverrideOrderNumberGeneration') && ($data['name'] != 'AdminAreaClientSummaryPage') && ($data['name'] != 'ClientAreaPageCart') && ($data['name'] != 'ClientAreaPageViewEmail') && ($data['name'] != 'ClientAreaPageEmails') && ($data['name'] != 'ClientAreaProductDetails') && ($data['name'] != 'ClientAreaPageDomainContacts') && ($data['name'] != 'ClientAreaPageDomainDNSManagement') && ($data['name'] != 'ClientAreaPageDomainDetails') && ($data['name'] != 'ClientAreaPageDomainEPPCode') && ($data['name'] != 'ClientAreaPageDomainEmailForwarding') && ($data['name'] != 'ClientAreaPageDomainRegisterNameservers') && ($data['name'] != 'ClientAreaPageDomainAddons') && ($data['name'] != 'ClientAreaPrimarySidebar') && ($data['name'] != 'ClientAreaPageViewInvoice') && ($data['name'] != 'ClientAreaPageMassPay') && ($data['name'] != 'ClientAreaPageAddFunds')){
				if ($data['active'] == 1) {
					$active = 'checked = "checked"';
				} else {
					$active = '';
				}
				$desc = json_decode($data['description']);
				$smsir_lang = Lang::trans('smsir_lang');
				if (isset($desc->$smsir_lang)) {
					$name = $desc->$smsir_lang;
				} else {
					$name = $data['name'];
				}
				echo '
					<tr>
						<td class="fieldlabel" width="30%">' . $name . '</td>
						<td class="">
							<textarea class="form-control" cols="50" name="' . $data['id'] . '_template">' . $data['template'] . '</textarea>
						</td>
					</tr>';
				echo '
				<tr>
					<td class="fieldlabel" width="30%" style="float:right;">' . Lang::trans('smsir_active') . '</td>
					<td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>
				</tr>
				';
				echo '
				<tr>
					<td class="fieldlabel" width="30%" style="float:right;">' . Lang::trans('smsir_parameter') . '</td>
					<td>' . $data['variables'] . '</td>
				</tr>
				';

				if (!empty($data['extra'])) {
					echo '
					<tr>
						<td class="fieldlabel" width="30%">' . Lang::trans('smsir_ekstra') . '</td>
						<td class="">
							<input class="form-control" type="text" name="' . $data['id'] . '_extra" value="' . $data['extra'] . '">
						</td>
					</tr>
					';
				}
				if ($_GET['type'] == "admin") {
					echo '
					<tr>
						<td class="fieldlabel" width="30%">' . Lang::trans('smsir_admingsm') . '</td>
						<td class="">
							<input class="form-control" type="text" name="' . $data['id'] . '_admingsm" value="' . $data['admingsm'] . '">
							' . Lang::trans('smsir_admingsmornek') . '
						</td>
					</tr>
					';
				}
				echo '<tr>
					<td colspan="2"><hr></td>
				</tr>';
			}
        }
        echo '
        </tbody>
                </table>
            </div>
            <p align="center"><input class="btn btn-primary" type="submit" name="submit" value="' . Lang::trans('smsir_save') . '" class="button" /></p>
        </form>';

    } elseif ($tab == "messages") {
        if (!empty($_GET['deletesms'])) {
            $smsid = (int)$_GET['deletesms'];
			$delete = Capsule::table('mod_smsir_messages')->where('id', '=', $smsid)->delete();
        }
        echo '
        <!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" type="text/css">
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" type="text/css">
        <script type="text/javascript">
            $(document).ready(function(){
                $(".datatable").dataTable();
            });
        </script>-->

        <div style="text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
        <table dir="rtl" class="datatable" border="0" cellspacing="1" cellpadding="3" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>' . Lang::trans('smsir_gsmnumber') . '</th>
                <th>' . Lang::trans('smsir_message') . '</th>
                <th>' . Lang::trans('smsir_datetime') . '</th>
                <th>' . Lang::trans('smsir_status') . '</th>
                <th width="20"></th>
            </tr>
        </thead>
        <tbody>
        ';

        // Getting pagination values.
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = (isset($_GET['limit']) && $_GET['limit'] <= 50) ? (int)$_GET['limit'] : 10;
        $start = ($page > 1) ? ($page * $limit) - $limit : 0;
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
		
        /* Getting messages order by date desc */
        // $sql = "SELECT * FROM `mod_smsir_messages` ORDER BY `datetime` {$order} limit {$start},{$limit}";
        // $result = mysql_query($sql);
		$result = Capsule::table('mod_smsir_messages')->orderBy('datetime', $order)->skip($start)->take($limit)->get();
		
		if($page && $limit){
			$i = ($page-1) * $limit;
		} else {
			$i = 0;
		}
		
        //Getting total records
		$count = Capsule::table('mod_smsir_messages')->count();

        //Page calculation
        $sayfa = ceil($count / $limit);

        foreach($result as $data) {
            $status = $data->status;

            $i++;
            echo '<tr>
            <td>' . $i . '</td>
            <td>' . $data->to . '</td>
            <td>' . $data->text . '</td>
            <td>' . $data->datetime . '</td>
            <td>' . Lang::trans('smsir_'.$status) . '</td>
            <td><a class="btn btn-primary confirmation" href="addonmodules.php?module=smsir&tab=messages&deletesms=' . $data->id . '" title="' . Lang::trans('smsir_delete') . '">' . Lang::trans('smsir_delete') . '</a></td></tr>';
        }
        /* Getting messages order by date desc */

        echo '
        </tbody>
        </table>
        <script type="text/javascript">
			$(".confirmation").on("click", function () {
				return confirm("'.Lang::trans('smsir_areyousure').'");
			});			
        </script>

        ';
        $list = "";
        for ($a = 1; $a <= $sayfa; $a++) {
            $selected = ($page == $a) ? 'selected="selected"' : '';
            $list .= "<option value='addonmodules.php?module=smsir&tab=messages&page={$a}&limit={$limit}&order={$order}' {$selected}>{$a}</option>";
        }
        echo "<select  onchange=\"this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);\">{$list}</select></div>";

    } elseif ($tab == "sendbulk") {

		if($_POST['numbers']){
			$numsarray = explode(",", $_POST['numbers']);
			$class->setGsmnumber(json_encode($numsarray));
			$class->setMessage($_POST['message']);
			$result = $class->send($numsarray);

			if ($result == false) {
				echo $class->getErrors();
			} else {
				echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_message')."</span></strong><br>".Lang::trans('smsir_smssent')." ".json_encode($numsarray)."<br></div>";
			}
		} elseif($_POST['messageforcustomerclub']){
			$result = $class->sendtocustomerclub($_POST['messageforcustomerclub']);

			if ($result == false) {
				echo $class->getErrors();
			} else {
				echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_message')."</span></strong><br>".Lang::trans('smsir_smssenttocustomerclub')."<br></div>";
			}
		} elseif($_POST['messageforclients']){
			$result = $class->sendtoclients($_POST['messageforclients']);

			if ($result == false) {
				echo $class->getErrors();
			} else {
				echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_message')."</span></strong><br>".Lang::trans('smsir_smssenttoalients')."<br></div>";
			}
		}
		
        echo '<table dir="rtl" class="form" width="100%" border="0">
			<tr>
				<td width="30%">
					<div style="height: 250px;direction: rtl;text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
						<table class="form" border="0">
							<form action="" method="post">
								<input type="hidden" name="action" value="save" />
								<tbody>
									<tr>
										<td class="fieldlabel" width="30%">' . Lang::trans('smsir_number') . '</td>
										<td class="fieldarea">
											<input class="form-control" id="textbox" name="numbers" type="text" style="padding:5px"><br>
											<span style="text-align: right; float: right;">' . Lang::trans('smsir_sendbulkdesc') . '</span>
										</td>
									</tr>
									<tr>
										<td class="fieldlabel" width="30%">' . Lang::trans('smsir_message') . '</td>
										<td class="fieldarea">
										   <textarea class="form-control" required name="message" style="padding:5px"></textarea>
										</td>
									</tr>
								</tbody>
						</table>
								<p align="center"><input class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_send') . '" class="button" /></p>
							</form>
					</div>
				</td>
				<td width="30%">
					<div style="height: 250px;text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
						<table class="form" border="0">
							<form action="" method="post">
								<tbody>
									<tr>
										<td class="fieldlabel" width="30%">' . Lang::trans('smsir_message') . '</td>
										<td class="fieldarea">
										   <textarea class="form-control" required name="messageforclients" style="padding:5px"></textarea>
										</td>
									</tr>
								</tbody>
						</table>
								<p align="center"><input class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_sendtoallclients') . '" class="button" /></p>
							</form>
					</div>
				</td>
				<td width="30%">
					<div style="height: 250px;text-align: right;background-color: whiteSmoke;margin: 0px;padding: 10px;">
						<table class="form" border="0">
							<form action="" method="post">
								<tbody>
									<tr>
										<td class="fieldlabel" width="30%">' . Lang::trans('smsir_message') . '</td>
										<td class="fieldarea">
										   <textarea class="form-control" required name="messageforcustomerclub" style="padding:5px"></textarea>
										</td>
									</tr>
								</tbody>
						</table>
								<p align="center"><input class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_sendtoallcustomerclub') . '" class="button" /></p>
							</form>
					</div>
				</td>
			</tr>
		</table>';
    }
	elseif ($tab == "verifications") {
		date_default_timezone_set('Asia/Tehran');
		$nowtime = time();

        if (!empty($_GET['deletesms'])) {
            $smsid = (int)$_GET['deletesms'];
			$delete = Capsule::table('mod_smsir_verifications')->where('id', '=', $smsid)->delete();
        }
		
		if((!empty($_GET['verify_id'])) && (!empty($_GET['verify_status']))){
			$verify_id = (int)$_GET['verify_id'];
			$verify_status = $_GET['verify_status'];
						
			if($verify_status == 'active'){
				$update_pending = Capsule::table('mod_smsir_verifications')->where('id', $verify_id)->update(['status' => 'active','add_time' => $nowtime]);
				if($update_pending){
					echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_accountactivated')."<br></div>";
				} else {
					echo "<div class='errorbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_accountactivationerror')."<br></div>";
				}
			}
			if($verify_status == 'pending'){
				$update_pending = Capsule::table('mod_smsir_verifications')->where('id', $verify_id)->update(['status' => 'pending','add_time' => $nowtime]);
				if($update_pending){
					echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_accountpended')."<br></div>";
				} else {
					echo "<div class='errorbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_accountpendingerror')."<br></div>";
				}
			}
		}
		
        if (($_POST['verify_resend']) && ($_POST['verify_ide'])) {
            $verify_resend = $_POST['verify_resend'];
            $verify_ide = $_POST['verify_ide'];
			if($verify_resend == 'ok'){
				$row_mob = Capsule::table('mod_smsir_verifications')->where('id', '=', $verify_ide)->get();
				$row_mobi = $row_mob[0];
				$user_mobile = $row_mobi->mobile;
				$user_id = $row_mobi->user_id;

				$random_number = mt_rand(100000, 999999);
				
				$class->setGsmnumber(json_encode($user_mobile));
				$class->setUserid($user_id);
				$sendverification = $class->sendverification($user_mobile,$random_number);
				if($sendverification == true){
					$update_resend = Capsule::table('mod_smsir_verifications')->where('id', $verify_ide)->update(['status' => 'pending','add_time' => $nowtime,'code' => $random_number]);
					if($update_resend){
						echo "<div class='successbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_verifyresendsuccessfully')."<br></div>";
					} else {
						echo "<div class='errorbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_verifyresenderrorsaving')."<br></div>";
					}
				} else {
					echo "<div class='errorbox'><strong><span class='title'>".Lang::trans('smsir_verifications')."</span></strong><br>".Lang::trans('smsir_verifyresenderror')."<br></div>";
				}
			}
        }
		
		$search_cond = "";
		if($_POST['search_submit'] == "ok"){
			if(($_POST['search_email']) || ($_POST['search_mobile']) || ($_POST['search_code'])){
				$search_cond = " WHERE ";
				if(($_POST['search_email']) && ($_POST['search_mobile']) && ($_POST['search_code'])){
					$search_cond .= " 
						`b`.`email` = '".$_POST['search_email']."' AND 
						`a`.`mobile` LIKE '%".$_POST['search_mobile']."%' AND 
						`a`.`code` = '".$_POST['search_code']."'
					";
				} else {
					if(($_POST['search_email']) && ($_POST['search_mobile'])){
						$search_cond .= " 
							`b`.`email` = '".$_POST['search_email']."' AND 
							`a`.`mobile` LIKE '%".$_POST['search_mobile']."%'
						";
					} elseif(($_POST['search_email']) && ($_POST['search_code'])){
						$search_cond .= " 
							`b`.`email` = '".$_POST['search_email']."' AND 
							`a`.`code` = '".$_POST['search_code']."'
						";
					} elseif(($_POST['search_mobile']) && ($_POST['search_code'])){
						$search_cond .= " 
							`a`.`mobile` LIKE '%".$_POST['search_mobile']."%' AND 
							`a`.`code` = '".$_POST['search_code']."'
						";
					} else {
						if($_POST['search_email']){
							$search_cond .= " `b`.`email` = '".$_POST['search_email']."' ";
						}
						if($_POST['search_mobile']){
							$search_cond .= " `a`.`mobile` LIKE '%".$_POST['search_mobile']."%' ";
						}
						if($_POST['search_code']){
							$search_cond .= " `a`.`code` = '".$_POST['search_code']."' ";
						}
					}
				}
			}
		}

        echo '
        <!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" type="text/css">
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" type="text/css">
        <script type="text/javascript">
            $(document).ready(function(){
                $(".datatable").dataTable();
            });
        </script>-->

        <div style="text-align: center;background-color: whiteSmoke;margin: 0px;padding: 10px;">
			<table dir="rtl" class="datatable" border="0" cellspacing="1" cellpadding="3" width="100%">
				<thead>
					<tr>
						<form action="" method="post">
							<td>' . Lang::trans('smsir_searchon') . ' :</td>
							<td>' . Lang::trans('smsir_useremail') . ' : <input name="search_email" type="text" value="'.$_POST['search_email'].'" /></th>
							<td>' . Lang::trans('smsir_gsmnumber') . ' : <input name="search_mobile" type="text" value="'.$_POST['search_mobile'].'" /></th>
							<td>' . Lang::trans('smsir_verificationcode') . ' : <input name="search_code" type="text" value="'.$_POST['search_code'].'" /></th>
							<td><input name="search_submit" class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_search') . '" /></th>
							<input type="hidden" name="search_submit" value="ok" />
						</form>
					</tr>
				</thead>
			</table>
        <table dir="rtl" class="datatable" border="0" cellspacing="1" cellpadding="3" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>' . Lang::trans('smsir_useremail') . '</th>
                <th>' . Lang::trans('smsir_gsmnumber') . '</th>
                <th>' . Lang::trans('smsir_verificationcode') . '</th>
                <th>' . Lang::trans('smsir_datetime') . '</th>
                <th>' . Lang::trans('smsir_status') . '</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        ';
		
        // Getting pagination values.
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = (isset($_GET['limit']) && $_GET['limit'] <= 50) ? (int)$_GET['limit'] : 10;
        $start = ($page > 1) ? ($page * $limit) - $limit : 0;
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        /* Getting messages order by date desc */
        $sql = "SELECT `a`.`id`,`a`.`user_id` ,`a`.`mobile`,`a`.`code`,`a`.`add_time`,`a`.`status`,`b`.`email`
		FROM `mod_smsir_verifications` AS `a`
		JOIN `tblclients` AS `b` ON `b`.`id` = `a`.`user_id`
		".$search_cond."
		ORDER BY `add_time` {$order} 
		limit {$start},{$limit}";
        $result = mysql_query($sql);
		
		if($page && $limit){
			$i = ($page-1) * $limit;
		} else {
			$i = 0;
		}

		$count = Capsule::table('mod_smsir_verifications')->count();

        //Page calculation
        $sayfa = ceil($count / $limit);
		
        while ($data = mysql_fetch_array($result)) {
            $status = $data['status'];

            $i++;
            echo '<tr>
            <td>' . $i . '</td>
            <td><a href="clientssummary.php?userid='.$data['user_id'].'" target="_blank">' . $data['email'] . '</a></td>
            <td>' . $data['mobile'] . '</td>
            <td>' . $data['code'] . '</td>
            <td>' . date("Y/m/d-H:i:s",$data['add_time']) . '</td>
            <td>';
			echo '<form action="" method="post">';
			
			$types = array("active","pending");
			$verify_status_ops = "";
			foreach($types as $typeskey => $typesval){
				$typeselected = ($typesval == $status) ? 'selected="selected"' : '';
				$verify_status_ops .= '<option value="addonmodules.php?module=smsir&tab=verifications&verify_id='.$data['id'].'&verify_status='.$typesval.'" '.$typeselected.'>'.Lang::trans('smsir_'.$typesval).'</option>';
			}
			echo '<select name="verify_status" id="verify_status" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">'.$verify_status_ops.'</select>
			</form>';
			$verify_status_ops = "";

			echo '</td>
            <td>
			<form action="" method="post">
				<input class="btn btn-primary" type="submit" value="' . Lang::trans('smsir_resend') . '" class="button" />
				<input type="hidden" name="verify_resend" value="ok" />
				<input type="hidden" name="verify_ide" value="'.$data['id'].'" />
			</form>
			</td>
			<td>
				<a class="btn btn-primary confirmation" href="addonmodules.php?module=smsir&tab=verifications&deletesms=' . $data['id'] . '" title="' . Lang::trans('smsir_delete') . '">' . Lang::trans('smsir_delete') . '</a>
			</td>
			</tr>';
        }
        /* Getting messages order by date desc */

        echo '
        </tbody>
        </table>
        <script type="text/javascript">
			$(".confirmation").on("click", function () {
				return confirm("'.Lang::trans('smsir_areyousure').'");
			});
        </script>

        ';
        $list = "";
        for ($a = 1; $a <= $sayfa; $a++) {
            $selected = ($page == $a) ? 'selected="selected"' : '';
            $list .= "<option value='addonmodules.php?module=smsir&tab=verifications&page={$a}&limit={$limit}&order={$order}' {$selected}>{$a}</option>";
        }
        echo "<select  onchange=\"this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);\">{$list}</select>
		".Lang::trans('smsir_total'). " : " .$count."</div>";
    }
    echo "<div style='text-align:center;'>".Lang::trans('smsir_lisans')."</div>";
}

	