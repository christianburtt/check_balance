<?php
use PHPUnit\Framework\TestCase;
require __DIR__."/../src/ActionList.php";
require __DIR__."/../src/ActionFactory.php";
require __DIR__."/../src/Boosters.php";

final class ActionListTest extends TestCase{
    public function testCreateActionListNoArguments(){
        $this->assertInstanceOf(
            \ValueObject\ActionList::class,
            new \ValueObject\ActionList()
        );
    }
    public function testCreateActionListWithArray(){
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery",time());
        $args []= new \ValueObject\Action("43de22","rideshare",time());
        $this->assertInstanceOf(
            \ValueObject\ActionList::class,
            new \ValueObject\ActionList($args)
        );
        
    }
    public function testAppendLength(){
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery",time());
        $args []= new \ValueObject\Action("43de22","rideshare",time());
        $testList = new \ValueObject\ActionList();
        $testList->append($args);
        $this->assertEquals(
            2,
            count($testList->list)
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
        $early = $theTime->getTimestamp();
        $theTime->add( new DateInterval("PT30M"));
        $later = $theTime->getTimestamp();
        $theTime->add( new DateInterval("PT30M"));
        $latest = $theTime->getTimestamp();
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery",$later);
        $args []= new \ValueObject\Action("43de22","rideshare",$early);
        $args []= new \ValueObject\Action("43de22","rent",$latest,2);

        $testList = new \ValueObject\ActionList($args);
        $testList->sort();
        $this->assertEquals($early,$testList->list[0]->getTimestamp());
    }
    public function testLastTimeStamp(){
        $theTime = new DateTime();
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery",$theTime->getTimestamp());
        $testList = new \ValueObject\ActionList($args);
        $this->assertEquals($theTime->format('Y-m-d H:i:s.f'),$testList->getLastActionTime());
    }

    public function testCheckBalance(){
        $myList =  \Factories\ActionFactory::createActionList("matt");
        $this->assertEquals(19,
            $myList->caclulateBalance()
        );
    }

}