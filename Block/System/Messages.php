<?php

namespace AltoLabs\Snappic\Block\System;

class Messages extends \Magento\Backend\Block\Template
{
    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfigInterface
     */
    protected $reinitableConfigInterface;

    /**
     * @param \Magento\Backend\Block\Template\Context                 $context
     * @param \AltoLabs\Snappic\Helper\Data                           $helper
     * @param \Magento\Framework\App\Config\Storage\WriterInterface   $writerInterface
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfigInterface
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \AltoLabs\Snappic\Helper\Data $helper,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfigInterface,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->writerInterface = $writerInterface;
        $this->reinitableConfigInterface = $reinitableConfigInterface;

        // Don't cache this block
        $this->setData('cache_lifetime', null);

        parent::__construct($context);
    }

    /**
     * Should the Snappic message been displayed?
     *
     * @return bool
     */
    public function getShowAdminMessage()
    {
        if ($this->getIsDisplayed()) {
            return false;
        }
        $this->setIsDisplayed(true);
        return true;
    }

    /**
     * Has the Snappic message been displayed already?
     *
     * @return bool
     */
    public function getIsDisplayed()
    {
        return $this->_scopeConfig->getValue($this->helper->getConfigPath('system/completion_message')) === 'displayed';
    }

    /**
     * Set (and save to config) whether the Snappic message has been displayed
     *
     * @param boolean $displayed
     * @return $this
     */
    public function setIsDisplayed($displayed = true)
    {
        $this->writerInterface->save($this->helper->getConfigPath('system/completion_message'), 'displayed');
        // Reset the cached store config for the next load
        $this->reinitableConfigInterface->reinit();

        return $this;
    }

    /**
     * Get the Snappic link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->helper->getSnappicAdminUrl()
            . '/?login&pricing&provider=magento&domain=' . urlencode($this->helper->getDomain())
            . '&access_token=' . urlencode($this->helper->getToken() . ':' . $this->helper->getSecret());
    }
}
