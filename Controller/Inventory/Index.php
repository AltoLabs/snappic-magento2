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
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \AltoLabs\Snappic\Helper\Data                    $snappicHelper
     * @param \Magento\Framework\Json\Helper\Data              $jsonHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
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
        try {
            $payload = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        } catch (\Zend_Json_Exception $e) {
            return $this->jsonFactory->create()->setData([
                'error' => 'The request was not valid JSON: ' . $e->getMessage(),
                'quantities' => 0
            ]);
        }

        if (empty($payload['ids'])) {
            return $this->jsonFactory->create()->setData([
                'error' => 'No product IDs passed.',
                'quantities' => 0
            ]);
        }

        $ids = $payload['ids'];
        $quantities = [];
        foreach ($ids as $id) {
            try {
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->productRepository->getById($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // Note: should we just return zero here when the product doesn't exist?
                return $this->jsonFactory->create()->setData([
                    'error' => 'Product #' . $id . ' does not exist.',
                    'quantities' => 0
                ]);
            }
            $quantities[$id] = $this->snappicHelper->getProductStock($product);
        }

        return $this->jsonFactory->create()->setData([
            'status' => 'success',
            'quantities' => $quantities
        ]);
    }
}
