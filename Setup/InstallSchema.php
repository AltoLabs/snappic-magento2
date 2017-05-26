<?php

namespace AltoLabs\Snappic\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
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

        $this->objectManager->get('Magento\Framework\App\State')->setAreaCode('adminhtml');

        $helper = $this->objectManager->get('AltoLabs\Snappic\Helper\Data');

        $this->objectManager
            ->get('AltoLabs\Snappic\Model\Connect')
            ->setSendable([
                'token' => $helper->getToken(),
                'secret' => $helper->getSecret()
            ])
            ->notifySnappicApi('app/installed');

        $setup->endSetup();
    }
}
