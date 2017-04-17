<?php

namespace AltoLabs\Snappic\Controller\Cart;

class Clear extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Framework\Json\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        $this->cart = $cart;
        $this->session = $session;
        $this->jsonFactory = $jsonResultFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->cart->truncate()->save();
        $this->session->setCartWasUpdated(true);

        return $this->jsonFactory->create([
            'status' => 'success',
            'total' => ($this->cart->getQuote()->getSubtotal() ?: '0.00')
        ]);
    }
}
