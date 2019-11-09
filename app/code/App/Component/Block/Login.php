<?php
namespace App\Component\Block;

use Magento\Framework\View\Element\Template;

class Login extends \Magento\Framework\View\Element\Template
{
    protected $jsonHelper;
    protected $urlBuilder;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getObject ($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getLoginJson ($version='pc')
    {
        $data = [];
        $loginHelper = $this->getObject('Magento\Customer\Block\Form\Login');
        $data['post_action'] = $loginHelper->getPostActionUrl();
        $data['create_url'] = $this->urlBuilder->getUrl('customer/account/create');
        $data['forgot_url'] = $loginHelper->getForgotPasswordUrl();
        return $this->jsonHelper->jsonEncode($data);
    }
}