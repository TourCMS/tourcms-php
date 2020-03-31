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
        $tmpdir = dirname(__DIR__) . "/.tmp";
        $adapter = new Local($tmpdir);
        $filesystem = new Filesystem($adapter);
        $flysystem = new Flysystem($filesystem);
        $this->cache = new SimpleCache($flysystem);
    }

    protected function tearDown() : void
    {
        $this->cache->clear();
        unset($this->cache);
        Mockery::close();
    }

    /** @test */
    public function it_can_instantiate_tourcms_without_a_cache_driver()
    {
        $tourcms = new TourCMS(0, "key", "simplexml", 0);

        $this->assertInstanceOf(TourCMS::class, $tourcms);
    }

    /** @test */
    public function it_can_retrieve_results_without_a_cache_driver()
    {
        $tourcms = $this->getMockedTourCMS("simplexml", false);

        $response = $tourcms->search_tours();

        $this->assertIsFromRemote($response);
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

    /** @test */
    public function it_returns_remote_result_for_methods_that_arent_in_the_cache_timeouts_config_array()
    {
        $tourcms = $this->getMockedTourCMS();

        $response = $tourcms->start_new_booking($this->getSampleXmlPayload(), 1);

        $this->assertInstanceOf(SimpleXMLElement::class, $response);
        $this->assertIsFromRemote($response);
    }

    /** @test */
    public function it_returns_remote_result_for_cacheable_method_that_hasnt_been_cached_before()
    {
        $tourcms = $this->getMockedTourCMS();

        $response = $tourcms->search_tours();

        $this->assertIsFromRemote($response);
    }

    /** @test */
    public function it_persists_response_in_cache_when_returning_remote_response_for_method_that_should_be_cached()
    {
        $tourcms = $this->getMockedTourCMS();

        $response = $tourcms->search_tours();

        $this->assertTrue($this->cache->has('p_tours_search.xml'));
        $this->assertIsFromRemote(new SimpleXMLElement($this->cache->get('p_tours_search.xml')));
    }

    /** @test */
    public function it_returns_the_response_from_cache_when_there_is_one_persisted()
    {
        $tourcms = $this->getMockedTourCMS();

        $tourcms->search_tours();

        $response = $tourcms->search_tours();

        $this->assertIsFromCache($response);
    }

    /** @test */
    public function it_assigns_the_time_to_live_to_the_cache_objects_and_therefore_returns_remote_response_when_the_ttl_expired()
    {
        $timeouts = $this->getStandardTimeouts();
        $timeouts["search_tours"] = ["time" => 1];
        $tourcms = $this->getMockedTourCMS("simplexml", true, $timeouts, 2);

        $response1 = $tourcms->search_tours();

        sleep(2);

        $response2 = $tourcms->search_tours();

        //the actual assertion is done by mockery by ensuring that request_from_remote is called twice

        $this->assertIsFromRemote($response1);
        $this->assertIsFromRemote($response2);
    }

    /** @test */
    public function it_returns_proper_response_when_tourcms_is_instantiated_with_raw_flag()
    {
        $tourcms = $this->getMockedTourCMS('raw', false);

        $response = $tourcms->search_tours();

        $this->assertIsString($response);
    }

    /** @test */
    public function it_returns_string_for_cached_response_with_result_type_raw()
    {
        $tourmcs = $this->getMockedTourCMS('raw');

        $this->cache->set('p_tours_search.xml', $this->getCachedResponse()->asXML(), 1000);

        $result = $tourmcs->search_tours();
        $resultObject = new SimpleXMLElement($result);

        $this->assertIsString($result);
        $this->assertIsFromCache($resultObject);
    }

    public function getStandardTimeouts()
    {
        return [
            "search_tours" => ["time" => 1800],
            "show_tour" => ["time" => 3600],
            "show_tour_datesanddeals" => ["time" => 900],
            "list_channels" => ["time" => 3600],
            "show_channel" => ["time" => 3600],
            "show_supplier" => ["time" => 360]
        ];
    }

    /**
     * @param string $format
     * @param bool $cache
     * @param null $timeouts
     * @param int $callCount an expectation to how often request_from_remote should be called
     * @return Mockery\Mock
     */
    public function getMockedTourCMS($format = "simplexml", $cache = true, $timeouts = null, $callCount = 100)
    {
        $response = $format === "simplexml" ? $this->getRemoteResponse() : $this->getRemoteResponse()->asXML();
        $cache = $cache ? $this->cache : null;
        $timeouts = $timeouts ? $timeouts : null;

        $callCountFrom = $callCount === 100 ? 0 : $callCount;

        $tourcms = Mockery::mock(
            TourCMS::class . "[request_from_remote]",
            [0, "key", $format, 0, $cache, $timeouts]
        )->shouldAllowMockingProtectedMethods();

        $tourcms->shouldReceive('request_from_remote')
            ->andReturn($response)
            ->between($callCountFrom, $callCount);

        return $tourcms;
    }

    public function getCachedResponse()
    {
        return new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <response>
                <request>POST /c/some/url.xml</request>
                <error>OK</error>
                <source>cache</source>
            </response>");
    }

    public function getRemoteResponse()
    {
        return new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <response>
                <request>POST /c/some/url.xml</request>
                <error>OK</error>
                <source>remote</source>
            </response>");
    }

    public function getSampleXmlPayload()
    {
        return new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <booking>
                
            </booking>");
    }

    public function assertIsFromRemote(SimpleXMLElement $response)
    {
        $source = (string) $response->source;
        $this->assertEquals("remote", $source);
    }

    public function assertIsFromCache(SimpleXMLElement $response)
    {
        $source = (string) $response->source;
        $this->assertEquals("cache", $source);
    }


}