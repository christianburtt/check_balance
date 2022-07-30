<?php
use PHPUnit\Framework\TestCase;
require __DIR__."/../src/ActionList.php";
require __DIR__."/../src/ActionFactory.php";
require __DIR__."/../src/Boosters.php";

final class ActionListTest extends TestCase{
    public function testCreateActionListNoArguments(){
        $this->assertInstanceOf(
            \Aggregates\ActionList::class,
            new \Aggregates\ActionList()
        );
    }
    public function testCreateActionListWithArray(){
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery","2022-07-27 12:12:12");
        $args []= new \ValueObject\Action("43de22","rideshare","2022-07-27 12:12:12");
        $this->assertInstanceOf(
            \Aggregates\ActionList::class,
            new \Aggregates\ActionList($args)
        );
        
    }
    public function testAppendLength(){
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery","2022-07-27 12:12:12");
        $args []= new \ValueObject\Action("43de22","rideshare","2022-07-27 12:12:12");
        $testList = new \Aggregates\ActionList();
        $testList->append($args);
        $this->assertEquals(
            1,
            count($testList->masterList["delivery"])
        );
        $this->assertEquals(
            1,
            count($testList->masterList["rideshare"])
        );
    }
    //turned my method to protected so comment out this test.
    //Test passed first, by the way.
    // public function testTimeCompare(){
    //     $theTime = new DateTime();
    //     $early = $theTime->getTimestamp();
    //     $theTime->add( new DateInterval("PT30M"));
    //     $later = $theTime->getTimestamp();
    //     $args = [];
    //     $args []= new  \ValueObject\Action("43de22","delivery",$early);
    //     $args []= new \ValueObject\Action("43de22","rideshare",$later);
    //     $testList = new \ValueObject\ActionList($args);
    //     $this->assertEquals(-1,$testList->timeCompare($args[0],$args[1]));
    //     $this->assertEquals(0,$testList->timeCompare($args[0],$args[0]));
    //     $this->assertEquals(1,$testList->timeCompare($args[1],$args[0]));
    // }
    public function testListSort(){
        $theTime = new DateTime();
        $early = $theTime->format("Y-m-d H:i:s");
        $theTime->add( new DateInterval("PT30M"));
        $later = $theTime->format("Y-m-d H:i:s");
        $theTime->add( new DateInterval("PT30M"));
        $latest = $theTime->format("Y-m-d H:i:s");
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery",$later);
        $args []= new \ValueObject\Action("43de22","delivery",$early);
        $args []= new \ValueObject\Action("43de22","delivery",$latest,2);

        $testList = new \Aggregates\ActionList($args);
        $testList->sort('delivery');
        $this->assertEquals($early,$testList->masterList['delivery'][0]->getTimestamp());
    }
    public function testDisplayItems(){
        $myList =  \Factories\ActionFactory::createActionList("matt");
        $this->assertIsArray($myList->masterList);
        $this->assertIsArray(($myList->displayItems()));
        $this->assertIsString($myList->displayItems()[0]['time']);
    }

    public function testCheckBalance(){
        $myList =  \Factories\ActionFactory::createActionList("matt");
        $this->assertEquals(14,
            $myList->caclulateBalance()
        );
    }

}