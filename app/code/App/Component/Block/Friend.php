<?php
namespace App\Component\Block;

class Friend extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper
    )
    {
        parent::__construct($context);
        $this->jsonHelper =$JsonHelper;
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getFriendJson($version = 'pc') {
        $data = [];
        $friendHelper = $this->getObject('Magento\SendFriend\Block\Send');

        $data['send_url'] = $friendHelper->getSendUrl();
        $data['max_rows'] = (int)$friendHelper->getMaxRecipients();
        $data['user_name'] = $friendHelper->getUserName();
        $data['email'] = $friendHelper->getEmail();
        $data['message'] = $friendHelper->getMessage();

        return $this->jsonHelper->jsonEncode($data);
    }
}