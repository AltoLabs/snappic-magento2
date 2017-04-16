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
        // $productIds = $observer->getEvent()->getProductIds();
        // $this->_handleProductsChanges($productIds);
        // return $this;
    }
}
