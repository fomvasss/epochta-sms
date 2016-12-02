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


    public function __construct()
    {
        $this->sms = new APISMS();
        $this->sms_lifetime = Config::get('epochta_sms.sms_lifetime');
        $this->currency = Config::get('epochta_sms.currency');
    }

    private function checkKey()
    {
        if (empty(Config::get('epochta_sms.private_key')) || empty(Config::get('epochta_sms.private_key'))) {
            Log::error('Error SMS: Not set private or public key');
            return 0;
        }
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

        $res = $this->sms->sendSMS($sender, $text, $phone, null, $this->sms_lifetime);

        if (empty($res['result']['id'])) {
            Log::error('Error sending SMS: ' . $phone . isset($res['code']) ? $res['code'] : null);
            return 0;
        }

        Log::info('Sended SMS: ' . $phone . '. ID :' . $res['result']['id']);
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

        if (isset($res["result"]["balance_currency"])) {
            return $res["result"]["balance_currency"];
        }
        elseif (isset($res["result"]["error"])) {
            Log::error("Error SMS: ".$res["result"]["code"]);
            return 0;
        }
        else {
            return 0;
        }
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

        $result = $this->sms->getCampaignDeliveryStats($id);
        if (empty($result['result']['status'])) {
            return 'Ошибка соеденения с сервером';
        };

        switch ($result['result']['status'][0]) {
            case '0': return "В очереди отправки";
            case 'SENT': return "Отправлено";
            case 'DELIVERED': return "Доставлено";
            case 'NOT_DELIVERED': return "Не доставлено";
            case 'INVALID_PHONE_NUMBER': return "Неверный номер";
            case 'SPAM': return "Отрправлено в спам";
        }
    }



}