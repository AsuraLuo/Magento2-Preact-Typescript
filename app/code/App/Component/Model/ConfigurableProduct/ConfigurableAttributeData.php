<?php
namespace App\Component\Model\ConfigurableProduct;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{
    private $swatchHelper;

    public function __construct(
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->swatchHelper = $swatchHelper;
    }

    public function getAttributeOptionsData($attribute, $config)
    {
        $attributeOptionsData = [];
        foreach ($attribute->getOptions() as $attributeOption) {
            $optionId = $attributeOption['value_index'];
            $attributeOptionsData[] = [
                'id' => $optionId,
                'label' => $attributeOption['label'],
                'value' => $this->getSwatchHashCode($optionId),
                'products' => isset($config[$attribute->getAttributeId()][$optionId])
                    ? $config[$attribute->getAttributeId()][$optionId]
                    : [],
            ];
        }
        return $attributeOptionsData;
    }

    public function getSwatchHashCode($optionid) {
        $hashCodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionid]);

        return $hashCodeData[$optionid]['value'];
    }
}