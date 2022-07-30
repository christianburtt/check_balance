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
  /**
   * Matts data:
   *    delivery points: 6 , 6 with 5 of which got a boost, but was over a month ago, so not added
   *    rent points: 8
   *    rideshare points: 10, 5 of which got a boost
   *    cashouts: -10, 1 withdrawal
   *    total: 14
   */
 $mattList = ActionFactory::createActionList("matt");
 print("matt: ".$mattList->calculateBalance()."\n");

 /**
  * Mark data (from initial problem statement):
  *     delivery points: 12, 7 with 5 boosts
  *     rent points: 6
  *     rideshare points: 3
  *     cashouts: 0
  *     total: 21 (after 1 month it will be 16)
  */
 $markList = ActionFactory::createActionList("mark");
 print_r("mark: ".$markList->calculateBalance()."\n");


 /**
  * Luke data
  *     delivery points: 10, 5 that got 5 boosted points
  *     rent points: 18
  *     rideshare points: 5. 4 got almost bosted but not quite
  *     cashouts: -18
  *     total: 15.  this will stay after one month because the boosted points were withdrawan FIRST
  */
 $lukeList = ActionFactory::createActionList("luke");
 print_r("luke: ".$lukeList->calculateBalance());

 /**
  * IF you want to see the amounts broken down by category/data stream
  * uncomment the lines below
  */
//  print_r("\ndelivery: ".$lukeList->caclulateBalance("delivery"));
//  print_r("\nrent: ".$lukeList->caclulateBalance("rent"));
//  print_r("\nrideshare: ".$lukeList->caclulateBalance("rideshare"));
//  print_r("\nwithdrawals: ".$lukeList->caclulateBalance("cashout"));
//  print_r("\nTotal: ".$lukeList->caclulateBalance());

