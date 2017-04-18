<?php

namespace AltoLabs\Snappic\Model\Product\Action;

use Magento\Catalog\Model\Product\Action;

class Plugin
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * The "catalog_product_attribute_update_after" event was removed from M1 to M2 - this is a workaround by
     * re-triggering it with a customised name, so it won't get in the way of anything else but will still allow
     * our ProductAfterAttributeUpdate observer to work.
     *
     * @param  Action $action    Original model
     * @param  callable $proceed Original method as a closure
     * @param  array $productIds The product IDs we want for the "after" event
     * @param  array $attrData
     * @param  int $storeId
     * @return Action
     */
    public function aroundUpdateAttributes(Action $action, callable $proceed, $productIds, $attrData, $storeId)
    {
        // Call original method
        $proceed($productIds, $attrData, $storeId);

        // Trigger event with original method argument
        $this->eventManager->dispatch(
            'altolabs_catalog_product_attribute_update_after',
            ['product_ids' => $productIds]
        );

        return $action;
    }
}
