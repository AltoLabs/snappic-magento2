<?php

namespace AltoLabs\Snappic\Model;

class Connect extends \Magento\Framework\Model\AbstractModel
{
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
        $this->clientFactory = $clientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->writerInterface = $writerInterface;

        parent::__construct($context, $registry);
    }

    /**
     * Send a notification to the Snappic API
     *
     * @param  string $topic
     * @return bool Whether successful
     */
    public function notifySnappicApi($topic)
    {
        $this->dataHelper->log('Snappic: notifySnappicApi ' . $this->dataHelper->getApiHost() . '/magento/webhooks');

        $client = new \Magento\Framework\HTTP\ZendClient($this->dataHelper->getApiHost() . '/magento/webhooks');
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
        } catch (Exception $e) {
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
        $this->dataHelper->log('Snappic: getSnappicStore');

        if ($this->get('snappicStore')) {
            return $this->get('snappicStore');
        }

        $domain = $this->dataHelper->getDomain();
        $client = new \Magento\Framework\HTTP\ZendClient(
            $this->dataHelper->getApiHost() . '/stores/current?domain=' . $domain
        );
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $body = $client->request()->getBody();
            $snappicStore = $this->jsonHelper->jsonDecode($body);
            $this->setData('snappicStore', $snappicStore);
            return $snappicStore;
        } catch (Exception $e) {
            $this->dataHelper->log($e->getMessage());
        }
    }

    /**
     * Returns the Facebook pixel ID
     *
     * @return string
     */
    public function getFacebookId()
    {
        $configPath = $this->dataHelper->getConfigPath('facebook/pixel_id');
        $facebookId = (string) $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($facebookId)) {
            $this->dataHelper->log('Trying to fetch Facebook ID from Snappic API...');
            $facebookId = $this->getSnappicStore()->facebook_pixel_id;

            if (!empty($facebookId)) {
                $this->dataHelper->log('Got facebook ID from API: ' . $facebookId);
                $this->writerInterface->save($configPath, $facebookId);
            }
        }
        return $facebookId;
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
