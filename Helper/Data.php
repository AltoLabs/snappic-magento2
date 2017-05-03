<?php

namespace AltoLabs\Snappic\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @var \Magento\Framework\Oauth\Helper\Oauth
     */
    protected $oauthHelper;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * Default API values and system configuration paths
     *
     * @var string
     */
    const CONFIG_PREFIX = 'altolabs_config/';
    const API_HOST_DEFAULT = 'https://api.snappic.io';
    const STORE_ASSETS_HOST_DEFAULT = 'https://store.snappic.io';
    const SNAPPIC_ADMIN_URL_DEFAULT = 'https://www.snappic.io';

    /**
     * @param \Magento\Framework\App\Helper\Context                 $context
     * @param \Magento\Framework\App\DeploymentConfig\Reader        $deploymentConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface
     * @param \Magento\Framework\Oauth\Helper\Oauth                 $oauthHelper
     * @param \Magento\Customer\Model\Session                       $sessionManager
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager
     * @param \Magento\Catalog\Model\ProductRepository              $productRepository
     * @param \Magento\Framework\Logger\Monolog                     $logger
     * @param \AltoLabs\Snappic\Model\Logger                        $logHandler
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        \Magento\Framework\Oauth\Helper\Oauth $oauthHelper,
        \Magento\Customer\Model\Session $sessionManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Logger\Monolog $logger,
        \AltoLabs\Snappic\Model\Logger $logHandler
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->writerInterface = $writerInterface;
        $this->oauthHelper = $oauthHelper;
        $this->sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->logger->pushHandler($logHandler);

        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getApiHost()
    {
        return $this->getEnvOrDefault('SNAPPIC_API_HOST', self::API_HOST_DEFAULT);
    }

    /**
     * @return string
     */
    public function getConfigPath($suffix)
    {
        return self::CONFIG_PREFIX . $suffix;
    }

    /**
     * @return string
     */
    public function getStoreAssetsHost()
    {
        return $this->getEnvOrDefault('SNAPPIC_STORE_ASSETS_HOST', self::STORE_ASSETS_HOST_DEFAULT);
    }

    /**
     * @return string
     */
    public function getSnappicAdminUrl()
    {
        return $this->getEnvOrDefault('SNAPPIC_ADMIN_URL', self::SNAPPIC_ADMIN_URL_DEFAULT);
    }

    /**
     * Gets the currently active Magento store model
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Write something to the Snappic log file
     *
     * @param string $message
     * @return $this
     */
    public function log($message = '')
    {
        $this->logger->addDebug($message);
        return $this;
    }

    /**
     * Return from environment variables or a default value
     *
     * @param  string $key
     * @param  string $key
     * @return string
     */
    public function getEnvOrDefault($key, $default = null)
    {
        $val = getenv($key);
        return empty($val) ? $default : $val;
    }

    /**
     * Get the URL segment that is used for the Magento admin
     *
     * @return string
     */
    public function getAdminHtmlPath()
    {
        return (string) $this->deploymentConfig
            ->get(\Magento\Backend\Setup\ConfigOptionsList::CONFIG_PATH_BACKEND_FRONTNAME) ?: 'admin';
    }

    /**
     * Returns the Snappic token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->generateTokenAndSecret('token');
    }

    /**
     * Returns the Snappic secret token
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->generateTokenAndSecret('secret');
    }

    /**
     * @param  string $what System configuration path name
     * @return array
     */
    protected function generateTokenAndSecret($what)
    {
        $ret = $this->scopeConfig->getValue(
            $this->getConfigPath('security/' . $what),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!empty($ret)) {
            return $ret;
        }

        $token = $this->oauthHelper->generateToken();
        $secret = $this->oauthHelper->generateTokenSecret();
        $this->writerInterface->save($this->getConfigPath('security/token'), $token);
        $this->writerInterface->save($this->getConfigPath('security/secret'), $secret);
        // Mage::app()->getConfig()->reinit();

        $data = [
            'token' => $token,
            'secret' => $secret
        ];

        return $data[$what];
    }

    /**
     * Returns the given product's stock level
     *
     * @param  Product $product
     * @return int Stock level
     */
    public function getProductStock(\Magento\Catalog\Model\Product $product)
    {
        // Product is simple...
        if (!$this->isConfigurable($product)) {
            $productId = $product->getId();
            // If *any* of the parent isn't in stock, we consider this product isn't.
            $parentIds = $product->getTypeInstance()->getParentIdsByChild($productId);
            if (count($parentIds) != 0) {
                foreach ($parentIds as $parentId) {
                    $parent = $this->productRepository->getById($parentId);
                    try {
                        $stockItem = $this->getProductStockItem($parent);
                        if ($stockItem->getManageStock() && !$stockItem->getIsInStock()) {
                            return 0;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }

        try {
            $stockItem = $this->getProductStockItem($product);
            if ($stockItem && $stockItem->getManageStock()) {
                if ($stockItem->getIsInStock()) {
                    return (int)$stockItem->getQty();
                } else {
                    return 0;
                }
            } else {
                return 99;
            }
        } catch (Exception $e) {
            return 99;
        }
    }

    /**
     * Check whether a product is a configurable product or not
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isConfigurable(\Magento\Catalog\Model\Product $product)
    {
        return $product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
    }

    /**
     * Get the product's stock item model
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface|null
     */
    protected function getProductStockItem(\Magento\Catalog\Model\Product $product)
    {
        return $product->getExtensionAttributes()->getStockItem();
    }

    /**
     * Return a data payload from a given order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getSendableOrderData(\Magento\Sales\Model\Order $order)
    {
        return [
            'id'                      => $order->getId(),
            'number'                  => $order->getId(),
            'order_number'            => $order->getId(),
            'email'                   => $order->getCustomerEmail(),
            'contact_email'           => $order->getCustomerEmail(),
            'total_price'             => $order->getTotalDue(),
            'total_price_usd'         => $order->getTotalDue(),
            'total_tax'               => '0.00',
            'taxes_included'          => true,
            'subtotal_price'          => $order->getTotalDue(),
            'total_line_items_price'  => $order->getTotalDue(),
            'total_discounts'         => '0.00',
            'currency'                => $order->getBaseCurrencyCode(),
            'financial_status'        => 'paid',
            'confirmed'               => true,
            'landing_site'            => $this->sessionManager->getLandingPage(),
            'referring_site'          => $this->sessionManager->getLandingPage(),
            'billing_address'         => [
                'first_name' => $order->getCustomerFirstname(),
                'last_name'  => $order->getCustomerLastname(),
            ]
        ];
    }

    /**
     * Get product data to be sent to Snappic
     *
     * @param \Magento\Catalog\Model\Product $produt
     * @return array
     */
    public function getSendableProductData(\Magento\Catalog\Model\Product $product)
    {
        return [
            'id'                  => $product->getId(),
            'title'               => $product->getName(),
            'body_html'           => $product->getDescription(),
            'sku'                 => $product->getSku(),
            'price'               => $product->getPrice(),
            'inventory_quantity'  => $this->getProductStock($product),
            'handle'              => $product->getUrlModel()->getUrl($product),
            'variants'            => $this->getSendableVariantsData($product),
            'images'              => $this->getSendableImagesData($product),
            'options'             => $this->getSendableOptionsData($product),
            'updated_at'          => $product->getUpdatedAt(),
            'published_at'        => $product->getUpdatedAt()
        ];
    }

    /**
     * Return data for the simple products under a given configurable product (variants)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getSendableVariantsData(\Magento\Catalog\Model\Product $product)
    {
        if (!$this->isConfigurable($product)) {
            return [];
        }

        $sendable = [];
        $subProducts = $product->getTypeInstance()->getUsedProducts($product);
        foreach ($subProducts as $subProduct) {
            // Assign store and load sub product.
            $subProduct->setStoreId($product->getStoreId())->load($subProduct->getId());

            // Variant is disabled, consider that it's deleted and just don't add it.
            if ((int) $subProduct->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                continue;
            }

            // Add variant data to array.
            $sendable[] = [
                'id'                  => $subProduct->getId(),
                'title'               => $subProduct->getName(),
                'sku'                 => $subProduct->getSku(),
                'price'               => $subProduct->getPrice(),
                'inventory_quantity'  => $this->getProductStock($subProduct),
                'updated_at'          => $subProduct->getUpdatedAt()
            ];
        }

        return $sendable;
    }

    /**
     * Get a list of a product's images
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getSendableImagesData(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\Framework\Data\Collection $images */
        $images = $product->getMediaGalleryImages();
        $imagesData = [];
        foreach ($images as $image) {
            /** @var \Magento\Framework\DataObject $image */
            $imagesData[] = [
                'id'         => $image->getId(),
                'src'        => $image->getUrl(),
                'position'   => $image->getPosition(),
                'updated_at' => $product->getUpdatedAt()
            ];
        }
        return $imagesData;
    }

    /**
     * Get a list of a product's product options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getSendableOptionsData(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $options */
        $options = $product->getProductOptionsCollection();
        $sendable = [];

        foreach ($options as $option) {
            $optionValues = [];
            foreach ($option->getValuesCollection() as $optionValue) {
                $optionValues[] = (string) $optionValue->getTitle();
            }

            $sendable[] = [
                'id'       => $option->getId(),
                'name'     => $option->getTitle(),
                'position' => $option->getSortOrder(),
                'values'   => $optionValues
            ];
        }

        return $sendable;
    }

    /**
     * Get the domain from the current store's URL
     *
     * @return string
     */
    public function getDomain()
    {
        $url = $this->getCurrentStore()->getBaseUrl();
        $components = parse_url($url);
        return $components['host'];
    }
}
