<?php

namespace AltoLabs\Snappic\Observer;

use Magento\Framework\Event\ObserverInterface;

class AfterOrderPlace extends AbstractObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $sendable = $this->helper->getSendableOrderData($order);
        $this->connect->setSendable($sendable)->notifySnappicApi('orders/paid');
    }
}
