<?php

namespace AltoLabs\Snappic\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Url
     */
    protected $url;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $url
    ) {
        $this->customerSession = $customerSession;
        $this->url = $url;
    }

    /**
     * If the requested URL is "shopinsta", save the URL to the session for tracking
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $url = $this->url->getCurrentUrl();
        $landingPage = $this->customerSession->getLandingPage();
        if (!$landingPage || strpos($url, '/shopinsta') !== false) {
            $this->customerSession->setLandingPage($url);
        }
    }
}
