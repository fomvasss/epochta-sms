<?php

namespace Fomvasss\Epochta\Services;

use Fomvasss\Epochta\Libraries\APISMS;
use Fomvasss\Epochta\Libraries\Account;

use Fomvasss\Epochta\Libraries\Addressbook;
use Fomvasss\Epochta\Libraries\Exceptions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;



class Epochta
{

    private $sms;
    private $sms_lifetime;
    private $currency;

    /**
     * Epochta constructor.
     * Set configuration
     */
    public function __construct()
    {
        $this->sms = new APISMS();
        $this->sms_lifetime = Config::get('epochta_sms.sms_lifetime');
        $this->currency = Config::get('epochta_sms.currency');
    }


    /**
     * Quick send sms. No list using, just 1 phone
     *
     * @param  int  $sender, stirng $text, string $phone
     * @return int ID SMS or 0 if error sending
     */
    public function sendSms($sender, $text, $phone)
    {
        $this->checkKey();

        $phone = $this->clearNumber($phone);

        $res = $this->sms->sendSMS($sender, $text, $phone, null, $this->sms_lifetime);

        if (isset($res['error']) || empty($res['result']['id'])) {
            Log::error('Epochta. Phone number: ' .$phone. '. ' . $res['error']);
            return 0;
        }

        //Log::info('Epochta. OK. Phone number: ' . $phone . '. ID SMS:' . $res['result']['id']);
        return $res['result']['id'];
    }

    /**
     * Get balance.
     *
     * @return float sum or 0 if error
     */
    public function getUserBalance()
    {
        $this->checkKey();

        $account = new Account($this->sms);

        $res=$account->getUserBalance($this->currency);

        if (isset($res['error']) || empty($res['result']['balance_currency'])) {
            Log::error('Epochta. getUserBalance(). '.$res['error']);
            return 0;
        }

        return $res["result"]["balance_currency"];
    }

    /**
     * Get balance.
     *
     * @param  int SMS $id
     * @return string status SMS or error
     */
    public function getSmsStatus($id)
    {

        $this->checkKey();

        //$status = $sms->getCampaignInfo($id);
        if (empty($id)) {
            return 'Ошибка ID SMS';
        }
        $res = $this->sms->getCampaignDeliveryStats($id);
        if (isset($res['error']) || empty($res['result']['status'])) {
            Log::error('Epochta. ID SMS: '. $id .' ' . $res['error']);
            dd($res);
            return 'Ошибка сервера';
        };

        switch ($res['result']['status'][0]) {
            case '0': return "В очереди отправки";
            case 'SENT': return "Отправлено";
            case 'DELIVERED': return "Доставлено";
            case 'NOT_DELIVERED': return "Не доставлено";
            case 'INVALID_PHONE_NUMBER': return "Неверный номер";
            case 'SPAM': return "Отрправлено в спам";
        }
    }


    private function checkKey()
    {
        if (empty(Config::get('epochta_sms.private_key')) || empty(Config::get('epochta_sms.private_key'))) {
            Log::error('Epochta Error: Not set private or public key');
            return 0;
        }
    }

    private function clearNumber($number)
    {
        return str_replace([' ', '(', ')','.','-','+'], '', '+'.$number);
    }

}