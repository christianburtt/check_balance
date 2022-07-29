<?php
/**
 * Starting point for app for Incentives program
 */

use Factories\ActionFactory;

 include __DIR__.'/Action.php';
 include __DIR__.'/Boosters.php';
 include __DIR__.'/ActionFactory.php';
 include __DIR__.'/ActionList.php';


 
 $myList = ActionFactory::createActionList("mark");
 
//  $myActions []= new \ValueObject\Action("12", "delivery", time());
//  $actionList = new \ValueObject\ActionList($myActions);

//  print_r($actionList);
