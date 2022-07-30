<?php

namespace ValueObject;

use DateTime;

/**
 * Boosters is a class for the Rules of Boosts as well as verifying if there ARE boosters
 * and validate if those possible boosts provide points.
 * 
 * I assumed, because we didn't have the data, that they are currently running all the time
 * but there is the possibility of changging the activeStart and activeEnd.
 * I would imagine having more boosters in a repository and receivin them via an API call or a 
 * direct DB read
 * 
 */
class Boosters {
    /**
     * the rules are the list of rules a group of activities must adhere to
     * in order to get bonus points.
     * the rules array has a collection of rules including:
     *      what type of activity it applies to
     *      The requirement number of activities to be completed
     *      The duration of the active window in hours. so if it's 2, the requirement activities must be 
     *          completed within 2 hours to be valid
     *      The bonus number of points the boost gives
     *      The active start and end times for the booost.
     * @var array
     */
    public $rules;

    public function __construct() {
        //these rules were set in the business requirements. I added activeStart and activeEnd
        //to be from 00:00 to 23:59, so techincally in my system the boosts would not happen as a 
        //day passed.
        //In a real situation, these rules would be saved in a DB or accessed via an API. 
        //in this case, I just wrote the two rules as direct JSON objects.
        $this->rules = json_decode('[{"type":"delivery","requirement":"5","duration":"2","bonus":"5","activeStart":"00:00","activeEnd":"23:59"},
        {"type":"rideshare","requirement":5,"duration":8,"bonus":10,"activeStart":"00:00","activeEnd":"23:59"}]');
    }

    /**
     * Checks a given type for rules that may apply to a list of Actions
     * It returns an array of possible boost points that have NOT BEEN VALIDATED
     * We could call validation at the end of this method, but I passed that to 
     * the aggregate entity
     * @param string $type of rule to apply
     * @param array $itemList list of actions to verify
     * @return array 
     */
    public function getPossibleBoosts($type, $itemList):array {
        //match the type first
        $ruleExists = false;
        $possibleBoosts = []; //empty array default value
        $boostRule = new \stdClass(); //just to hold the rule from the json array
        //check each rule to see if it matches the type
        foreach($this->rules as $rule){
            if($rule->type == $type){ 
                $ruleExists=true;
                $boostRule=$rule;
                break;
            }
        }
        //if no matching type, then return an empty array - no boosts possible
        if(!$ruleExists) return $possibleBoosts;

        //initialize counters
        $possibleBoostCount = 0;
        $possibleBoostStart = 0;
        foreach ($itemList as $action) {
            //check the time to make sure it's during the boosts active time
            $actionTime = new DateTime($action->getTimestamp());
            $timeString = $actionTime->format('H:i'); //this gives us something like "11:43" 
            //if the format 08:00 is outside the active times of the boost, then 
            //do nothing
            if ($timeString < $boostRule->activeStart || $timeString > $boostRule->activeEnd) {
                $possibleBoostCount = 0;
                $possibleBoostStart = 0;
                continue;
            }
            //otherwise we have a valid boost possibility 

            if ($possibleBoostStart == 0) { //we're just starting
                $possibleBoostCount++;
                $possibleBoostStart = $action->getTimestamp();
            } else {
                //check the last timestamp
                $possibleTime = new DateTime($possibleBoostStart);

                //get the interval between the first possible boost and now.
                $interval = $possibleTime->diff($actionTime);
                //if it's within the limit, we're good
                //that means no years, months, or whole days have passed AND the hour difference is within the 
                //boost rule duration
                if ($interval->y == 0 && $interval->m == 0 && $interval->d == 0 
                        && $interval->h < $boostRule->duration) {
                    $possibleBoostCount++; //add an action to the counter

                    //if we hit the required number within the time limit, add it to the array
                    if ($possibleBoostCount == $boostRule->requirement) {
                        $possibleBoosts[] = ['points' => $possibleBoostCount, 'start' => $possibleBoostStart, 'expiry' => $possibleTime->add(new \DateInterval("P30D"))->format("Y-m-d H:i:s")];
                    }
                } else {
                    //wer're past the duration now, so reset everything
                    $possibleBoostCount = 0;
                    $possibleBoostStart = 0;
                }
            }
        }
        return $possibleBoosts; //the 2-d array of possible boosts with points, start, and expiry
    }

    /**
     * This validates a 2-D array of possible boosts (From getPossibleBoosts)
     * agains the list of withdrawals.  
     * 
     * Here's the rationale:  boost points expire after 30 days. Since we are aggregating data
     * from different sources, we cannot assume the data streams know when the withdrawals occurred.
     * so if there were possible boosts 2 months ago, but no withdrawals, those points are lost
     * but if there was a withdrawal before 30 days, then we can add those boosted points as they were
     * WITHDRAWAN FIRST
     * @param array $possibleBoosts 2-d array, from getPossibleBoosts
     * @param mixed $withdrawals array of action items of ONLY withdrawals/cashouts
     * @return int the valid points from the boosts.
     */
    public function validateBoosts($possibleBoosts, $withdrawals):int {
        $total = 0; //initialize at 0
        foreach($possibleBoosts as $boost){

            //quick check to see if it's not been a month and they are still valid
            $theTime = new DateTime();
            if($theTime->format('Y-m-d H:i:s') < $boost['expiry']){
                //if it hasn't been 30 days, we definitely add the points, regardless of withdrawals.
                $total +=$boost['points'];
                continue;
            }
            //the boosts were over a month (aka 30 days) old, so check to see if they were used or not.
            foreach($withdrawals as $withdrawal){
            
                //if the cash out happened after the first action of the boost AND before it expired
                //add it to the balance and break out of this inner foreach loop to go to the next boost
                if($withdrawal->getTimestamp() > $boost['start'] && $withdrawal->getTimestamp()<$boost['expiry']) {
                    $total +=$boost['points'];
                    break;
                }
                
            }
        }
        return $total;
    }
}
