<?php

namespace AltoLabs\Snappic\Test\Unit\Model;

class ConnectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AltoLabs\Snappic\Model\Connect
     */
    protected $model;

    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->dataHelper = $this->getMockBuilder('AltoLabs\Snappic\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->jsonHelper = $this->getMockBuilder('Magento\Framework\Json\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject('AltoLabs\Snappic\Model\Connect', [
            'dataHelper' => $this->dataHelper,
            'jsonHelper' => $this->jsonHelper
        ]);
    }

    /**
     * Ensure that "sendable" data can be set and retrieved
     */
    public function testGetAndSetSendable()
    {
        $testData = ['foo' => 'bar'];

        // Ensure a blank slate to start with
        $this->assertNull($this->model->getSendable());

        // Ensure it returns itself
        $this->assertSame($this->model, $this->model->setSendable($testData));

        // Ensure it returns the sendable data
        $this->assertSame($testData, $this->model->getSendable());
    }

    /**
     * Test that data can be "sealed" for transport
     */
    public function testSeal()
    {
        $this->jsonHelper->expects($this->once())
            ->method('jsonEncode');
        $this->model->seal(['foo', 'bar', 'baz']);
    }

    /**
     * Test that data can be signed with the secret key
     */
    public function testSignPayload()
    {
        $this->dataHelper->expects($this->once())
            ->method('getSecret')
            ->willReturn('opensesame');

        $this->assertSame('8ac9e061fc5046aa90ac8fc2a2fd4ae8', $this->model->signPayload('foobar'));
    }
}
