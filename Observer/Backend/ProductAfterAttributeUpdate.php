<?php

namespace AltoLabs\Snappic\Observer\Backend;

use AltoLabs\Snappic\Observer\AbstractObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductAfterSave extends AbstractObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->handleProductChanges((array) $observer->getEvent()->getProductIds());
    }
}
