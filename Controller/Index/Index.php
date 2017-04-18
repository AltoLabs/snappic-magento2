<?php

namespace AltoLabs\Snappic\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \AltoLabs\Snappic\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \AltoLabs\Snappic\Helper\Data         $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \AltoLabs\Snappic\Helper\Data $helper
    ) {
        $this->helper = $helper;

        parent::__construct($context);
    }

    public function execute()
    {
        $this->getResponse()->setBody($this->indexPageHtml());
    }

    /**
     * @return string HTML
     */
    protected function indexPageHtml()
    {
        $storeAssetsHost = $this->helper->getStoreAssetsHost();
        return "
            <div style=\"width:100%;height:auto\"><snpc-main></snpc-main></div>
            <script>
            var SnappicOptions = {
                ecommerce_provider: 'magento',
                webcomponents_url: '$storeAssetsHost/bower_components/webcomponentsjs/webcomponents-lite.min.js',
                styles_url: '$storeAssetsHost/styles/main.css',
                bundle_url: '$storeAssetsHost/elements/elements.vulcanized.html',
                soapjs_url: '$storeAssetsHost/scripts/soap.min.js',
                xml2json_url: '$storeAssetsHost/scripts/xml2json.min.js',
                enable_ig_error_detect: true,
                enable_infinite_scroll: true,
                enable_checkout_bar: true,
                enable_gallery: false,
                enable_options: false,
                enable_magento_json_endpoints: true
            };
            </script>
            <script src=\"$storeAssetsHost/scripts/app.js\" async></script>
        ";
    }
}
