<?php
/* Created by cornelius on  30.03.20 */

namespace TourCMS\Utils\tests;


use Phpfastcache\Helper\Psr16Adapter;
use PHPUnit\Framework\TestCase;
use TourCMS\Utils\TourCMS;

class TourCMSCachingTest extends TestCase
{
    /** @test */
    public function it_can_receive_a_psr16_instance_as_constructor_argument(){
        $cache = new Psr16Adapter("Files");
        $tourcms = new TourCMS(0, "key", "simplexml", 0, $cache);

        $this->assertInstanceOf(TourCMS::class, $tourcms);
    }
}