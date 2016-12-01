# epochta-sms
Package for sending SMS in Laravel 5.3 (use service https://www.epochta.ru)

# Install
composer require "fomvasss/epochta-sms":dev-fomin

register the service provider in config/app.php in the providers array:
  Fomvasss\Epochta\EpochtaServiceProvider::class,
  
  Then publish assets with php artisan vendor:publish. This will add the file config/epchta_sms.php
# Config  
  Edit file config/epchta_sms.php, add:
  - sms_key_private
  - sms_key_public
  - sett test_mode = false
  
# Using
- uses this class:
use Fomvasss\Epochta\Libraries\APISMS;
- and optional this
use Fomvasss\Epochta\Libraries\Account;
use Fomvasss\Epochta\Libraries\Addressbook;
use Fomvasss\Epochta\Libraries\Exceptions;
  

    $sms=new APISMS();
    $addressbook=new Addressbook($Gateway);
    $account=new Account($Gateway);

Example send SMS

    $res = $sms->sendSMS('TestName', 'Тестовий текст для СМС', '+380969416666', Carbon::now(), 12);
    dd($res);
    dd($res['result']['id']);
    
Other method see this doc 
- https://www.epochta.com.ua/products/sms/v3.php 
- https://www.epochta.ru/products/sms/php-30-example.php