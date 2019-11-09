<?php
namespace App\Component\Block;

class Newsletter extends \Magento\Framework\View\Element\Template
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

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getNewsletterJson($version = 'pc'){
        $data = [];
        $newsHelper = $this->getObject('Magento\Customer\Block\Newsletter');
        $data['action'] = $newsHelper->getAction();
        $data['isSubscribed'] = $newsHelper->getIsSubscribed();
        
        return $this->jsonHelper->jsonEncode($data);
    }
}