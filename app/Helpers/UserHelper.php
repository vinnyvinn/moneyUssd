<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserHelper
{
    protected $name;
    protected $phone;
    protected $balance;
    protected $pin;


    public function __construct($phone)
    {
        $this->phone = $phone;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPhone()
    {
       return $this->phone;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setBalance($balance)
    {
       $this->balance = $balance;
    }

    public function getBalance()
    {

     return $this->balance;
    }

    public function register()
    {
       $user = new User;
       $user->name = $this->getName();
       $user->pin = Hash::make($this->getPin());
       $user->phone = $this->getPhone();
       $user->balance = $this->getBalance();
       $user->save();
    }
    public function isUserRegistered()
    {
        $user = User::where('phone',$this->getPhone())->first();
        if (!$user){
            return false;
        }
        return true;
    }

    public function readName()
    {
        return User::where('phone',$this->getPhone())->first()->name;
    }

    public  function checkBalance()
    {
        return User::where('phone',$this->getPhone())->first()->balance;
    }

    public function readUserId()
    {
        return User::where('phone',$this->getPhone())->first()->id;
    }

    public function correctPin()
    {
        $user = User::where('phone',$this->getPhone())->first();

        if ($user) {
            if (Hash::check($this->pin,$user->pin)) {
                return true;
            }
        }
        return false;
    }
}
