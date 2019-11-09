<?php
namespace App\Component\Block;

class Page extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper
    ) {
        parent::__construct($context);
        $this->jsonHelper = $JsonHelper;
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getPageJson($version = 'pc')
    {
        $data = array();
        $pagerHelper = $this->getObject('Magento\Cms\Block\Page');
        $identities = $pagerHelper->getIdentities();
        $page = $pagerHelper->getPage();
        $id = $page->getId();
        $title = $page->getTitle();
        $content = $page->getContent();
        
        $data['page_id'] = $id;
        $data['page_identities'] = $identities;
        $data['page_title'] = $title;
        $data['page_content']= $content;

        return $this->jsonHelper->jsonEncode($data);
    }
}