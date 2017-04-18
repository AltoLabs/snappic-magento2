<?php

namespace AltoLabs\Snappic\Controller\Cart;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $snappicHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Framework\Json\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \AltoLabs\Snappic\Helper\Data $snappicHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->cart = $cart;
        $this->session = $session;
        $this->jsonFactory = $jsonResultFactory;
        $this->jsonHelper = $jsonHelper;
        $this->snappicHelper = $snappicHelper;
        $this->productRepository = $productRepository;
        $this->configurable = $configurable;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $storeId = $this->snappicHelper->getCurrentStore()->getId();

        try {
            $payload = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        } catch (\Zend_Json_Exception $e) {
            return $this->jsonFactory->create()->setData([
                'error' => 'The request was not valid JSON: ' . $e->getMessage(),
                'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
            ]);
        }

        if (empty($payload['id'])) {
            $this->snappicHelper->log('Product with ID ' . $payload['id'] . ' was not found.');
            return $this->jsonFactory->create()->setData([
                'error' => 'The product was not found.',
                'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
            ]);
        }

        try {
            $product = $this->productRepository->getById($payload['id'], false, $storeId);

            if (!$product || !$product->getId()) {
                $this->snappicHelper->log('Product with ID ' . $payload['id'] . ' was not found.');
                return $this->jsonFactory->create()->setData([
                    'error' => 'The product was not found.',
                    'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
                ]);
            }

            // If product is part of configurables.
            $parentIds = $this->configurable->getParentIdsByChild($product->getId());
            if (count($parentIds) != 0) {
                foreach ($parentIds as $parentId) {
                    $parent = $this->productRepository->getById($parentId);
                    $attrOpts = $parent->getTypeInstance()->getConfigurableAttributesAsArray($parent);
                    $attrs = [];
                    foreach ($attrOpts as $attr) {
                        $vals = array_column($attr['values'], 'value_index');
                        $curVal = $product->getData($attr['attribute_code']);
                        if (in_array($curVal, $vals)) {
                            $attrs[$attr['attribute_id']] = $curVal;
                        }
                    }
                    $req = new \Magento\Framework\DataObject([
                        'product' => $parentId,
                        'qty' => 1,
                        'super_attribute' => $attrs
                    ]);
                    $this->cart->addProduct($parent, $req);
                    break;
                }
            } else {
                // No parent ID just add the product.
                $this->cart->addProduct($product, ['qty' => 1]);
            }

            if (!$this->cart->getCustomerSession()->getCustomer()->getId()
                && $this->cart->getQuote()->getCustomerId()
            ) {
                $this->cart->getQuote()->setCustomerId(null);
            }
            $this->cart->save();
            $this->session->setCartWasUpdated(true);

            return $this->jsonFactory->create()->setData([
                'status' => 'success',
                'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
            ]);
        } catch (\Exception $e) {
            return $this->jsonFactory->create()->setData([
                'error' => $e->getMessage(),
                'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
            ]);
        }
    }
}
