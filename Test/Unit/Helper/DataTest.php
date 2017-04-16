<?php

namespace AltoLabs\Snappic\Test\Unit\Helper;

use AltoLabs\Snappic\Helper\Data;

/**
 * @coversDefaultClass \AltoLabs\Snappic\Helper\Data
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Oauth\Helper\Oauth
     */
    protected $oauthHelper;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionManager;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->oauthHelper = $this->getMockBuilder('Magento\Framework\Oauth\Helper\Oauth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->writerInterface = $this->getMockBuilder('Magento\Framework\App\Config\Storage\WriterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sessionManager = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(['getLandingPage'])
            ->getMock();

        $this->logger = $this->getMockBuilder('Magento\Framework\Logger\Monolog')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = $this->objectManager->getObject(Data::class, [
            'writerInterface' => $this->writerInterface,
            'oauthHelper' => $this->oauthHelper,
            'sessionManager' => $this->sessionManager,
            'logger' => $this->logger
        ]);
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

    /**
     * Test that the token and secret can be generated and will be saved to the configuration store
     */
    public function testGenerateAndGetTokenAndSecret()
    {
        $this->oauthHelper->expects($this->exactly(2))->method('generateToken')->willReturn('footoken');
        $this->oauthHelper->expects($this->exactly(2))->method('generateTokenSecret')->willReturn('foosecret');
        $this->writerInterface->expects($this->exactly(4))->method('save');

        $this->assertSame('footoken', $this->helper->getToken());
        $this->assertSame('foosecret', $this->helper->getSecret());
    }

    /**
     * Test that the "sendable" data payload can be assembled correctly
     */
    public function testGetSendableOrderData()
    {
        $this->sessionManager->expects($this->exactly(2))->method('getLandingPage')->willReturn('xyz123');

        $order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $order->expects($this->exactly(3))->method('getId')->willReturn(123);
        $order->expects($this->exactly(2))->method('getCustomerEmail')->willReturn('foo@example.com');
        $order->expects($this->exactly(4))->method('getTotalDue')->willReturn('100.00');
        $order->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $order->expects($this->once())->method('getCustomerFirstname')->willReturn('Foo');
        $order->expects($this->once())->method('getCustomerLastname')->willReturn('Example');

        $expected = [
            'id'                      => 123,
            'number'                  => 123,
            'order_number'            => 123,
            'email'                   => 'foo@example.com',
            'contact_email'           => 'foo@example.com',
            'total_price'             => '100.00',
            'total_price_usd'         => '100.00',
            'total_tax'               => '0.00',
            'taxes_included'          => true,
            'subtotal_price'          => '100.00',
            'total_line_items_price'  => '100.00',
            'total_discounts'         => '0.00',
            'currency'                => 'USD',
            'financial_status'        => 'paid',
            'confirmed'               => true,
            'landing_site'            => 'xyz123',
            'referring_site'          => 'xyz123',
            'billing_address'         => [
                'first_name' => 'Foo',
                'last_name'  => 'Example'
            ]
        ];

        $result = $this->helper->getSendableOrderData($order);
        $this->assertSame($expected, $result);
    }

    /**
     * Ensure that the log method calls the logger
     */
    public function testLog()
    {
        $this->logger->expects($this->once())->method('addDebug');

        $this->helper->log('Test');
    }
}
