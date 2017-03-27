# Epochta-sms
Package for sending SMS in Laravel ~5.3 (use service https://www.epochta.ru)

# Install
```
composer require "fomvasss/epochta-sms"
```
register the service provider and aliases in config/app.php:
```
  Fomvasss\Epochta\EpochtaServiceProvider::class,
  ...
  'Epochta' => Fomvasss\Epochta\Services\Epochta::class,
```
Then publish assets with 
```
php artisan vendor:publish
```
This will add the file config/epchta_sms.php
# Config  
Edit file config/epchta_sms.php, set:
- private_key
- public_key
- test_mode
- sms_lifetime
- currency
  
# Using
##Using facades Epochta
```
use Fomvasss\Epochta\Services\Facade\Epochta;
```
Now, you can use next: 
```
Epochta::sendSms($sender, $text, $phone) //return ID SMS
```
```
Epochta::getUserBalance()   //return float
```
```
Epochta::getSmsStatus($idSms);  //return string status SMS
```
Function getSmsStatus() return next statuses:
- Ошибка сервера
- В очереди отправки
- Отправлено
- Доставлено
- Не доставлено
- Неверный номер
- Отрправлено в спам

##Using objects APISMS, Addressbook and Account (original API)
```
use Fomvasss\Epochta\Libraries\APISMS;
use Fomvasss\Epochta\Libraries\Account;
use Fomvasss\Epochta\Libraries\Addressbook;
use Fomvasss\Epochta\Libraries\Exceptions;
```  
```
$sms=new APISMS();
$addressbook=new Addressbook($Gateway);
$account=new Account($Gateway);
$account=new Exceptions($Gateway);
```
Example sended SMS
```
$res = $sms->sendSMS('SenderName', 'your sms text :)', '+380969416666', null, 0);
$res['result']['id']; // ID SMS
```

Other method and API see this doc:
- https://www.epochta.com.ua/products/sms/v3.php 
- https://www.epochta.ru/products/sms/php-30-example.php