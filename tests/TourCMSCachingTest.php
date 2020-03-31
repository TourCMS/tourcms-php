<?php
/* Created by cornelius on  30.03.20 */

namespace TourCMS\Utils\tests;


use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use Mockery;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use TourCMS\Utils\TourCMS;

class TourCMSCachingTest extends TestCase
{

    protected $cache = null;

    protected function setUp() : void
    {
        $adapter = new Local(sys_get_temp_dir());
        $filesystem = new Filesystem($adapter);
        $flysystem = new Flysystem($filesystem);
        $this->cache = new SimpleCache($flysystem);
    }

    protected function tearDown() : void
    {
        unset($this->cache);
    }

    /** @test */
    public function it_can_instantiate_tourcms_without_a_cache_driver()
    {
        $tourcms = new TourCMS(0, "key", "simplexml", 0);

        $this->assertInstanceOf(TourCMS::class, $tourcms);
    }

    /** @test */
    public function it_can_receive_a_psr16_instance_as_constructor_argument(){
        $tourcms = new TourCMS(0, "key", "simplexml", 0, $this->cache);

        $this->assertInstanceOf(TourCMS::class, $tourcms);
    }

    /** @test */
    public function it_can_receive_a_config_array_containing_timeouts_as_constructor_argument(){
        $timeouts = $this->getStandardTimeouts();
        $tourcms = new TourCMS(0, "key", "simplexml", 0, $this->cache, $timeouts);

        $this->assertInstanceOf(TourCMS::class, $tourcms);
    }

    /** @tes */
    public function it_can_call_remote_methods_and_returns_mocked_response()
    {
        $tourcms = $this->getMockedTourCMS();
        $result = $tourcms->show_tour_datesanddeals(1, 1);
        $this->assertInstanceOf(SimpleXMLElement::class, $result);

    }


    function getStandardTimeouts()
    {
        return ["search_tours" => ["time" => 1800],
            "show_tour" => ["time" => 3600],
            "show_tour_datesanddeals" => ["time" => 900],
            "list_channels" => ["time" => 3600],
            "show_channel" => ["time" => 3600],
            "show_supplier" => ["time" => 360]
        ];
    }

    function getMockedTourCMS()
    {
        $response = $this->getCachedResponse();

        $tourcms = Mockery::mock(
            TourCMS::class . "[request_from_remote]",
            [0, "key", "simplexml", 0, $this->cache]
        )->shouldAllowMockingProtectedMethods();

        $tourcms->shouldReceive('request_from_remote')
            ->andReturn($response);

        return $tourcms;
    }

    function getCachedResponse()
    {
        return new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <response>
                <request>POST /c/some/url.xml</request>
                <error>OK</error>
                <cached>1</cached>
            </response>");
    }

    function getRemoteResponse()
    {
        return new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <response>
                <request>POST /c/some/url.xml</request>
                <error>OK</error>
                <remote>1</remote>
            </response>");
    }
}