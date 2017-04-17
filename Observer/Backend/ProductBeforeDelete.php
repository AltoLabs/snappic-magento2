<?php

namespace AltoLabs\Snappic\Observer\Backend;

use AltoLabs\Snappic\Observer\AbstractObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductBeforeDelete extends AbstractObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = [];
        $action = 'products/';
        $product = $observer->getEvent()->getProduct();

        if ($this->helper->isConfigurable($product)) {
            // Product is configurable, delete it directly.
            $action .= 'delete';
            $data[] = $this->helper->getSendableProductData($product);
        } else {
            // Product is simple, it might be part of a configurable or not...
            $productId = (int) $product->getId();
            $parentIds = $this->configurable->getParentIdsByChild($productId);

            if (count($parentIds) == 0) {
                // No parent IDs, product can be sent directly.
                $action .= 'delete';
                $data[] = $this->helper->getSendableProductData($product);
            } else {
                // Got parent IDs, send them instead.
                $action .= 'update';
                foreach ($parentIds as $parentId) {
                    $parent = $this->productRepository->getById($parentId, true);
                    // Save the parent to force the updated_at column to have changed.
                    $updatedParent = $this->productRepository->save($parent);
                    // We want to change the variants on this configurable, so it does
                    // not include the deleted child.
                    $parentData = $this->helper->getSendableProductData($updatedParent);
                    $variants = [];
                    foreach ($parentData['variants'] as $variant) {
                        if ((int)$variant['id'] == $productId) {
                            continue;
                        }
                        $variants[] = $variant;
                    }
                    $parentData['variants'] = $variants;
                    $data[] = $parentData;
                }
            }
        }

        if (count($data) != 0) {
            $this->connect->setSendable($data)->notifySnappicApi($action);
        }
    }
}
