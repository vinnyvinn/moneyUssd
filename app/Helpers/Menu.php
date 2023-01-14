<?php

namespace App\Helpers;

use App\Models\Agent;
use App\Models\User;
use App\Models\UssdSession;
use Illuminate\Support\Facades\Hash;

class Menu
{
   protected $sessionId;
   protected $text;

    public function __construct(){}

    public function menuRegistered($name)
    {
        $response = "Welcome ".$name." Reply with \n";
        $response .="1. Send Money\n";
        $response .="2. Withdraw\n";
        $response .="3. Check Balance\n";
        return $response;
    }

    public function menuUnregistered()
    {
       $response = "CON Welcome to this app. Reply with\n";
       $response .="1. Register";
       echo $response;
    }

    public function registerMenu($data,$phone)
    {
     $level = count($data);
     if ($level == 1) {
         echo "CON Please enter full name.";
     }elseif($level == 2) {
         echo "CON Please enter your PIN";
     }elseif ($level == 3) {
         echo "CON Please enter pin again.";
     }elseif ($level ==4) {
         $name = $data[1];
         $pin = $data[2];
         $confirm_pin = $data[3];

         if ($pin != $confirm_pin) {
             echo "END You pins do not match.Try again later";
         }else {
             $user = new UserHelper($phone);
             $user->setName($name);
             $user->setPin($pin);
             $user->setBalance(Util::$WALLET_BALANCE);
             $user->register();
             echo "END You have successfully been registered.";
         }

     }

    }

    public function sendMoneyMenu($data,$sender)
    {
      $level = count($data);
      $receiver = null;
      $receiverName = null;
     if ($level == 1){
         echo "CON Enter receiver phone number:";
     }elseif ($level == 2) {
         echo "CON enter amount:";
     }elseif ($level == 3) {
         echo "CON enter your PIN";
     }elseif ($level == 4) {
          $receiver = new UserHelper($data[1]);
          $receiverName = $receiver->readName();

         $response = "Send ".$data[2].' to '.$receiverName.' - '.$data[1]."\n";
         $response .="1. Confirm\n";
         $response .="2. Cancel\n";
         $response .=Util::$GO_BACK." Back\n";
         $response .=Util::$GO_BACK_MAIN." Main menu\n";
         echo "CON ".$response;
     }elseif ($level == 5 && $data[4] == 1) {
         $pin = $data[3];
         $sender->setPin($pin);
         $amount = $data[2];
         $ttype = "send";

         $receiver = new UserHelper($data[1]);
         $newSenderBalance = $sender->checkBalance() - $amount - Util::$TRANSACTION_FEES;
         $newReceiverBalance = $receiver->checkBalance() + $amount;

         if (!$sender->correctPin()) {
             echo "END Wrong PIN was entered.";
         }else {
             $transaction = new TransactionHelper($amount,$ttype);
             $result =  $transaction->sendMoney($sender->readUserId(),$receiver->readUserId(),$newSenderBalance,$newReceiverBalance);
             if ($result==true) {
                 echo "END your request is being processed.Please wait for an sms";
             }else {
                 echo "END an error occurred.Please try again later.";
             }
         }


     }elseif ($level == 5 && $data[4] == 2) {
         echo "END Request cancelled. Thank you for using our app.";
     }elseif ($level == 5 && $data[4] == Util::$GO_BACK){
         echo "CON You have requested to go back one step-pin";
     }elseif ($level == 5 && $data[4] == Util::$GO_BACK_MAIN){
         echo "END Your have requested to go back to the main menu";
     }else{
         echo "END You have entered an invalid choice.";
     }
    }

    public function withdrawMoneyMenu($data,$user)
    {
     $level = count($data);

     if ($level == 1) {
         echo "CON Enter agent Number:";
     }elseif ($level == 2){
         echo "CON Enter amount:";
     }elseif ($level == 3){
         echo "CON Enter PIN";
     }elseif ($level == 4) {
      $agent = new AgentHelper($data[1]);
      $agentName = $agent->readNameByNumber();
      $response = "CON Withdraw Ksh ".$data[2] .' from agent '.$agentName."\n";
      $response .="1. Confirm\n";
      $response .="2. Cancel\n";
      echo $response;
     }elseif ($level == 5 && $data[4] == 1) {
      $user->setPin($data[3]);

      if (!$user->correctPin()) {
          echo "END Wrong Pin.";
      }

      if ($user->checkBalance() <  ($data[2] + Util::$TRANSACTION_FEES)) {
         echo "END Insufficient wallet balance.";
      }

      $ttype = 'withdraw';
      $agent = new AgentHelper($data[1]);
      $newBalance = $user->checkBalance() - $data[2] - Util::$TRANSACTION_FEES;
      $trx = new TransactionHelper($data[2],$ttype);
      $result = $trx->withdrawCash($user->readUserId(),$agent->readIdByNumber(),$newBalance);
      if ($result) {
          echo "END Request processed successfully.";
      }else {
          echo "END ".$result;
      }

     }elseif ($level == 5 && $data[4] == 2) {
         echo "END Thank you for using our app.";
     }else {
         echo "END You have entered an invalid choice.";
     }
    }

    public function checkBalanceMenu($data,$phone)
    {
        $level = count($data);

        if ($level == 1) {
            echo "CON ENTER Your PIN.";
        }
        elseif ($level == 2) {

            //validate pin
            $user = new UserHelper($phone);
            $user->setPin($data[1]);
            $user->setBalance($user->checkBalance());
            if (!$user->correctPin()) {
                echo "END Wrong Pin";
            }

            echo "END your wallet balance is: ".$user->getBalance();
            $sms = "your wallet balance is: ".$user->getBalance();
            $sendSms = new SmsHelper($user->getPhone());
            $result =  $sendSms->sendSms($sms);
        }else {
          echo "END An error occurred. Please try again later.";
        }
    }

    public function middleware($text,$sessionId) {
       return $this->invalidEntry($this->goBack($this->goBackToMain($text)),$sessionId);
    }

    public function goBack($text)
    {
      $exploded_text = explode("*",$text);

      while (array_search(Util::$GO_BACK,$exploded_text) !=false) {
          $firstIndex = array_search(Util::$GO_BACK,$exploded_text);
           array_splice($exploded_text,$firstIndex-1,2);
      }
      return join("*",$exploded_text);
    }

    public function goBackToMain($text)
    {
       $explodedText = explode("*",$text);

       while (array_search(Util::$GO_BACK_MAIN,$explodedText) !=false) {
           $firstIndex = array_search(Util::$GO_BACK_MAIN,$explodedText);
           $explodedText = array_slice($explodedText,$firstIndex+1);
       }
       return join("*",$explodedText);
    }

    public function persistInvalidEntry($sessionId,$user_id,$ussdLevel)
    {
        $session = new UssdSession;
        $session->sessionId = $sessionId;
        $session->ussdLevel = $ussdLevel;
        $session->uid = $user_id;
        $session->save();
    }

    public function invalidEntry($ussdStr,$sessionId)
    {
        $result = UssdSession::where('sessionId',$sessionId)->get();

        if (count($result) == 0){
            return $ussdStr;
        }

        $strArr = explode("*",$ussdStr);

        info($strArr);
        foreach ($result as $res) {
            unset($strArr[$res->ussdLevel]);
        }
        info($strArr);

        $strArr = array_values($strArr);

        return join("*",$strArr);

    }
}
