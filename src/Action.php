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

use InvalidArgumentException;


class Action{
    /**
     * the ID of the user.
     * Not really used, but possibly needed in the future.
     * @var mixed
     */
    private $user_id;

    /**
     * type of action
     * current accpeted values:
     *  - "rent"
     *  - "rideshare"
     *  - "delivery"
     *  - "cashout"
     * @var string
     */
    public $type;
    
    /**
     * timestamp of when the action was created/saved in the repository
     * ideally a 64-bit integer :D
     * @var int
     */
    public $timestamp;

    /**
     * duration of the action
     * only used sometimes (currently only for rent type), default is null
     * represented by an integer of whole days
     * @var int
     */
    private $duration; 

    /**
     * the absolute value of the cashout amount
     * only used for "cashout" action type
     * @var int
     */
    private $cashOutValue;

    /**
     * Default Constructor
     * @param mixed $id 
     * @param mixed $type 
     * @param mixed $timestamp 
     * @param int $duration optional
     * @param int $cash optional
     * @return void 
     */
    public function __construct($id, $type,$timestamp,$duration=0,$cash=0){
        if(!$id || !$type || !$timestamp) throw (new \InvalidArgumentException);
        $this->user_id = $id;
        $this->type=$type;
        $this->duration=$duration;
        $this->timestamp=$timestamp;
        $this->cashOutValue=$cash;
    }

    /**
     * calculates the points for this specific action.
     * If it's "rent" it's duration *2
     * if it's "cashout" it will be a negative int
     * @return int 
     */
    public function calculate_points() : int{
        if($this->type == "rent") return $this->duration*2;
        if($this->type == "cashout") return $this->cashOutValue*-1;

        return 1;
    }
    public function getTimestamp() : int{
        return $this->timestamp;
    }
    public function getPoints() : int{
        return $this->calculate_points();
    }

    
    
}