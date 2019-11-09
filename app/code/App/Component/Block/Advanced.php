<?php
namespace App\Component\Block;

class Advanced extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;
    private $eavConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->eavConfig = $eavConfig;
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getAdvancedJson($version = 'pc'){
        $data =[];
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'color');
        $options = $attribute->getSource()->getAllOptions();
        $colors = [];
        foreach ($options as $opt) {
            if ($opt['value']) {
                $colors[] = [
                    'name' => $opt['label'],
                    'value' => $opt['value']
                ];
            }
        }
        $data['colors'] = $colors;

        return $this->jsonHelper->jsonEncode($data);
    }
}