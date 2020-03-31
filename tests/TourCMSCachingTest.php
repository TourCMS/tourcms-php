<?php
/* Created by cornelius on  30.03.20 */

namespace TourCMS\Utils\tests;


use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use PHPUnit\Framework\TestCase;
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
}