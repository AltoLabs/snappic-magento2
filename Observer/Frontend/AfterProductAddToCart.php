<?php

namespace AltoLabs\Snappic\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class AfterProductAddToCart implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Magento\Checkout\Model\Session          $checkoutSession
     * @param \Magento\Framework\App\RequestInterface  $request
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->productRepository = $productRepository;
    }

    /**
     * Adds a record of the product that was just added to cart to a custom session variable for us to track
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $this->productRepository->getById($this->request->getParam('product', 0));

        if ($product->getId()) {
            $this->checkoutSession->setCartProductJustAdded(
                new \Magento\Framework\DataObject([
                    'id'            => $product->getId(),
                    'qty'           => $this->request->getParam('qty', 1),
                    'name'          => $product->getName(),
                    'price'         => $product->getPrice(),
                    'category_name' => '' // We can't determine this?
                ])
            );
        }
    }
}
