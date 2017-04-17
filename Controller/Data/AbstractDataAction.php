<?php

namespace AltoLabs\Snappic\Controller\Data;

class Add extends \Magento\Framework\App\Action\Action
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

    protected function renderUnauthorized()
    {
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 401, true)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setBody('Unauthorized');
    }

    /**
     * @return int
     */
    protected function getPage()
    {
        $page = (int) $this->getRequest()->getParam('page');
        return $page == null ? 1 : $page;
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        $perPage = (int) $this->getRequest()->getParam('per_page');
        return empty($perPage) ? 50 : $perPage;
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
