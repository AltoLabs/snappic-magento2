<?php

namespace AltoLabs\Snappic\Model;

class Connect extends \Magento\Framework\Model\AbstractModel
{
    const STORE_DEFAULTS = array('facebook_pixel_id' => null);

    /**
     * The sandbox Facebook pixel ID
     *
     * @var string
     */
    const SANDBOX_PIXEL_ID = '123123123';

    /**
     * The sendable data
     *
     * @var array
     */
    protected $sendablePayload;

    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Json\Helper
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @param \Magento\Framework\Model\Context                      $context
     * @param \Magento\Framework\Registry                           $registry
     * @param \AltoLabs\Snappic\Helper\Data                         $dataHelper
     * @param \Magento\Framework\Json\Helper\Data                   $jsonHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \AltoLabs\Snappic\Helper\Data $dataHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface
    ) {
        $this->dataHelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
        $this->scopeConfig = $scopeConfig;
        $this->writerInterface = $writerInterface;

        parent::__construct($context, $registry);
    }

    /**
     * Send a notification to the Snappic API
     *
     * @param  string $topic
     * @param  bool $bypassSandbox
     * @return bool Whether successful
     */
    public function notifySnappicApi($topic, $bypassSandbox = false)
    {
        $host = $this->dataHelper->getApiHost($bypassSandbox);
        $this->dataHelper->log('Snappic: notifySnappicApi ' . $host . '/magento/webhooks');

        $client = new \Zend_Http_Client($host . '/magento/webhooks');
        $client->setMethod(\Zend_Http_Client::POST);
        $sendable = $this->seal($this->getSendable());
        $client->setRawData($sendable);
        $headers = [
            'Content-type'                => 'application/json',
            'X-Magento-Shop-Domain'       => $this->dataHelper->getDomain(),
            'X-Magento-Topic'             => $topic,
            'X-Magento-Webhook-Signature' => $this->signPayload($sendable),
        ];
        $client->setHeaders($headers);

        try {
            $response = $client->request();
            if (!$response->isSuccessful()) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Gets the current store information from Snappic and stores it as a class property
     *
     * @return object|null
     */
    public function getSnappicStore()
    {
        $domain = $this->dataHelper->getDomain();
        $client = new \Zend_Http_Client($this->dataHelper->getApiHost() . '/stores/current?domain=' . $domain);
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $body = (string) $client->request()->getBody();
            return array_merge(self::STORE_DEFAULTS, $this->jsonHelper->jsonDecode($body));
        } catch (\Exception $e) {
            $this->dataHelper->log('Failed retrieving Snappic Store: ' . $e->getMessage());
            return self::STORE_DEFAULTS;
        }
    }

    /**
     * Returns the Facebook pixel ID, factoring in sandbox mode
     *
     * @param bool $fetchWhenNone
     * @return string
     */
    public function getFacebookId($fetchWhenNone)
    {
        $fbId = $this->getStoredFacebookPixelId();
        if (!$fetchWhenNone) {
            return $fbId;
        }

        if (empty($fbId) || ($fbId == self::SANDBOX_PIXEL_ID && $this->dataHelper->getIsProduction())) {
            $this->dataHelper->log('Trying to fetch Facebook ID from Snappic API...');
            $snappicStore = $this->getSnappicStore();
            $configPath = $this->dataHelper->getConfigPath('facebook/pixel_id');
            $fbId = $snappicStore['facebook_pixel_id'];
            if (!empty($fbId)) {
                $fbId = $snappicStore['facebook_pixel_id'];
                $this->dataHelper->log('Got facebook ID from API: ' . $fbId);
                $this->writerInterface->save($configPath, $fbId);
            }
        }
        return $fbId;
    }

    /**
     * Returns the Facebook pixel ID from system configuration
     *
     * @return string
     */
    public function getStoredFacebookPixelId()
    {
        $configPath = $this->dataHelper->getConfigPath('facebook/pixel_id');
        return (string) $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Set the sendable data
     *
     * @param array $sendable
     * @return $this
     */
    public function setSendable($sendable)
    {
        $this->sendablePayload = $sendable;
        return $this;
    }

    /**
     * Get the sendable data
     *
     * @return array
     */
    public function getSendable()
    {
        return $this->sendablePayload;
    }

    /**
     * Encode the given data for transport
     *
     * @param  mixed $input
     * @return string JSON
     */
    public function seal($input)
    {
        return $this->jsonHelper->jsonEncode(['data' => $input]);
    }

    /**
     * Return a hash of the given data payload by using the configured secret key
     *
     * @param  string $data
     * @return string
     */
    public function signPayload($data)
    {
        return md5($this->dataHelper->getSecret() . $data);
    }
}
