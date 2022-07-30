<?php

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

require __DIR__."/../src/Action.php";
/**
 * @coversDefaultClass \ValueObject\Action
 *  
 */
final class ActionTest extends TestCase{
    /**
     * @covers ::__construct
     * 
     */
    public function testCreateActionNoDuration(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","delivery",time())
        );
    }
     /**
     * @covers ::__construct
     * 
     */
    public function testCreateActionWithDuration(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","rent",time(),3)
        );
    }
     /**
     * @covers ::__construct
     * 
     */
    public function testCreateActionWithCashOut(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","cashout",time(),0,12)
        );
    }
     /**
     * @coversNothing
     * 
     */
    public function testNullConstructorCall(){
        $this->expectException(\InvalidArgumentException::class);
        $action = new \ValueObject\Action(null, null, null);
    }
     /**
     * @covers ::calculate_points
     * @covers ::__construct
     * 
     */
    public function testCalculatePointsDelivery(){
        $testAction = new \ValueObject\Action("ff4d3a","delivery","2022-07-27 12:12:12");
        $this->assertEquals(1,$testAction->calculate_points());
    }
    /**
     * @covers ::calculate_points
     * @covers ::__construct
     * 
     */
    public function testCalculatePointsRent(){
        $testAction = new \ValueObject\Action("ff4d3a","rent","2022-07-27 12:12:12",3);
        $this->assertEquals(6,$testAction->calculate_points());
    }
    /**
     * @covers ::calculate_points
     * @covers ::__construct
     * 
     */
    public function testCalculatePointsCashOut(){
        $testAction = new \ValueObject\Action("ff4d3a","cashout","2022-07-27 12:12:12",0,14);
        $this->assertEquals(-14,$testAction->calculate_points());
    }
    /**
     * @covers ::getTimestamp
     * @covers ::__construct
     * 
     */
    public function testGetTimeStamp(){
        $testAction = new \ValueObject\Action("ff4d3a","cashout","2022-07-27 12:12:12",0,14);
        $this->assertIsString($testAction->getTimestamp());
        $this->assertEquals("2022-07-27 12:12:12",$testAction->getTimestamp());
    }
}