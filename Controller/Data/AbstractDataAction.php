<?php

namespace AltoLabs\Snappic\Controller\Data;

abstract class AbstractDataAction extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \AltoLabs\Snappic\Helper\Data $snappicHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \AltoLabs\Snappic\Helper\Data $snappicHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
    ) {
        $this->jsonFactory = $jsonResultFactory;
        $this->snappicHelper = $snappicHelper;
        $this->productRepository = $productRepository;
        $this->productCollection = $productCollection;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    abstract public function execute();

    /**
     * Checks the given request "token" against that stored in the DB
     *
     * @return bool
     */
    protected function verifyToken()
    {
        return $this->snappicHelper->getToken() == $this->getRequest()->getParam('token');
    }

    /**
     * Return an "unauthorised" message for protected endpoints with a 401 header
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function renderUnauthorized()
    {
        $this->getResponse()->setHttpResponseCode(401);

        return $this->jsonFactory->create()->setData([
            'error' => true,
            'message' => 'Unauthorized'
        ]);
    }

    /**
     * Get the current collection page number
     *
     * @return int
     */
    protected function getPage()
    {
        return (int) $this->getRequest()->getParam('page', 1);
    }

    /**
     * Get the current pagination limit
     *
     * @return int
     */
    protected function getPerPage()
    {
        return (int) $this->getRequest()->getParam('per_page', 50);
    }

    /**
     * @param  \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    protected function getIanaTimezone(\Magento\Store\Api\Data\StoreInterface $store)
    {
        return $this->snappicHelper->getCurrentStore()->getConfig('general/locale/timezone');
    }

    /**
     * @param  \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    protected function getMoneyWithCurrencyFormat(\Magento\Store\Api\Data\StoreInterface $store)
    {
        $localeCode = $this->snappicHelper->getCurrentStore()->getConfig('general/locale/code');

        $currency = new \Zend_Currency(null, $localeCode);
        $currency->setLocale($localeCode);

        $formatted = $currency->toCurrency(0.50);
        $unformatted = $currency->toCurrency(0.50, ['display' => \Zend_Currency::NO_SYMBOL]);

        return str_replace($unformatted, '{{amount}}', $formatted);
    }
}
