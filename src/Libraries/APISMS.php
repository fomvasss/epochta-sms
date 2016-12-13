<?php

namespace Fomvasss\Epochta\Libraries;

use Illuminate\Support\Facades\Config;

class APISMS
{

    private $privateKey;
    private $publicKey;
    private $formatResponse;
    private $url;
    private $version;
    private $testMode;

    function __construct() {

        $this->privateKey = Config::get('epochta_sms.private_key');
        $this->publicKey = Config::get('epochta_sms.public_key');
        if (Config::get('epochta_sms.https') == true) {
            $this->url = 'https://atompark.com/api/sms/';
        }
        else {
            $this->url = 'http://atompark.com/api/sms/';
        }
        $this->testMode = Config::get('epochta_sms.test_mode');
        $this->version = '3.0';
        $this->formatResponse = 'json';
    }

    public  function execCommad($command,$params,$simple=false){
        $params['key']=$this->publicKey;
        if($this->testMode) $params['test']=true;
        $controlSUM=$this->calcControlSum($params,$command);
        $params['sum']=$controlSUM;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $this->url.$this->version .'/'.$command);
        $result = curl_exec($ch);
        if(curl_errno($ch)>0) return array('success'=> false,  'code'=>curl_errno($ch),'error'=>curl_error($ch));
        elseif ($this->formatResponse=='json') return $this->processResponseJSON($result,$simple);
        elseif ($this->formatResponse=='xml') return $this->processResponseXML($result);
        else return $this->processResponseJSON($result);
    }

    private function processResponseJSON($result,$simple){
        if($simple) return json_decode($result,true);
        elseif ($result) {
            $jsonObj = json_decode($result,true);
            if(null===$jsonObj) {
                return array('success'=> false,  'result'=>NULL);
            }
            elseif(!empty($jsonObj->error)) {
                return array('success'=> false,  'error'=> $jsonObj->error,'code'=>$jsonObj->code);

            } else {
                return $jsonObj;
            }
        } else {
            return array('success'=> false,  'result'=>NULL);
        }
    }

    private function processResponseXML($result,$simple){
        //:TODO processResponseXML
        return NULL;
    }

    private function calcControlSum($params,$action){
        $params['version']=$this->version;
        $params['action']=$action;
        ksort($params);
        $sum='';
        foreach ($params as $k=>$v) $sum.=$v;
        $sum.=$this->privateKey;
        return md5($sum);
    }



    /************************* with original API file Stat.php ***************************/
    /*
**	creating campaign
**	$sender - sender. Up to 14 numbers for numeric senders, up to 11 for alphanumeric
** 	$text - sms text
**	$list_id - id of address book
**	$datetime must be in GMT, PHP format Y-m-d H:i:s
*/
    function createCampaign($sender, $text, $list_id, $datetime, $batch, $batchinterval, $sms_lifetime, $controlnumber){
        return $this->execCommad('createCampaign',array('sender' => $sender, 'text' => $text, 'list_id' => $list_id, 'datetime' => $datetime, 'batch' => $batch, 'batchinterval' => $batchinterval, 'sms_lifetime' => $sms_lifetime, 'controlnumber' => $controlnumber));
    }

    /*
    **	quick send sms. No list using, just 1 phone
    */
    function sendSMS($sender, $text, $phone, $datetime, $sms_lifetime){
        return $this->execCommad('sendSMS',array('sender' => $sender, 'text' => $text, 'phone' => $phone, 'datetime' => $datetime, 'sms_lifetime' => $sms_lifetime));
    }

    /*
    **	this function will return general information about campaign
    */
    function getCampaignInfo ($id) {
        return $this->execCommad('getCampaignInfo',array('id' => $id));
    }

    /*
    **	this function returns complete list of phones of the task, including DLR
    */
    function getCampaignDeliveryStats ($id,$datefrom="") {
        return $this->execCommad('getCampaignDeliveryStats',array('id' => $id, 'datefrom' => $datefrom));
    }

    /*
    **	Cancels campaign. Campaign must be in "Ready for sent" or "Scheduled" state
    */
    function cancelCampaign ($id) {
        return $this->execCommad('cancelCampaign',array('id' => $id));
    }

    /*
    **	Deletes campaign, any status
    */
    function deleteCampaign ($id) {
        return $this->execCommad('deleteCampaign',array('id' => $id));
    }

    /*
    **	gets list of campaigns
    */
    function getCampaignList() {
        return $this->execCommad('getCampaignList',"");
    }

    /*
    **	calculates price of campaign sending
    */
    function checkCampaignPrice ($sender, $text, $list_id) {
        return $this->execCommad("checkCampaignPrice", array('sender' => $sender, 'text' => $text, 'list_id' => $list_id));
    }

}