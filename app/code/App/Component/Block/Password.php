<?php
namespace App\Component\Block;

class Password extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        parent::__construct($context);
        $this->jsonHelper =$JsonHelper;
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getPasswordJson ($version = 'pc') {
        $data =[];
        $captchaHelper = $this->createObject('Magento\Captcha\Block\Captcha\DefaultCaptcha');
        $captcha = $captchaHelper->getCaptchaModel();
        $data['captcha_img'] = $captcha->getImgSrc();
        $data['refresh_url'] = $captchaHelper->getRefreshUrl();
        $data['captcha_type'] = $captchaHelper->getFormId();
        $passwordHelper = $this->createObject('Magento\Customer\Block\Account\Forgotpassword');
        $data['password_action'] = $passwordHelper->getUrl('*/*/forgotpasswordpost');

        return $this->jsonHelper->jsonEncode($data);
    }
}