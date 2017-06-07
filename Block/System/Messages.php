<?php

namespace AltoLabs\Snappic\Block\System;

class Messages extends \Magento\Backend\Block\Template
{
    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    /**
     * @var \AltoLabs\Snappic\Model\Connect
     */
    protected $connect;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \AltoLabs\Snappic\Helper\Data           $helper
     * @param \AltoLabs\Snappic\Model\Connect         $connect
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \AltoLabs\Snappic\Helper\Data $helper,
        \AltoLabs\Snappic\Model\Connect $connect,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->connect = $connect;

        // Don't cache this block
        $this->setData('cache_lifetime', null);

        parent::__construct($context);
    }

    /**
     * Whether to show the sandbox warning message
     *
     * @return bool
     */
    public function getShowSandboxWarning()
    {
        return (bool) $this->getHelper()->getIsSandboxed();
    }

    /**
     * Whether to show the "continue to signup" link for production use
     *
     * @return bool
     */
    public function getShowDisplayContinueSignup()
    {
        $pixel = $this->connect->getStoredFacebookPixelId();
        return empty($pixel) || $pixel == \AltoLabs\Snappic\Model\Connect::SANDBOX_PIXEL_ID;
    }

    /**
     * Return the Snappic helper
     *
     * @return \AltoLabs\Snappic\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Get the Snappic link
     *
     * @return string
     */
    public function getLink()
    {
        $helper = $this->getHelper();
        return $helper->getSnappicAdminUrl()
            . '/?login&pricing&provider=magento&domain=' . urlencode($helper->getDomain())
            . '&access_token=' . urlencode($helper->getToken() . ':' . $helper->getSecret());
    }
}
