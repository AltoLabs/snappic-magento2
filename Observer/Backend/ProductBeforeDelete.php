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
        // $data = [];
        // $action = 'products/';
        // $helper = $this->getHelper();
        // $product = $observer->getEvent()->getProduct();
        // // Product is configurable, delete it directly.
        // if ($product->isConfigurable()) {
        //     $action .= 'delete';
        //     $data[] = $helper->getSendableProductData($product);
        // }
        // // Product is simple, it might be part of a configurable or not...
        // else {
        //     $productId = (int)$product->getId();
        //     $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productId);
        //     // No parent IDs, product can be sent directly.
        //     if (count($parentIds) == 0) {
        //         $action .= 'delete';
        //         $data[] = $helper->getSendableProductData($product);
        //     }
        //     // Got parent IDs, send them instead.
        //     else {
        //         $action .= 'update';
        //         foreach ($parentIds as $parentId) {
        //             $parent = Mage::getModel('catalog/product')->load($parentId);
        //             // Save the parent to force the updated_at column to have changed.
        //             $parent->save();
        //             // We want to change the variants on this configurable, so it does
        //             // not include the deleted child.
        //             $parentData = $helper->getSendableProductData($parent);
        //             $variants = [];
        //             foreach ($parentData['variants'] as $variant) {
        //                 if ((int)$variant['id'] == $productId) {
        //                     continue;
        //                 }
        //                 $variants[] = $variant;
        //             }
        //             $parentData['variants'] = $variants;
        //             $data[] = $parentData;
        //         }
        //     }
        // }
        // if (count($data) != 0) {
        //     $this->getConnect()
        //         ->setSendable($data)
        //         ->notifySnappicApi($action);
        // }
    }
}
