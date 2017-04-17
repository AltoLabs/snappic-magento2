<?php

namespace AltoLabs\Snappic\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
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
     * @param \AltoLabs\Snappic\Helper\Data $helper
     * @param \AltoLabs\Snappic\Model\Connect $connect
     */
    public function __construct(
        \AltoLabs\Snappic\Helper\Data $helper,
        \AltoLabs\Snappic\Model\Connect $connect
    ) {
        $this->helper = $helper;
        $this->connect = $connect;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $this->connect
            ->setSendable([
                'token' => $this->helper->getToken(),
                'secret' => $this->helper->getSecret()
            ])
            ->notifySnappicApi('application/installed');

        $setup->endSetup();
    }
}
