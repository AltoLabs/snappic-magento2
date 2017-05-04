<?php

namespace AltoLabs\Snappic\Observer;

abstract class AbstractObserver
{
    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    /**
     * @var \AltoLabs\Snappic\Model\Connect
     */
    protected $connect;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \AltoLabs\Snappic\Helper\Data   $helper
     * @param \AltoLabs\Snappic\Model\Connect $connect
     */
    public function __construct(
        \AltoLabs\Snappic\Helper\Data $helper,
        \AltoLabs\Snappic\Model\Connect $connect,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->connect = $connect;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    abstract public function execute(\Magento\Framework\Event\Observer $observer);

    /**
     * @param  array $productIds
     * @return $this
     */
    protected function handleProductChanges(array $productIds = [])
    {
        $data = [];
        foreach ($productIds as $productId) {
            $product = $this->productRepository->getById($productId);
            // Product is configurable, send it directly.
            if ($this->helper->isConfigurable($product)) {
                // If the product gets disabled, directly delete it.
                if ((int)$product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                    $this->connect
                        ->setSendable([$this->helper->getSendableProductData($product)])
                        ->notifySnappicApi('products/delete');
                } else {
                    // Schedule an update for this product.
                    $data[] = $this->helper->getSendableProductData($product);
                }
            } else {
                // Product is simple. It might be part of a configurable or not...
                $parentIds = $this->configurable->getParentIdsByChild($productId);
                // No parent IDs, product can be sent directly.
                if (count($parentIds) == 0) {
                    // If the product gets disabled, directly delete it.
                    if ((int)$product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                        $this->connect
                            ->setSendable([$this->helper->getSendableProductData($product)])
                            ->notifySnappicApi('products/delete');
                    } else {
                        // Schedule an update for this product.
                        $data[] = $this->helper->getSendableProductData($product);
                    }
                } else {
                    // Got parent IDs, send them instead.
                    foreach ($parentIds as $parentId) {
                        $parent = $this->productRepository->getById($parentId, true);
                        // Save the parent to force the updated_at column to have changed.
                        $updatedParent = $this->productRepository->save($parent);
                        $data[] = $this->helper->getSendableProductData($updatedParent);
                    }
                }
            }
        }
        if (count($data) != 0) {
            $this->connect
                ->setSendable($data)
                ->notifySnappicApi('products/update');
        }
    }
}
