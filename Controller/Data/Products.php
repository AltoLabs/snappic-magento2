<?php

namespace AltoLabs\Snappic\Controller\Data;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class Products extends AbstractDataAction
{
    public function execute()
    {
        if (!$this->verifyToken()) {
            return $this->renderUnauthorized();
        }

        $products = $this->productCollection
            ->addAttributeToSelect('*')
            ->addFieldToFilter('visibility', Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', ['eq' => Status::STATUS_ENABLED])
            ->setOrder('entity_id', 'desc')
            ->setCurPage($this->getPage())
            ->setPageSize($this->getPerPage());

        $data = [];
        if ($products->getSize() > 0) {
            foreach ($products as $product) {
                $product = $this->productRepository->getById($product['entity_id']);
                $data[] = $this->snappicHelper->getSendableProductData($product);
            }
        }

        return $this->jsonFactory->create()->setData($data);
    }
}
