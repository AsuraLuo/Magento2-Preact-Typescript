<?php
namespace App\Component\Block;

class Home extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;
    private $pagerHelper;
    private $blockRepository;
    private $filterProvider;
    private $storeManager;
    protected $pageConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Cms\Block\Page $pagerHelper,
        \Magento\Cms\Model\BlockRepository $blockRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        parent::__construct($context);
        $this->jsonHelper = $JsonHelper;
        $this->pagerHelper = $pagerHelper;
        $this->blockRepository = $blockRepository;
        $this->filterProvider = $filterProvider;
        $this->pageConfig = $pageConfig;
        $this->storeManager = $context->getStoreManager();
    }

    protected function _prepareLayout()
    {
        $page = $this->pagerHelper->getPage();
        $this->pageConfig->getTitle()->set($page->getTitle());
        $this->pageConfig->setKeywords($page->getMetaKeywords());
        $this->pageConfig->setDescription($page->getMetaDescription());

        return parent::_prepareLayout();
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getCmsContent($content) {
        return $this->filterProvider->getBlockFilter()->setStoreId($this->storeManager->getStore()->getId())->filter($content);
    }

    public function getCmsBlock($blockId) {
        $data = array();
        $cmsBlock = $this->blockRepository->getById($blockId);
        $data['active'] = $cmsBlock->isActive();
        $data['title'] = $cmsBlock->getTitle();
        $data['content'] = $this->getCmsContent($cmsBlock->getContent());

        return $data;
    }

    public function getHomeJson()
    {
        $data = array();
        $identities = $this->pagerHelper->getIdentities();
        $page = $this->pagerHelper->getPage();
        $id = $page->getId();
        $title = $page->getTitle();
        
        $data['page_id'] = $id;
        $data['page_identities'] = $identities;
        $data['page_title'] = $title;
 
        return $this->jsonHelper->jsonEncode($data);
    }
}