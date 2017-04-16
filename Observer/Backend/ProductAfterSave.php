<?php

namespace AltoLabs\Snappic\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class ProductAfterSave implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $productId = $observer->getEvent()->getProduct()->getId();
        // $this->_handleProductsChanges(array($productId));
        // return $this;
    }
}
