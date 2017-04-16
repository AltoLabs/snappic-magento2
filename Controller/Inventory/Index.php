<?php

namespace AltoLabs\Snappic\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        die('TBC');
        // $core = Mage::helper('core');
        // $helper = Mage::helper('altolabs_snappic');
        // $payload = $core->jsonDecode($this->getRequest()->getRawBody());
        // $ids = $payload['ids'];
        // $quantities = array();
        // foreach ($ids as $id) {
        //     $product = Mage::getModel('catalog/product')->load($id);
        //     $quantities[$id] = $helper->getProductStock($product);
        // }
        // $this->getResponse()->setHeader('Content-type', 'application/json');
        // $this->getResponse()->setBody(json_encode(array('status' => 'success', 'quantities' => $quantities)));
    }
}
