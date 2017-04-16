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
     * @var \Magento\Framework\Json\Helper description
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry      $registry
     * @param \AltoLabs\Snappic\Helper\Data    $dataHelper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \AltoLabs\Snappic\Helper\Data $dataHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;

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
        // $helper = $this->getHelper();
        // Mage::log('Snappic: notifySnappicApi ' . $helper->getApiHost() . '/magento/webhooks', null, 'snappic.log');
        // $client = new Zend_Http_Client($helper->getApiHost() . '/magento/webhooks');
        // $client->setMethod(Zend_Http_Client::POST);
        // $sendable = $this->seal($this->getSendable());
        // $client->setRawData($sendable);
        // $headers = array(
        // 'Content-type'                => 'application/json',
        // 'X-Magento-Shop-Domain'       => $helper->getDomain(),
        // 'X-Magento-Topic'             => $topic,
        // 'X-Magento-Webhook-Signature' => $this->signPayload($sendable),
        // );
        // $client->setHeaders($headers);
        // try {
        //     $response = $client->request();
        //     if (!$response->isSuccessful()) {
        //         return false;
        //     }
        // } catch (Exception $e) {
        //     return false;
        // }
        // return true;
    }

    /**
     * @return object|null
     */
    public function getSnappicStore()
    {
        // Mage::log('Snappic: getSnappicStore', null, 'snappic.log');
        // if ($this->get('snappicStore')) {
        //     return $this->get('snappicStore');
        // }
        // $helper = $this->getHelper();
        // $domain = $helper->getDomain();
        // $client = new Zend_Http_Client($helper->getApiHost() . '/stores/current?domain=' . $domain);
        // $client->setMethod(Zend_Http_Client::GET);
        // try {
        //     $body = $client->request()->getBody();
        //     $snappicStore = Mage::helper('core')->jsonDecode($body, Zend_Json::TYPE_OBJECT);
        //     $this->setData('snappicStore', $snappicStore);
        //     return $snappicStore;
        // } catch (Exception $e) {
        //     return null;
        // }
    }

    /**
     * Returns the Facebook pixel ID
     *
     * @return string
     */
    public function getFacebookId()
    {
        // $helper = $this->getHelper();
        // $configPath = $helper->getConfigPath('facebook/pixel_id');
        // $facebookId = (string) Mage::getStoreConfig($configPath);
        // if (empty($facebookId)) {
        //     Mage::log('Trying to fetch Facebook ID from Snappic API...', null, 'snappic.log');
        //     $facebookId = $this->getSnappicStore()->facebook_pixel_id;
        //     if (!empty($facebookId)) {
        //         Mage::log('Got facebook ID from API: ' . $facebookId, null, 'snappic.log');
        //         Mage::app()->getConfig()->saveConfig($configPath, $facebookId);
        //     }
        // }
        // return $facebookId;
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
        return md5($this->getHelper()->getSecret() . $data);
    }

    /**
     * Return the Snappic helper
     *
     * @return \AltoLabs\Snappic\Helper\Data
     */
    public function getHelper()
    {
        return $this->dataHelper;
    }
}
