<?php

namespace ValueObject;
/**
 * Boosters is a bit amorphous.
 * I assumed, because we didn't have the data, that they are currently running all the time
 * but there is the possibility of changging the activeStart and activeEnd.
 * I would imagine having more boosters in a repository and receivin them via an API call or a 
 * direct DB read
 * 
 */
class Boosters{
    public $rules;

    public function __construct(){
        //these rules were set in the business requirements. I added activeStart and activeEnd
        //to be from 00:00 to 23:59, so techincally in my system the boosts would not happen as a 
        //day passed
        $this->rules = json_decode('[{"type":"delivery","requirement":"5","duration":"2","bonus":"5","activeStart":"00:00","activeEnd":"23:59"},
        {"type":"rideshare","requirement":5,"duration":8,"bonus":10,"activeStart":"00:00","activeEnd":"23:59"}]');
    }
}