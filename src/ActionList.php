<?php
/**
 * Class ActionList
 * An ActionList Object is an Aggregate object. It's root Entitiy SHOULD be a User Entity
 * but in this test case, we don't have access to the User Entity, so it
 * somewhat acts as its own root.
 */
namespace ValueObject;

use DateInterval;
use DateTime;

Class ActionList{
    /**
     * an 2-D array of Action Objects, sorted by "type" of action
     * so that each data respository has it's own list of actions, not including withdrawals
     * @var array
     */
    public $masterList;

    /**
     * a 1-D array of Withdrawal Actions
     * @var array
     */
    private $withdrawals;

    /**
     * a 1-d array of possible Boosted/Bonus points
     * @var array
     */
    private $possibleBoosts;

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
        $this->masterList = [];
        if($actions){
            foreach($actions as $action){
                if($action->type == "cashout"){
                    $this->withdrawals []=$action;
                }else{
                    $this->masterList[$action->type] []=$action;
                }
            }
        }
        $this->boosterRules = new Boosters();
        $possibleBoosts = [];
    }

    /**
     * Receives an array of Action objects and adds them to the main list
     * @param array $actions 
     * @return void 
     */
    public function append($actions):void{
        foreach($actions as $action){
            if($action->type == "cashout"){
                $this->withdrawals []=$action;
            }else{
                $this->masterList[$action->type] []=$action;
            }
        }
    }

    /**
     * Sorts the list by timestamp.
     * @return bool 
     */
    public function sort($type) :bool{
        if($type == "cashout"){
            if(!$this->withdrawals) return false;
            return usort($this->withdrawals, array($this,"timeCompare"));
        }else{
            return usort($this->masterList[$type], array($this,"timeCompare"));
        }
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
    // public function getLastActionTime():string{

    //     $lastActionTime = new \DateTime();
    //     $lastActionTime->setTimestamp($this->list[count($this->list)-1]->getTimestamp());
    //     return $lastActionTime->format('Y-m-d H:i:s.f');
    // }

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
     * @param string type = if set, it will only calculate the balance of the specific List
     * @return int 
     */
    public function caclulateBalance($type=""):int{
        //When there IS a type
        $total = 0;

        if($type!==""){
            $this->sort($type);
            $this->possibleBoosts = [];
            $boostRule = null;
            foreach($this->boosterRules->rules as $rule){
                if($rule->type == $type){
                    $boostRule=$rule;
                    break;
                }
            }
            $possibleBoostCount = 0; //this will have to match the number of actions in a timeframe
            $possibleBoostStart = 0; //this will start the clock on the timeframe

            if($type=="cashout"){
                foreach($this->withdrawals as $withdrawal){
                    $total +=$withdrawal->calculate_points();
                }
            }else{
                foreach($this->masterList[$type] as $action){
                    $total +=$action->calculate_points();
                    if($boostRule){
                        //check the time to make sure it's during the boosts active time
                        $actionTime = new DateTime($action->getTimestamp());
                        // $actionTime->setTimestamp($action->timestamp);
                        $timeString = $actionTime->format('H:i'); //this gives us something like "11:43" or "08:52" or "21:37"
                        //if the format 08:00 is outside the active times of the boost, then 
                        //do nothing
                        if($timeString<$boostRule->activeStart || $timeString > $boostRule->activeEnd ){ 
                            $possibleBoostCount=0;
                            $possibleBoostStart=0;
                            continue;
                        }
                        //otherwise we have a valid boost possibility 

                        if($possibleBoostStart==0){ //we're just starting
                            $possibleBoostCount++;
                            $possibleBoostStart = $action->getTimestamp();
                        }else{
                            //check the last timestamp
                            $possibleTime = new DateTime($possibleBoostStart);
                            // $possibleTime->setTimestamp($possibleBoostStart);

                            //get the interval between the first possible boost and now.
                            $interval = $possibleTime->diff($actionTime);
                            //if it's within the limit, we're good
                            //that means no years, months, or whole days have passed AND the hour difference is within the 
                            //boost rule duration
                            if( $interval->y == 0 && $interval->m==0 && $interval->d==0 && $interval->h < $boostRule->duration){
                                $possibleBoostCount++; //add an action to the counter

                                //if we hit the required number within the time limit, add it to the array
                                if($possibleBoostCount == $boostRule->requirement){
                                    $this->possibleBoosts []= ['points'=>$possibleBoostCount,'start'=>$possibleBoostStart,'expiry'=>$possibleTime->add(new DateInterval("P30D"))->format("Y-m-d H:i:s")];
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
                foreach($this->possibleBoosts as $boost){

                    //quick check to see if it's not been a month and they are still valid
                    if($this->masterList[$type][count($this->masterList[$type])-1]->getTimestamp() < $boost['expiry']){
                        $total +=$boost['points'];
                        continue;
                    }
                    foreach($this->withdrawals as $withdrawal){
                    
                        //if the cash out happened after the first action of the boost AND before it expired
                        //add it to the balance and break out of this inner foreach loop to go to the next boost
                        if($withdrawal->getTimestamp() > $boost['start'] && $withdrawal->getTimestamp()<$boost['expiry']) {
                            $total +=$boost['points'];
                            break;
                        }
                        
                    }
                }
            }
            return $total;
        }else{
            //sort withdrawals first
            $this->sort('cashout');
            //recursively call this function on each type
            foreach($this->masterList as $type=>$list){
                $total += $this->caclulateBalance($type);
            }
            foreach($this->withdrawals as $withdrawal){
                $total += $withdrawal->calculate_points(); //this will be a negative number
            }
            return $total; //this is the balance of all actions with bonus/boosts and Cash-outs that a user has done.
        }
        
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