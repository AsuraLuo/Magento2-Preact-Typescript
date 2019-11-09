<?php
namespace App\Component\Block;

class Address extends \Magento\Framework\View\Element\Template
{
    private $urlBuilder;
    private $jsonHelper;
    private $urlEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\Url\EncoderInterface $urlEncoder
    )
    {
        parent::__construct($context);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->urlEncoder =$urlEncoder;
        $this->jsonHelper =$JsonHelper;
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getAddressJson($version = 'pc'){
        $data = [];
        $bookHelper = $this->getObject('Magento\Customer\Block\Address\Book');
        $data['new_url'] = $bookHelper->getAddAddressUrl();
        $billing_address = $bookHelper->getDefaultBilling();
        $shipping_address = $bookHelper->getDefaultBilling();
        $data['billing_address'] = [];
        $data['shipping_address'] = [];

        return $this->jsonHelper->jsonEncode($data);
    }
}