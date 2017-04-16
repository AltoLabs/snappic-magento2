<?php

namespace AltoLabs\Snappic\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $session = Mage::getSingleton('core/session');
        // $url = Mage::helper('core/url')->getCurrentUrl();
        // $landingPage = $session->getLandingPage();
        // if (!$landingPage || strpos($url, '/shopinsta') !== false) {
        //     $session->setLandingPage($url);
        // }
        // return $this;
    }
}
