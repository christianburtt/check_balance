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
 $mattList = ActionFactory::createActionList("matt");
 print("matt: ".$mattList->caclulateBalance()."\n");

 $markList = ActionFactory::createActionList("mark");
 print_r("mark: ".$markList->caclulateBalance()."\n");

 $lukeList = ActionFactory::createActionList("luke");
 print_r("luke: ".$lukeList->caclulateBalance());

