<?php
/**
 * Class Action
 * Each action object represents something that a user has completed
 * Actions represent actions the user does in real life (delivery, rideshare, etc)
 *  AS WELL AS When a user cashes out points
 * It's basically an entry in a ledger.
 * namespaced to ValueObject
 */
namespace ValueObject;

use DateInterval;
use DateTime;
use stdClass;

Class ActionList{
    /**
     * an array of Action Objects
     * @var array
     */
    public $list;

    /**
     * An object of rules for boosting points.
     * @var object
     */
    private $boosterRules;

    /**
     * constructor
     * @param array $actions of Action Objects - Optional
     * @return void 
     */
    public function __construct($actions=null){
        $this->list = [];
        if($actions){
            foreach($actions as $action){
                $this->list []=$action;
            }
        }
        $this->boosterRules = new Boosters();
        $this->sort();
    }

    /**
     * Receives an array of Action objects and adds them to the main list
     * @param array $actions 
     * @return void 
     */
    public function append($actions):void{
        foreach($actions as $action){
            $this->list []=$action;
        }
        $this->sort();
    }

    /**
     * Sorts the list by timestamp.
     * @return bool 
     */
    public function sort() :bool{
        return usort($this->list, array($this,"timeCompare"));
    }

    /**
     * protected callback function for testing timestamps for the sort function
     * @param \ValueObject\Action $actionA 
     * @param \ValueObject\Action $actionB 
     * @return int -1 if ActionA comes first, 0 at the exact same time (unlikely), 1 if ActionA comes later.
     */
    protected function timeCompare($actionA, $actionB): int{
        return $actionA->getTimestamp() <=> $actionB->getTimestamp();
    }

    /**
     * Gets the last time stamp as a string.
     * EXPECTS list to be sorted!
     * @return string 
     */
    public function getLastActionTime():string{

        $lastActionTime = new \DateTime();
        $lastActionTime->setTimestamp($this->list[count($this->list)-1]->getTimestamp());
        return $lastActionTime->format('Y-m-d H:i:s.f');
    }

    /**
     * This is the workhorse of the algorithm.
     * ASSUMPTIONS: the list is sorted (this should be true, because we run automatically
     * after list creation and appending)
     * 
     * FIRST, we loop through and sum up all the action points. This takes debits and credits
     * THEN, we loop through each boost rule AND through the actions.
     * FNALLY, we loop through again to check if there are any checkouts before the expiry
     * 
     * The performance of this is not great, O((2+M)*N), where M is the number of booster rules and N is the
     * number of actions in the action list.
     * @return int 
     */
    public function caclulateBalance():int{
        $total = 0;
        //FIRST loop through and add up everything
        foreach($this->list as $action){
            $total += $action->calculate_points();
        }
        //this total has all the debits and credits EXCEPT for BONUSES.

        //NOW loop through each booster rule and check the list for start times and
        //validate if it meets the rules
        $validBoosts = [];
        foreach($this->boosterRules->rules as $rule){
            $possibleBoostCount = 0; //this will have to match the number of actions in a timeframe
            $possibleBoostStart = 0; //this will start the clock on the timeframe
            foreach($this->list as $action){
                //we're segmenting the list for only the same types of actions
                if($action->type!=$rule->type) continue;


                //check the time to make sure it's during the boosts active time
                $theTime = new DateTime();
                $theTime->setTimestamp($action->timestamp);
                $timeString = $theTime->format('H:i'); //this gives us something like "11:43" or "08:52" or "21:37"
                //if the format 08:00 is outside the active times of the boost, then 
                //do nothing
                if($timeString<$rule->activeStart || $timeString > $rule->activeEnd ){ 
                    $possibleBoostCount=0;
                    $possibleBoostStart=0;
                    continue;
                }
                //otherwise we have a valid boost possibility 

                if($possibleBoostStart==0){ //we're just starting
                    $possibleBoostCount++;
                    $possibleBoostStart = $action->timestamp;
                }else{
                    //check the last timestamp
                    $possibleTime = new DateTime();
                    $possibleTime->setTimestamp($possibleBoostStart);

                    //get the interval between the first possible boost and now.
                    $interval = $possibleTime->diff($theTime);
                    //if it's within the limit, we're good
                    if($interval->h < $rule->duration){
                        $possibleBoostCount++; //add an action to the counter

                        //if we hit the required number within the time limit, add it to the array
                        if($possibleBoostCount == $rule->requirement){
                            $validBoosts []= ['points'=>$possibleBoostCount,'start'=>$possibleBoostStart,'expiry'=>$possibleTime->add(new DateInterval("P30D"))->getTimestamp()];
                        }
                    }else{
                        //wer're past the duration now, so reset everything
                        $possibleBoostCount=0;
                        $possibleBoostStart=0;

                    }
                }
        
            }
        }

        //FINALLY we do a loop just throug the valid boosts to check the list again to see if the valid boosts got cashed out or expired
        foreach($validBoosts as $boost){
            //quick check to see if it's not been a month and they are still valid
            if($this->list[count($this->list)-1]->getTimestamp() < $boost['expiry']){
                $total +=$boost['points'];
                continue;
            }
            foreach($this->list as $action){
                //we're only checking for cash-out actions 
                if ($action->type =="cashout"){
                    //if the cash out happened after the first action of the boost AND before it expired
                    //add it to the balance and break out of this inner foreach loop to go to the next boost
                    if($action->timestamp > $boost['start'] && $action->timestamp<$boost['expiry']) {
                        $total +=$boost['points'];
                        break;
                    }
                }else{

                }
            }
        }


        return $total; //this is the balance of all actions with bonus/boosts and Cash-outs that a user has done.
    }

    /**
     * This was basically just to see the action type and a nicely-printed date
     * no test cases run on this, and it could be removed without any impact
     * @return array 
     */
    public function displayItems():array{
        $itemList = [];
        foreach($this->list as $item){
            $theTime = new DateTime();
            $theTime->setTimestamp($item->getTimestamp());
            $itemList []= [
                'type'=>$item->type,
                'time'=>$theTime->format('Y-m-d H:i:s')
                
            ];
        }
        return $itemList;
    }

}