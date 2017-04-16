<?php

namespace AltoLabs\Snappic\Observer;

use Magento\Framework\Event\ObserverInterface;

class AfterOrderPlace implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $order = $observer->getEvent()->getOrder();
        // $sendable = $this->getHelper()->getSendableOrderData($order);
        // $this->getConnect()
        //      ->setSendable($sendable)
        //      ->notifySnappicApi('orders/paid');
        // return $this;
    }
}
