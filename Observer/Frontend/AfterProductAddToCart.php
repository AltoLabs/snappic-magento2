<?php

namespace AltoLabs\Snappic\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class AfterProductAddToCart implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('product', 0));
        // if ($product->getId()) {
        //     Mage::getSingleton('core/session')->setCartProductJustAdded(
        //         new Varien_Object(array(
        //             'id'            => $product->getId(),
        //             'qty'           => Mage::app()->getRequest()->getParam('qty', 1),
        //             'name'          => $product->getName(),
        //             'price'         => $product->getPrice(),
        //             'category_name' => Mage::getModel('catalog/category')->load($categories[0])->getName(),
        //         ))
        //     );
        // }
        // return $this;
    }
}
