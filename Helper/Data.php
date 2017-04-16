<?php

namespace AltoLabs\Snappic\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $configReader;
    protected $a;

    /**
     * Default API values and system configuration paths
     *
     * @var string
     */
    const CONFIG_PREFIX = 'snappic/';
    const API_HOST_DEFAULT = 'https://api.snappic.io';
    const STORE_ASSETS_HOST_DEFAULT = 'https://store.snappic.io';
    const SNAPPIC_ADMIN_URL_DEFAULT = 'https://www.snappic.io';

    /**
     * @param \Magento\Framework\App\Helper\Context          $context
     * @param \Magento\Framework\App\DeploymentConfig\Reader $configReader
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\DeploymentConfig\Reader $configReader
    ) {
        $this->configReader = $configReader;
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
        $config = $this->configReader->load();
        var_dump($config);
        $path = 'admin';
        if (!empty($config['backend']['frontName'])) {
            $path = $config['backend']['frontName'];
        }
        return (string) $path;
    }

    public function getToken()
    {
        return $this->generateTokenAndSecret('token');
    }

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
        // $ret = Mage::getStoreConfig($this->getConfigPath('security/'.$what));
        // if (!empty($ret)) {
        //     return $ret;
        // }
        // $token = Mage::helper('oauth')->generateToken();
        // $secret = Mage::helper('oauth')->generateTokenSecret();
        // Mage::app()->getConfig()->saveConfig($this->getConfigPath('security/token'), $token);
        // Mage::app()->getConfig()->saveConfig($this->getConfigPath('security/secret'), $secret);
        // Mage::app()->getConfig()->reinit();
        // $data = array('token' => $token, 'secret' => $secret);
        // return $data[$what];
    }

    /**
     * Returns the given product's stock level
     *
     * @param  Product $product
     * @return int Stock level
     */
    public function getProductStock($product)
    {
        // Product is simple...
        // if (!$product->isConfigurable()) {
        //     $productId = $product->getId();
        //     // If *any* of the parent isn't in stock, we consider this product isn't.
        //     $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productId);
        //     if (count($parentIds) != 0) {
        //         foreach ($parentIds as $parentId) {
        //             $parent = Mage::getModel('catalog/product')->load($parentId);
        //             try {
        //                 $stockItem = $this->getProductStockItem($parent);
        //                 if ($stockItem->getManageStock() && !$stockItem->getIsInStock()) {
        //                     return 0;
        //                 }
        //             } catch (Exception $e) {
        //                 continue;
        //             }
        //         }
        //     }
        // }
        // try {
        //     $stockItem = $this->getProductStockItem($product);
        //     if ($stockItem->getManageStock()) {
        //         if ($stockItem->getIsInStock()) {
        //             return (int)$stockItem->getQty();
        //         } else {
        //             return 0;
        //         }
        //     } else {
        //         return 99;
        //     }
        // } catch (Exception $e) {
        //     return 99;
        // }
    }

    protected function getProductStockItem($product)
    {
        // return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
    }

    public function getSendableOrderData($order)
    {
        // $session = Mage::getSingleton('core/session');
        // return array(
        //     'id'                      => $order->getId(),
        //     'number'                  => $order->getId(),
        //     'order_number'            => $order->getId(),
        //     'email'                   => $order->getCustomerEmail(),
        //     'contact_email'           => $order->getCustomerEmail(),
        //     'total_price'             => $order->getTotalDue(),
        //     'total_price_usd'         => $order->getTotalDue(),
        //     'total_tax'               => '0.00',
        //     'taxes_included'          => true,
        //     'subtotal_price'          => $order->getTotalDue(),
        //     'total_line_items_price'  => $order->getTotalDue(),
        //     'total_discounts'         => '0.00',
        //     'currency'                => $order->getBaseCurrencyCode(),
        //     'financial_status'        => 'paid',
        //     'confirmed'               => true,
        //     'landing_site'            => $session->getLandingPage(),
        //     'referring_site'          => $session->getLandingPage(),
        //     'billing_address'         => array(
        //         'first_name'              => $order->getCustomerFirstname(),
        //         'last_name'               => $order->getCustomerLastname(),
        //     )
        // );
    }

    public function getSendableProductData($product)
    {
        // return array(
        //     'id'                  => $product->getId(),
        //     'title'               => $product->getName(),
        //     'body_html'           => $product->getDescription(),
        //     'sku'                 => $product->getSku(),
        //     'price'               => $product->getPrice(),
        //     'inventory_quantity'  => $this->getProductStock($product),
        //     'handle'              => $product->getUrlKey(),
        //     'variants'            => $this->getSendableVariantsData($product),
        //     'images'              => $this->getSendableImagesData($product),
        //     'options'             => $this->getSendableOptionsData($product),
        //     'updated_at'          => $product->getUpdatedAt(),
        //     'published_at'        => $product->getUpdatedAt()
        // );
    }

    public function getSendableVariantsData($product)
    {
        // if (!$product->isConfigurable()) {
        //     return array();
        // }
        // $sendable = array();
        // $subProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
        // foreach ($subProducts as $subProduct) {
        //     // Assign store and load sub product.
        //     $subProduct->setStoreId($product->getStoreId())
        //          ->load($subProduct->getId());
        //     // Variant is disabled, consider that it's deleted and just don't add it.
        //     if ((int)$subProduct->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
        //         continue;
        //     }
        //     // Add variant data to array.
        //     $sendable[] = array(
        //         'id'                  => $subProduct->getId(),
        //         'title'               => $subProduct->getName(),
        //         'sku'                 => $subProduct->getSku(),
        //         'price'               => $subProduct->getPrice(),
        //         'inventory_quantity'  => $this->getProductStock($subProduct),
        //         'updated_at'          => $subProduct->getUpdatedAt()
        //     );
        // }
        // return $sendable;
    }

    public function getSendableImagesData($product)
    {
        // $images = $product->getMediaGalleryImages();
        // $imagesData = array();
        // foreach ($images as $image) {
        //     $imagesData[] = array(
        //         'id'          => $image->getId(),
        //         'src'         => $image->getUrl(),
        //         'position'    => $image->getPosition(),
        //         'updated_at'  => $product->getUpdatedAt()
        //     );
        // }
        // return $imagesData;
    }

    public function getSendableOptionsData($product)
    {
        // $options = $product->getProductOptionsCollection();
        // $sendable = array();
        // foreach ($options as $option) {
        //     $optionValues = array();
        //     foreach ($option->getValuesCollection() as $optionValue) {
        //         $optionValues[] = (string) $optionValue->getTitle();
        //     }
        //     $sendable[] = array(
        //         'id'        => $option->getId(),
        //         'name'      => $option->getTitle(),
        //         'position'  => $option->getSortOrder(),
        //         'values'    => $optionValues,
        //     );
        // }
        // return $sendable;
    }

    public function getDomain()
    {
        // $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        // $components = parse_url($url);
        // return $components['host'];
    }
}
