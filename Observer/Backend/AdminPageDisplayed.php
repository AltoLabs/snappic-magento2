<?php

namespace AltoLabs\Snappic\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class AdminPageDisplayed implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//         if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
//             return;
//         }

//         $helper = $this->getHelper();
//         $flagPath = $helper->getConfigPath('system/completion_message');
//         $flag = Mage::getStoreConfig($flagPath);
//         if ($flag == 'displayed') {
//             return;
//         }

//         Mage::app()->getConfig()->saveConfig($flagPath, 'displayed');
//         Mage::app()->getConfig()->reinit();
//         $domain = $helper->getDomain();
//         $token = $helper->getToken();
//         $secret = $helper->getSecret();
//         $link = $helper->getSnappicAdminUrl() . '/?login&pricing&provider=magento&domain='
//             . urlencode($domain) . '&access_token=' . urlencode($token . ':' . $secret);
//         $html = <<<HTML
// <img src="http://snappic.io/static/img/general/logo.svg" style="padding:10px;background-color:#E85B52;">
// <div style="font-size:16px;font-weight:400;letter-spacing:1.2px;line-height: 1.2;border:0;padding:0;margin:24px 4px">Almost done!</div>
// <script>window.Snappic={};window.Snappic.signup=function(){window.location='$link';};</script>
// <img src="http://store.snappic.io/images/magento_continue_signup.png" style="width:100%;max-width:460px;cursor:pointer;" onclick="Snappic.signup()">
// HTML;
//         Mage::getSingleton('adminhtml/session')->addSuccess($html);
//         return $this;
    }
}
