<?php

namespace Factories;

class ActionFactory{

    static public function createActionList($user){
        $repo = file_get_contents(__DIR__."/repositories/".$user.".json");
        $json_array = json_decode($repo);
        print_r($json_array);
    }
}