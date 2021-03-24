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

# ارسال پیامک به کاربران در سیستم مدیریت هاستینگ WHMCS

 یک افزونه کاربردی برای سیستم مدیریت هاستینگ WHMCS می باشد که شما را قادر می سازد تا براحتی از طریق پیامک در تمامی بخش های ممکن سیستم اقدام به اطلاع رسانی برای کاربرانتان کنید.


> [نصب](https://github.com/pejmankheyri/SMSIR-WHMCS#%D9%86%D8%B5%D8%A8)
> 
> [امکانات](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%A7%D9%85%DA%A9%D8%A7%D9%86%D8%A7%D8%AA)
> 
> [تنظیمات](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D9%86%D8%B8%DB%8C%D9%85%D8%A7%D8%AA)
> 
> [کمک به توسعه](https://github.com/pejmankheyri/SMSIR-WHMCS#%DA%A9%D9%85%DA%A9-%D8%A8%D9%87-%D8%AA%D9%88%D8%B3%D8%B9%D9%87)
> 
> [لایسنس](https://github.com/pejmankheyri/SMSIR-WHMCS#%D9%84%D8%A7%DB%8C%D8%B3%D9%86%D8%B3)
> 
> [تصاویر افزونه](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D8%B5%D8%A7%D9%88%DB%8C%D8%B1-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87)

## نصب

* پوشه `SMSIR-WHMCS` را در مسیر اصلی سیستم WHMCS خود کپی کنید.
* افزونه را از طریق منوی `افزونه ها` فعال کنید.
* [تنظیمات افزونه را در بخش `SMSir` انجام دهید.](https://github.com/pejmankheyri/SMSIR-WHMCS#%D8%AA%D9%86%D8%B8%DB%8C%D9%85%D8%A7%D8%AA)


## امکانات

* ارسال پیامک غیرسیستمی به شماره های خاص، به همه کاربران سیستم و به همه مخاطبین باشگاه مشتریان
* نمایش لیست پیامک های ارسال شده در سیستم whmcs
* نمایش لیست پیامک های ارسالی برای فعالسازی کاربران سیستم با قابلیت جستجو و مدیریت لیست
* افزودن امضا در انتهای تمام پیامک های ارسالی
* تنظیمات برای فعالسازی کاربر در هنگام ثبت سفارش
* تنظیمات برای فعالسازی کاربر در هنگام ورود به سیستم
* تنظیمات برای فعالسازی جهت دسترسی به صفحه کاربری سبد خرید
* تنظیمات برای فعالسازی جهت دسترسی به صفحه کاربری ایمیل
* تنظیمات برای فعالسازی جهت دسترسی به صفحه کاربری محصولات
* تنظیمات برای فعالسازی جهت دسترسی به صفحات کاربری مربوط به دامین
* تنظیمات برای فعالسازی جهت دسترسی به صفحه کاربری فاکتور
* تنظیمات برای فعالسازی جهت دسترسی به صفحه کاربری افزایش موجودی
* تنظیمات برای نمایش وضعیت کاربر در صفحه خلاصه اطلاعات کاربر
* تنظیمات برای نمایش وضعیت کاربر در نوار کناری صفحه کاربری
* انتخاب گروه کاربری استثنا برای فعالسازی کاربری (مثلا نماینده ها)
* تنظیم تعداد روز برای اعتبارسنجی مجدد حساب کاربران فعال شده
* تنظیمات برای انواع قالب های ارسال پیامک به کاربر در سیستم
* تنظیمات برای انواع قالب های ارسال پیامک به مدیران در سیستم

## تنظیمات

* تنظیمات اصلی افزونه شامل جدول زیر می باشد:

    | تنظیمات | توضیح |
    | ------ | ------ |
    | لینک وب سرویس | لینک وب سرویسی که پنل ارسال پیامک از آن استفاده می کند (https://ws.sms.ir/) |
    | کلید وب سرویس | کلید وب سرویس پنل پیامک sms.ir |
    | کد امنیتی | کد امنیتی پنل پیامک sms.ir |
    | شماره ارسال کننده | شماره ارسال کننده پنل پیامک sms.ir |
    | ارسال از طریق باشگاه مشتریان | باید ماژول باشگاه مشتریان خریداری شود و سپس ارسال از آن طریق انجام می شود |
    | امضا (در انتهای هر پیامک ارسالی) | درصورت فعال بودن در انتهای همه پیام ها امضای شما ارسال می شود |
    | فیلد شماره موبایل | پس از ایجاد فیلد دلخواه موبایل از این قسمت باید انتخاب شود |
    | وضعیت کاربر در صفحه اطلاعات کاربر | درصورت فعال بودن وضعیت کاربر در صفحه خلاصه اطلاعات کاربر نمایش داده می شود |
    | وضعیت کاربر در نوار کناری صفحه کاربری | درصورت فعال بودن وضعیت کاربر در نوار کناری صفحه کاربری نمایش داده می شود |
    | گروه کاربری استثنا | می توانید با انتخاب گروه کاربری خاص آنها را از فعالسازی استثنا قرار دهید |
    | اعتبارسنجی مجدد | تعیید تعداد روزهایی که می خواهید دوباره اعتبارسنجی کاربران انجام شود |

## کمک به توسعه

از Pull request ها استقبال می کنیم.

برای تغییرات عمده ، لطفاً ابتدا یک issue را باز کنید تا در مورد آنچه می خواهید تغییر دهیم و بحث کنیم.

## لایسنس

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fpejmankheyri%2FSMSIR-WHMCS?ref=badge_large)

</div>

<div dir="rtl">

## تصاویر افزونه

* <a href="https://user-images.githubusercontent.com/3329008/112186419-bd6ee100-8c1e-11eb-8b10-688160f87088.png" target="_blank">تصویر 01</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186518-d5466500-8c1e-11eb-98d2-0b74280fec2e.png" target="_blank">تصویر 02</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186568-e68f7180-8c1e-11eb-8103-e9f113ab37c9.png" target="_blank">تصویر 03</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186625-f4dd8d80-8c1e-11eb-9332-5fbfa17faa3e.png" target="_blank">تصویر 04</a>
* <a href="https://user-images.githubusercontent.com/3329008/112186673-00c94f80-8c1f-11eb-9721-ffaf57f7449e.png" target="_blank">تصویر 05</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276169-0fa31700-8c9e-11eb-9768-35b9b2a6cdac.png" target="_blank">تصویر 06</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276193-19c51580-8c9e-11eb-8b53-7481cebaae8d.png" target="_blank">تصویر 07</a>
* <a href="https://user-images.githubusercontent.com/3329008/112276238-25184100-8c9e-11eb-9594-1b05e9a698a2.png" target="_blank">تصویر 08</a>

</div>