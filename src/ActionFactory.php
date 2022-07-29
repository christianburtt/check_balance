<?php

namespace Factories;

class ActionFactory{

    static public function createActionList($user){
        $repo = file_get_contents(__DIR__."/../repositories/".$user.".json");
        $json_array = json_decode($repo);
        // print_r($json_array);
        $allActions = [];
        foreach($json_array as $item){
            if(!isset($item->duration))$item->duration = 0;
            if(!isset($item->cash))$item->cash=0;
            $allActions []= new \ValueObject\Action($item->id,$item->type,$item->timestamp,$item->duration,$item->cash);
        }
        return new \ValueObject\ActionList($allActions);
    }
}