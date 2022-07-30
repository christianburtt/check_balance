<?php

/**
 * Class ActionList
 * An ActionList Object is an Aggregate object. It's root Entitiy SHOULD be a User Entity
 * but in this test case, we don't have access to the User Entity, so it
 * somewhat acts as its own root.
 */

namespace Aggregates;

use DateInterval;
use DateTime;

class ActionList {
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
     * @var Boosters 
     */
    private $boosters;

    /**
     * constructor
     * @param array $actions of Action Objects - Optional
     * @return void 
     */
    public function __construct($actions = null) {
        $this->masterList = [];
        if ($actions) {
            foreach ($actions as $action) {
                if ($action->type == "cashout") {
                    $this->withdrawals[] = $action;
                } else {
                    $this->masterList[$action->type][] = $action;
                }
            }
        }
        $this->boosters = new \ValueObject\Boosters();
        $possibleBoosts = [];
    }

    /**
     * Receives an array of Action objects and adds them to the main list
     * @param array $actions 
     * @return void 
     */
    public function append($actions): void {
        foreach ($actions as $action) {
            if ($action->type == "cashout") {
                $this->withdrawals[] = $action;
            } else {
                $this->masterList[$action->type][] = $action;
            }
        }
    }

    /**
     * Sorts the list by timestamp.
     * @return bool 
     */
    public function sort($type): bool {
        if ($type == "cashout") {
            if (!$this->withdrawals) return false;
            return usort($this->withdrawals, array($this, "timeCompare"));
        } else {
            return usort($this->masterList[$type], array($this, "timeCompare"));
        }
    }

    /**
     * protected callback function for testing timestamps for the sort function
     * @param \ValueObject\Action $actionA 
     * @param \ValueObject\Action $actionB 
     * @return int -1 if ActionA comes first, 0 at the exact same time (unlikely), 1 if ActionA comes later.
     */
    protected function timeCompare($actionA, $actionB): int {
        return $actionA->getTimestamp() <=> $actionB->getTimestamp();
    }


    /**
     * This is the workhorse of the algorithm.
     * ASSUMPTIONS: the list is NOT sorted 
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
    public function caclulateBalance($type = ""): int {
        $total = 0;

        //When there IS a type
        if ($type !== "") {
            //sort this part of the list
            $this->sort($type);
            $this->possibleBoosts = [];
            // $boostRule = null;
            // foreach($this->boosters->rules as $rule){
            //     if($rule->type == $type){
            //         $boostRule=$rule;
            //         break;
            //     }
            // }
            // $possibleBoostCount = 0; //this will have to match the number of actions in a timeframe
            // $possibleBoostStart = 0; //this will start the clock on the timeframe

            if ($type == "cashout") {
                foreach ($this->withdrawals as $withdrawal) {
                    $total += $withdrawal->calculate_points();
                }
            } else {
                $this->possibleBoosts = $this->boosters->getPossibleBoosts($type, $this->masterList[$type]);

                foreach ($this->masterList[$type] as $action) {
                    $total += $action->calculate_points();
                }
                //FINALLY we do a loop just throug the valid boosts to check the list again to see if the valid boosts got cashed out or expired
                $total += $this->boosters->validateBoosts($this->possibleBoosts, $this->withdrawals);
            }
            return $total;
        } else {
            //sort withdrawals first
            $this->sort('cashout');

            //recursively call this function on each type
            //Note that withdrawals/cashouts are a different list
            foreach ($this->masterList as $type => $list) {
                $total += $this->caclulateBalance($type);
            }
            //add up the withdrawals as well
            foreach ($this->withdrawals as $withdrawal) {
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
    public function displayItems(): array {
        $itemList = [];
        foreach ($this->masterList as $subList) {
            foreach ($subList as $item) {
                $itemList[] = [
                    'type' => $item->type,
                    'time' => $item->getTimestamp()
                ];
            }
        }
        return $itemList;
    }
}
