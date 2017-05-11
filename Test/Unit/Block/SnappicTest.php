<?php

namespace AltoLabs\Snappic\Test\Unit\Block;

class SnappicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AltoLabs\Snappic\Block\Snappic
     */
    protected $block;

    /**
     * @var \AltoLabs\Snappic\Model\Connect
     */
    protected $connect;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->connect = $this->getMockBuilder('AltoLabs\Snappic\Model\Connect')
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutSession = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = $objectManager->getObject('AltoLabs\Snappic\Block\Snappic', [
            'connect' => $this->connect,
            'checkoutSession' => $this->checkoutSession,
            'registry' => $this->registry
        ]);
    }

    /**
     * Test that a Snappic specific product ID can be returned from a given product model
     */
    public function testGetSnappicProductId()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $product->expects($this->once())->method('getId')->willReturn(123);

        $this->assertSame('snappic_123', $this->block->getSnappicProductId($product));
    }

    /**
     * Test that the Facebook ID is retrieved from the connect model
     */
    public function testGetFacebookIdFromConnectModel()
    {
        $this->connect->expects($this->once())->method('getFacebookId')->willReturn('abcdef');

        $this->assertSame('abcdef', $this->block->getFacebookId(false));
    }

    /**
     * Test that the visitor script will be shown when there is a Facebook ID, and not when there isn't
     *
     * @param mixed $mockFacebookId
     * @param bool  $expected
     * @dataProvider visitorScriptProvider
     */
    public function testGetShowVisitorScript($mockFacebookId, $expected)
    {
        $this->connect->expects($this->once())->method('getFacebookId')->willReturn($mockFacebookId);
        $this->assertSame($expected, $this->block->getShowVisitorScript());
    }

    /**
     * @return array[]
     */
    public function visitorScriptProvider()
    {
        return [
            [null, false],
            ['query', true]
        ];
    }

    /**
     * Test that the grand total for a given order is returned rounded to two decimal points
     */
    public function testGetOrderTotal()
    {
        $order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $order->expects($this->once())->method('getGrandTotal')->willReturn(12.3456);

        $this->assertSame(12.35, $this->block->getOrderTotal($order));
    }

    /**
     * Test various situations around when the conversion script should be displayed
     *
     * @param string|null $facebookId
     * @param \Magento\Sales\Model\Order|null $order
     * @param bool $expected
     * @dataProvider conversionScriptProvider
     */
    public function testGetShowConversionScript($facebookId, $order, $expected)
    {
        $this->connect->expects($this->once())->method('getFacebookId')->willReturn($facebookId);

        $this->checkoutSession
            ->expects($facebookId ? $this->once() : $this->never())
            ->method('getLastRealOrder')
            ->willReturn($order);

        $this->assertSame($expected, $this->block->getShowConversionScript());
    }

    /**
     * @return array[]
     */
    public function conversionScriptProvider()
    {
        $data = [
            // #1: No Facebook ID is set
            [
                null,
                null,
                false
            ],
            // #2: Facebook ID is set, but no order can be retrieved from the session
            [
                'qwerty',
                false,
                false
            ],
            // #3: Facebook ID is set, order is a model but no increment ID can be retrieved
            [
                'qwerty',
                $emptyOrder = $this->getMockBuilder('Magento\Sales\Model\Order')
                    ->disableOriginalConstructor()
                    ->getMock(),
                false
            ],
            // #4: Facebook ID is set, order is a model and it has an increment ID
            [
                'qwerty',
                $validOrder = $this->getMockBuilder('Magento\Sales\Model\Order')
                    ->disableOriginalConstructor()
                    ->getMock(),
                true
            ]
        ];

        $emptyOrder->expects($this->once())->method('getIncrementId')->willReturn(null);
        $validOrder->expects($this->once())->method('getIncrementId')->willReturn(10000001);

        return $data;
    }

    /**
     * The product script should show when there is a Facebook ID set and a product in the registry
     *
     * @param string|null $facebookId
     * @param bool $product
     * @param bool $expected
     * @dataProvider productScriptProvider
     */
    public function testGetShowProductScript($facebookId, $product, $expected)
    {
        $this->connect->expects($this->once())->method('getFacebookId')->willReturn($facebookId);
        $this->registry
            ->expects($facebookId ? $this->once() : $this->never())
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);

        $this->assertSame($expected, $this->block->getShowProductScript());
    }

    /**
     * @return array[]
     */
    public function productScriptProvider()
    {
        return [
            [null, false, false],
            ['qwerty', false, false],
            ['qwerty', true, true]
        ];
    }
}
