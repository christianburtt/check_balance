<?php

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * @coversDefaultClass \ValueObject\Boosters
 *  
 */
final class BoostersTest extends TestCase{

    /**
     * @covers ::__construct
     * 
     */
    public function testCreateBooster(){
        $this->assertInstanceOf(
            \ValueObject\Boosters::class,
            new \ValueObject\Boosters()
        );
        
    }
    /**
     * @covers ::getPossibleBoosts
     * @covers ::__construct
     * @covers \ValueObject\Action::__construct
     * @covers \ValueObject\Action::getTimestamp
     * 
     */
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
    /**
     * @covers ::validateBoosts
     * @covers \ValueObject\Action::__construct
     * @covers ::getPossibleBoosts
     * @covers ::__construct
     * @covers \ValueObject\Action::getTimestamp
     * 
     */
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