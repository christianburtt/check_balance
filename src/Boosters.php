<?php

namespace ValueObject;
class Boosters{
    public $rules;

    public function __construct(){
        
        $this->rules = json_decode('[{"type":"delivery","requirement":"5","duration":"2","bonus":"5","activeStart":"00:00","activeEnd":"23:59"},
        {"type":"rideshare","requirement":5,"duration":8,"bonus":10,"activeStart":"00:00","activeEnd":"23:59"}]');
    }
}