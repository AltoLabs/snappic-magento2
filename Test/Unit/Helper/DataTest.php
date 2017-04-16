<?php

namespace AltoLabs\Snappic\Test\Unit\Helper;

use AltoLabs\Snappic\Helper\Data;

/**
 * @coversDefaultClass \AltoLabs\Snappic\Helper\Data
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject(Data::class);
    }

    /**
     * Test that the API host can be set manually, or a default value used
     *
     * @covers ::getApiHost
     * @covers ::getStoreAssetsHost
     * @covers ::getSnappicAdminUrl
     * @covers ::getEnvOrDefault
     */
    public function testApiHostCanBeConfiguredFromEnvironmentOrDefault()
    {
        $this->assertSame(Data::API_HOST_DEFAULT, $this->helper->getApiHost());

        putenv('SNAPPIC_API_HOST=foobar.com');
        $this->assertSame('foobar.com', $this->helper->getApiHost());
    }
}
