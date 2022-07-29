<?php
/**
 * Starting point for app for Incentives program
 */

use Factories\ActionFactory;

 include __DIR__.'/Action.php';
 include __DIR__.'/Boosters.php';
 include __DIR__.'/ActionFactory.php';
 include __DIR__.'/ActionList.php';


 
 $mattList = ActionFactory::createActionList("matt");
 print_r($mattList->caclulateBalance());

 $markList = ActionFactory::createActionList("mark");
 print_r($markList->caclulateBalance());

 $lukeList = ActionFactory::createActionList("luke");
 print_r($lukeList->caclulateBalance());
//  print_r($myList->displayItems());
//   print_r($myList);
//  $myActions []= new \ValueObject\Action("12", "delivery", time());
//  $actionList = new \ValueObject\ActionList($myActions);

//  print_r($actionList);
