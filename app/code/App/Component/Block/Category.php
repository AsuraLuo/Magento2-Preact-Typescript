<?php
namespace App\Component\Block;

use Magento\Framework\View\Element\Template;

class Category extends \Magento\Framework\View\Element\Template
{
    protected $jsonHelper;
    protected $request;
    protected $repository;
    protected $catalogData;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repository,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = []
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->repository = $repository;
        $this->catalogData = $catalogData;
        parent::__construct($context, $data);
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getCategoryJson ($version='pc')
    {
        $data = [];
        $type = $this->_request->getParam('type', null);
        $id = $this->_request->getParam('id');
        $postData = $this->_request->getParams();

        $viewHelper= $this->createObject('Magento\Catalog\Block\Category\View');
        $_category = $viewHelper->getCurrentCategory();
        $data['category_id'] = $_category->getId();
        $data['category_image'] = $_category->getImageUrl();
        $data['category_name'] = $_category->getName();
        $data['category_description'] = $_category->getDescription();
        $data['category_contentMode'] = $viewHelper->isContentMode();
        $data['category_mixedMode'] = $viewHelper->isMixedMode();
        $data['category_cms'] = $viewHelper->getCmsBlockHtml();
        $data['category_type'] = $type;

        $sorter = [];
        $toolHelper = $this->createObject('Magento\Catalog\Block\Product\ProductList\Toolbar');
        $orders = $toolHelper->getAvailableOrders();
        foreach ($orders as $_key => $_order) {
            $sorter[] = [
                'name' => $_order,
                'value' => $_key,
                'is_active' => $toolHelper->isOrderCurrent($_key)
            ];
        }
        $data['sorter'] = $sorter;
        $data['direction'] = $toolHelper->getCurrentDirection();

        $home = array(
            'home'=> [
                'label' => 'Home',
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ]
        );
        $path = $this->catalogData->getBreadcrumbPath();
        $path = array_merge($home, $path);
        $data['breadcrumbs'] = $path;

        return $this->jsonHelper->jsonEncode($data);
    }
}