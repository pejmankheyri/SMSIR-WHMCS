<p align="center">
<img src="https://user-images.githubusercontent.com/3329008/111814382-a31bc700-88ef-11eb-94e2-41dd10c0d2b1.png" /> + 
<img src="https://user-images.githubusercontent.com/3329008/112176051-657fac80-8c15-11eb-87c1-d48fa0942392.png" />
</p>
<p align="center">
  <a href="https://packagist.org/packages/pejmankheyri/smsir-whmcs"><img src="https://poser.pugx.org/pejmankheyri/smsir-whmcs/v/stable" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/pejmankheyri/smsir-whmcs"><img src="https://img.shields.io/packagist/dt/pejmankheyri/smsir-whmcs" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/pejmankheyri/smsir-whmcs"><img src="https://poser.pugx.org/pejmankheyri/smsir-whmcs/d/monthly" alt="Monthly Downloads"></a>
<a href="https://packagist.org/packages/pejmankheyri/smsir-whmcs"><img src="https://img.shields.io/github/license/pejmankheyri/smsir-whmcs" alt="License"></a>
<a href="https://app.fossa.com/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS?ref=badge_shield" alt="FOSSA Status"><img src="https://app.fossa.com/api/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS.svg?type=shield"/></a>
</p>
<div dir="rtl">

# Sending SMS to users in the WHMCS hosting management system

 There is a useful plugin for the WHMCS hosting management system that allows you to easily notify your users via text messages across all possible sections of the system.


> [Installation](https://github.com/pejmankheyri/SMSIR-WHMCS#%D9%86%D8%B5%D8%A8)
> 
> [Features](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%A7%D9%85%DA%A9%D8%A7%D9%86%D8%A7%D8%AA)
> 
> [Settings](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D9%86%D8%B8%DB%8C%D9%85%D8%A7%D8%AA)
> 
> [Development Assistance](https://github.com/pejmankheyri/SMSIR-WHMCS#%DA%A9%D9%85%DA%A9-%D8%A8%D9%87-%D8%AA%D9%88%D8%B3%D8%B9%D9%87)
> 
> [License](https://github.com/pejmankheyri/SMSIR-WHMCS#%D9%84%D8%A7%DB%8C%D8%B3%D9%86%D8%B3)
> 
> [Plugin Images](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D8%B5%D8%A7%D9%88%DB%8C%D8%B1-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87)

## Installation

* Copy the `SMSIR-WHMCS` folder to the main directory of your WHMCS system.
* Activate the plugin through the `Add-ons` menu in WHMCS.
* [Configure the plugin settings in the `SMSir` section.](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D9%86%D8%B8%DB%8C%D9%85%D8%A7%D8%AA)


## Features

- Sending non-systematic SMS to specific numbers, all users of the system, and all Customer Club contacts.
- Displaying a list of sent SMS messages in the WHMCS system.
- Displaying a list of activation SMS messages sent to system users with search and list management capabilities.
- Adding a signature at the end of all sent SMS messages.
- Settings for user activation during order placement.
- Settings for user activation during login.
- Settings for user activation to access the shopping cart user page.
- Settings for user activation to access the email user page.
- Settings for user activation to access the products user page.
- Settings for user activation to access the domain-related user pages.
- Settings for user activation to access the invoice user page.
- Settings for user activation to access the balance increase user page.
- Settings for displaying user status on the user summary page.
- Settings for displaying user status in the user side panel.
- Selecting user group exceptions for user activation (e.g., representatives).
- Setting the number of days for re-validation of activated user accounts.
- Settings for various SMS template types for users in the system.
- Settings for various SMS template types for administrators in the system.

## Settings

* The main settings of the plugin include the following table:

    | Settings | Description |
    | ------ | ------ |
    | Service Web Link | The web link for the service used for sending text messages. (https://ws.sms.ir/) |
    | Service API Key | The API key for the sms.ir text message panel. |
    | Security code | SMS panel security code sms.ir |
    | Sender number | SMS panel sender number sms.ir |
    | Send through customer club | The customer club module must be purchased and then shipping is done through it |
    | Signature (at the end of each text message sent) | If enabled, your signature will be sent at the end of all messages |
    | Mobile number field | After creating the desired mobile field, it must be selected from this section |
    | User status on the user information page | If the user's status is active, it will be displayed on the summary page of the user's information |
    | User status in the sidebar of the user page | If the user status is active, it will be displayed in the sidebar of the user page |
    | Exception user group | You can exclude them from activation by selecting specific user groups |
    | Revalidation | Confirm the number of days you want to revalidate users |

## Development Assistance

We welcome pull requests.

For major changes, please open an issue first so we can discuss what you want to change.
## License

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS?ref=badge_large)

</div>

<div dir="rtl">

## Plugin Images

* <a href="https://user-images.githubusercontent.com/3329008/112186419-bd6ee100-8c1e-11eb-8b10-688160f87088.png" target="_blank">Image 01</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186518-d5466500-8c1e-11eb-98d2-0b74280fec2e.png" target="_blank">Image 02</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186568-e68f7180-8c1e-11eb-8103-e9f113ab37c9.png" target="_blank">Image 03</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186625-f4dd8d80-8c1e-11eb-9332-5fbfa17faa3e.png" target="_blank">Image 04</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186673-00c94f80-8c1f-11eb-9721-ffaf57f7449e.png" target="_blank">Image 05</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276169-0fa31700-8c9e-11eb-9768-35b9b2a6cdac.png" target="_blank">Image 06</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276193-19c51580-8c9e-11eb-8b53-7481cebaae8d.png" target="_blank">Image 07</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276238-25184100-8c9e-11eb-9594-1b05e9a698a2.png" target="_blank">Image 08</a>

</div>
