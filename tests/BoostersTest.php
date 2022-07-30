<?php
use PHPUnit\Framework\TestCase;

final class BoostersTest extends TestCase{


    public function testCreateBooster(){
        $this->assertInstanceOf(
            \ValueObject\Boosters::class,
            new \ValueObject\Boosters()
        );
        
    }

    public function testPossibleBoosts(){
        $args = [];
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:12:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:15:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:17:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:19:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:21:12");

        $booster = new \ValueObject\Boosters();
        $this->assertIsArray($booster->getPossibleBoosts("delivery",$args));
        $this->assertCount(0,$booster->getPossibleBoosts("rent",$args));
    }

    public function testValidateBoosts(){
        $args = [];
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:12:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:15:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:17:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:19:12");
        $args []= new \ValueObject\Action("43de22","delivery","2022-07-27 12:21:12");
        $booster = new \ValueObject\Boosters();
        $this->assertEquals(5,$booster->validateBoosts($booster->getPossibleBoosts("delivery",$args),array()));
    }

}