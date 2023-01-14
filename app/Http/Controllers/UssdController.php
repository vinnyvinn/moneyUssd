<?php

namespace App\Http\Controllers;

use App\Helpers\Menu;
use App\Helpers\UserHelper;
use App\Helpers\Util;
use App\Models\UssdNotification;
use Illuminate\Http\Request;

class UssdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function initUssd()
    {
        //Read the variables sent via POST from our API
        $sessionId   = $_POST["sessionId"];
        $serviceCode = $_POST["serviceCode"];
        $phoneNumber = $_POST["phoneNumber"];
        $text        = $_POST["text"];

      $menu = new Menu();
      $user = new UserHelper($phoneNumber);
      $text = $menu->middleware($text,$sessionId);

      if ($text=="" && $user->isUserRegistered()) {
          echo 'CON '.$menu->menuRegistered($user->readName());
      }
      elseif ($text =="" && !$user->isUserRegistered()) {
          $menu->menuUnregistered();
      }

      elseif ($user->isUserRegistered()){
         $array = explode("*",$text);
         switch ($array[0]) {
             case 1:
                 $menu->sendMoneyMenu($array,$user);
                 break;
             case 2:
                 $menu->withdrawMoneyMenu($array,$user);
                 break;
             case 3:
                 $menu->checkBalanceMenu($array,$phoneNumber);
                 break;
             default:
                 $level = count($array) - 1;
                 $menu->persistInvalidEntry($sessionId,$user->readUserId(),$level);
                 echo "CON Invalid choice.\n".$menu->menuRegistered($phoneNumber);
         }
      }else {
          $text_array = explode("*",$text);
          switch ($text_array[0]) {
              case 1:
                  $menu->registerMenu($text_array,$phoneNumber);
                  break;
              default:
                  echo "END Invalid choice. ";
          }
      }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function insertNotifications(Request $request)
    {
        UssdNotification::create([
           'date' => $request->date,
           'sessionId' => $request->sessionId,
           'serviceCode' => $request->serviceCode,
           'networkCode' => $request->networkCode,
           'phoneNumber' => $request->phoneNumber,
           'status' => $request->status,
           'cost' => $request->cost,
           'durationInMillis' => $request->durationInMillis,
           'hopsCount' => $request->hopsCount,
           'input' => $request->input,
           'lastAppResponse' => $request->lastAppResponse,
           'errorMessage' => $request->errorMessage?:'success',
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function incomingSms(Request $request)
    {
      $from = $request->from;
      $text = $request->text;

      $data = explode(" ",$text);
      $user = new UserHelper($from);
      $user->setName($data[0]);
      $user->setPin($data[0]);
      $user->setBalance(Util::$WALLET_BALANCE);
      $user->register();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
