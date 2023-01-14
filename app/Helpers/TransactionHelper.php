<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class TransactionHelper
{
 protected $amount;
 protected $ttype;

 public function __construct($amount,$ttype)
 {
    $this->amount = $amount;
    $this->ttype = $ttype;
 }

    public function getAmount()
    {
        return $this->amount;
 }

    public function getTtype()
    {
        return $this->ttype;
 }

    public function sendMoney($uid,$ruid,$newSenderBalance,$newReceiverBalance)
    {
        try {
           DB::beginTransaction();
           DB::table('transactions')->insert([
              'uid' => $uid ,
              'ruid' => $ruid,
              'amount' => $this->getAmount(),
              'ttype' => $this->getTtype()
           ]);

           DB::table('users')->where('id',$uid)->update(['balance'=>$newSenderBalance]);
           DB::table('users')->where('id',$ruid)->update(['balance'=>$newReceiverBalance]);
           DB::commit();
           return true;
        }catch (\Exception $e) {
         DB::rollBack();
         info($e->getMessage());
         return 'An Error Occurred';
        }
 }

    public function withdrawCash($uid,$aid,$newBalance)
    {
        try {
            DB::beginTransaction();
            DB::table('transactions')->insert([
                'uid' => $uid ,
                'aid' => $aid,
                'amount' => $this->getAmount(),
                'ttype' => $this->getTtype()
            ]);
            DB::table('users')->where('id',$uid)->update(['balance'=>$newBalance]);
            DB::commit();
            return true;
        }catch (\Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return 'An Error Occurred';
        }
    }
}
