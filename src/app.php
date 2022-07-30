<?php
/**
 * Starting point for app for Incentives program
 */

use Factories\ActionFactory;
/**
 * Do all my includes here.
 */
 include __DIR__.'/Action.php';
 include __DIR__.'/Boosters.php';
 include __DIR__.'/ActionFactory.php';
 include __DIR__.'/ActionList.php';


 /**
  * 3 different tests from different repositories/files
  */
//  $mattList = ActionFactory::createActionList("matt");
//  print("matt: ".$mattList->caclulateBalance()."\n");

//  $markList = ActionFactory::createActionList("mark");
//  print_r("mark: ".$markList->caclulateBalance()."\n");

 $lukeList = ActionFactory::createActionList("matt");
 print_r("rent: ".$lukeList->caclulateBalance("rent"));

 print_r("\ndelivery: ".$lukeList->caclulateBalance("delivery"));
 print_r("\nrideshare: ".$lukeList->caclulateBalance("rideshare"));
 print_r("\nwithdrawals: ".$lukeList->caclulateBalance("cashout"));
 print_r("\nTotal: ".$lukeList->caclulateBalance());
// print_r($lukeList->caclulateBalance());
// $testList = ActionFactory::createActionList("luke","delivery");
// print_r($testList->caclulateBalance());

