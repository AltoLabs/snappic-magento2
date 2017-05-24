<?php

namespace AltoLabs\Snappic\Model\ResourceModel\Stock;

use Magento\CatalogInventory\Model\ResourceModel\Stock;

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
     * The stock item correctItemsQty method is not observable at the moment, so using a plugin to dispatch a
     * custom event
     *
     * https://github.com/magento/magento2/issues/4857
     *
     * @param Stock $item
     * @param callable $proceed
     * @param array $items
     * @param int $websiteId
     * @param string $operator
     */
    public function aroundCorrectItemsQty(Stock $subject, callable $proceed, array $items, $websiteId, $operator)
    {
        $proceed($items, $websiteId, $operator);

        if (empty($items)) {
            return;
        }

        $this->eventManager->dispatch(
            'altolabs_catalog_product_stock_save_after',
            ['product_ids' => array_keys($items)]
        );
    }
}
