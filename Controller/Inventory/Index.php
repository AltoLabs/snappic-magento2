<?php

namespace AltoLabs\Snappic\Controller\Inventory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $snappicHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \AltoLabs\Snappic\Helper\Data $snappicHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \AltoLabs\Snappic\Helper\Data $snappicHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->snappicHelper = $snappicHelper;
        $this->jsonHelper = $jsonHelper;
        $this->productRepository = $productRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $payload = $this->jsonHelper->jsonDecode($this->_request()->getRawBody());
        $ids = $payload['ids'];
        $quantities = [];
        foreach ($ids as $id) {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $product = $this->productRepository->getById($id);
            $quantities[$id] = $this->snappicHelper->getProductStock($product);
        }
        return $this->jsonFactory->create([
            'status' => 'success',
            'quantities' => $quantities
        ]);
    }
}
