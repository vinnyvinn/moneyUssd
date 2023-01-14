<?php

namespace App\Helpers;

use App\Models\Agent;

class AgentHelper
{

    protected $name;
    protected $number;
    public function __construct($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function readNameByNumber()
    {
       return Agent::where('agent_number',$this->number)->first()->name;
    }

    public function readIdByNumber()
    {
        return Agent::where('agent_number',$this->number)->first()->id;
    }
}
