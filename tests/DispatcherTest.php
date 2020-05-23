<?php


use Legion\Http\Request;
use PHPLegends\View\Finder;
use PHPLegends\View\Factory;
use Legion\Routing\Dispatcher;
use PHPUnit\Framework\TestCase;
use Legion\Http\ResponseFactory;


class FakeController{}

class DispatcherTest extends TestCase
{

    public function setUp()
    {
        $this->dispatcher = new Dispatcher(
            new Request('GET', '/'),
            new ResponseFactory(
                new Factory(
                    new Finder()
                )
            )
        );
    }


    public function testGetDefaultController()
    {
        $this->assertEquals(
            $this->dispatcher->getDefaultController(),
            \Legion\Controller\Controller::class
        );
    }

    public function testSetDefaultController()
    {
        try {

            $this->dispatcher->setDefaultController('not_found_controller_class');

        } catch (\Exception $e) {

            $this->assertEquals(
                get_class($e),
                'UnexpectedValueException'
            );
        }

        $this->dispatcher->setDefaultController(FakeController::class);

        $this->assertEquals(
            $this->dispatcher->getDefaultController(),
            FakeController::class
        );


    }



}