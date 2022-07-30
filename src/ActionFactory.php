<?php

/**
 * Action Factory
 * Theoretically, this factory would create an action list from pulling
 * data from different respositories, using a read model from CQRS.
 * I could imagine pulling from different apps (delivery, rideshare, renting, etc),
 * or from APIS or from a DB directly.  In this case, I used .json text files
 * to store the data to act as a stub.
 * 
 * Both additions / actions of work "delivery", "rideshare", etc
 * AND Cash-Outs are in the lists.  So in the case of matt and luke, they've taken
 * some money out already.
 */

namespace Factories;

use ValueObject\ActionList;

class ActionFactory {

    //static because it returns an ActionList not itself
    /**
     * the User ID could be string or DB identifier.  IN this case its the user name
     * which is the title of the text file.
     * @param mixed $user 
     * @return ActionList 
     */
    static public function createActionList($user) {
        //Again, this would be an API or DB call- probably multiple.  
        //one for delliveries, one for rideshares, one for withdrawals of cash
        //in this case, it's already been aggregated by the "API" aka the file.

        //get all the files and find only the ones we want
        $allFiles = scandir(__DIR__ . "/../repositories/");
        $actionList = new \ValueObject\ActionList();
        foreach ($allFiles as $file) {
            //if the file is not for our user, skip
            if(stripos($file,$user) !== false){
                
                $json_array = json_decode(file_get_contents(__DIR__ . "/../repositories/" . $file));
                $allActions = [];
                foreach ($json_array as $item) {
                    //default optional values
                    if (!isset($item->duration)) $item->duration = 0;
                    if (!isset($item->cash)) $item->cash = 0;
                    //add to the main array
                    $allActions[] = new \ValueObject\Action($item->id, $item->type, $item->timestamp, $item->duration, $item->cash);
                }
                $actionList->append($allActions);
            }
        }
        return $actionList;
    }
}
