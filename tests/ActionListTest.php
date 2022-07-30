<?php

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

require __DIR__."/../src/ActionList.php";
require __DIR__."/../src/ActionFactory.php";
require __DIR__."/../src/Boosters.php";

/**
 * @coversDefaultClass \Aggregates\ActionList
 * 
 */
final class ActionListTest extends TestCase{
    /**
     * @covers ::__construct
     * @covers \ValueObject\Boosters::__construct
     */
    public function testCreateActionListNoArguments(){
        $this->assertInstanceOf(
            \Aggregates\ActionList::class,
            new \Aggregates\ActionList()
        );
    }
    /**
     * @covers ::__construct
     * @covers \ValueObject\Boosters::__construct
     * @covers \ValueObject\Action::__construct
     * 
     */
    public function testCreateActionListWithArray(){
        $args = [];
        $args []= new  \ValueObject\Action("43de22","delivery","2022-07-27 12:12:12");
        $args []= new \ValueObject\Action("43de22","rideshare","2022-07-27 12:12:12");
        $this->assertInstanceOf(
            \Aggregates\ActionList::class,
            new \Aggregates\ActionList($args)
        );
        
    }
    /**
     * @covers ::append
     * @covers ::__construct
     * @covers \ValueObject\Action::__construct
     * @covers \ValueObject\Boosters::__construct
     */
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
    /**
     * @covers ::sort
     * @covers ::__construct
     * @covers ::timeCompare
     * @covers \ValueObject\Action::__construct
     * @covers \ValueObject\Boosters::__construct
     * @covers \ValueObject\Action::getTimestamp
     */
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
    /**
     * @covers ::displayItems
     * @covers ::__construct
     * @covers ::append
     * @covers \ValueObject\Boosters::__construct
     * @covers \ValueObject\Action::__construct
     * @covers \ValueObject\Action::getTimestamp
     * @covers \Factories\ActionFactory::createActionList
     */
    public function testDisplayItems(){
        $myList =  \Factories\ActionFactory::createActionList("matt");
        $this->assertIsArray($myList->masterList);
        $this->assertIsArray(($myList->displayItems()));
        $this->assertIsString($myList->displayItems()[0]['time']);
    }
    /**
     * @covers ::__construct
     * @covers ::append
     * @covers ::sort
     * @covers ::timeCompare
     * @covers ::calculateBalance
     * @covers \ValueObject\Boosters::__construct
     * @covers \ValueObject\Boosters::getPossibleBoosts
     * @covers \ValueObject\Boosters::validateBoosts
     * @covers \ValueObject\Action::__construct
     * @covers \ValueObject\Action::calculate_points
     * @covers \ValueObject\Action::getTimestamp
     * @covers \Factories\ActionFactory::createActionList
     */
    public function testCheckBalance(){
        $myList =  \Factories\ActionFactory::createActionList("matt");
        $this->assertEquals(14,
            $myList->calculateBalance()
        );
    }

}