<?php

namespace App\Helpers;

use AfricasTalking\SDK\AfricasTalking;

class SmsHelper
{
protected $phone;
protected $AT;

public function __construct($phone)
{
   $this->phone = $phone;
   $this->AT = new AfricasTalking(Util::$AT_USERNAME,Util::$AT_KEY);
}

    public function getPhone()
    {
        return $this->phone;
    }

    public function sendSms($message)
    {
        //get service
        $sms = $this->AT->sms();
      return  $sms->send([
           'to' => $this->getPhone(),
           'message' => $message,
           'from' => Util::$AT_COMPANY,
        ]);


    }

}
