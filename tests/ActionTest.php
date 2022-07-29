<?php
use PHPUnit\Framework\TestCase;
require __DIR__."/../src/Action.php";
final class ActionTest extends TestCase{
    public function testCreateActionNoDuration(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","delivery",time())
        );
    }
    public function testCreateActionWithDuration(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","rent",time(),3)
        );
    }
    public function testCreateActionWithCashOut(){
        $this->assertInstanceOf(
            \ValueObject\Action::class,
            new \ValueObject\Action("ff4d3a","cashout",time(),0,12)
        );
    }
    public function testNullConstructorCall(){
        $this->expectException(InvalidArgumentException::class);
        new \ValueObject\Action(null, null, null);
    }
    public function testCalculatePointsDelivery(){
        $testAction = new \ValueObject\Action("ff4d3a","delivery",time());
        $this->assertEquals(1,$testAction->calculate_points());
    }
    public function testCalculatePointsRent(){
        $testAction = new \ValueObject\Action("ff4d3a","rent",time(),3);
        $this->assertEquals(6,$testAction->calculate_points());
    }
    public function testCalculatePointsCashOut(){
        $testAction = new \ValueObject\Action("ff4d3a","cashout",time(),0,14);
        $this->assertEquals(-14,$testAction->calculate_points());
    }
    public function testGetTimeStamp(){
        $testAction = new \ValueObject\Action("ff4d3a","cashout",time(),0,14);
        $this->assertIsInt($testAction->getTimestamp());
    }
}