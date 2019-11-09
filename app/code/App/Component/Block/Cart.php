<?php
namespace App\Component\Block;

class Cart extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;
    private $cart;
    private $shippingConfig;
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig        
    )
    {
        parent::__construct($context);
        $this->jsonHelper = $JsonHelper;
        $this->cart = $cart;
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getAddressRegion() {
        $result = [];
        $countryHelper = $this->getObject("Magento\Directory\Api\CountryInformationAcquirerInterface");
        $countries = $countryHelper->getCountriesInfo();

        foreach ($countries as $country) {
            $regions = [];
            if ($availableRegions = $country->getAvailableRegions()) {
                foreach ($availableRegions as $region) {
                    $regionName = $region->getName();
                    if (preg_match('/Armed/',$regionName)) {
                        continue;
                    }
                    $regions[] = [
                        'code'   => $region->getId(),
                        'value' => $region->getCode(),
                        'name' => $region->getName()
                    ];
                }
            }

            $result[] = [
                'code' => strtolower($country->getId()), 
                'value'   => $country->getTwoLetterAbbreviation(),
                'name'   => (string)$country->getFullNameLocale(),
                'regions' => $regions
            ];
        }

        return $result;
    }

    public function getShippingMethods() {
        $methods = [];
        $activeCarriers = $this->shippingConfig->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];
            $carrierMethods = $carrierModel->getAllowedMethods();

            if ($carrierMethods) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $options[]= [
                        'carrier_code' => $methodCode,
                        'label' => $method
                    ];
                }

                $carrierTitle = $this->scopeConfig->getValue('carriers/'. $carrierCode . '/title');
            }

            $methods[] = [
                'options' => $options,
                'carrier_title' => $carrierTitle,
                'available' => $carrierModel->isActive()
            ];
        }

        return $methods;   
    }

    public function getCartJson($version = 'pc')
    {
        $data = array();
        $cartHelper = $this->getObject('Magento\Checkout\Block\Cart');
        $itemCount = $cartHelper->getItemsCount();

        if ($itemCount > 0) {
            $shippingHelper = $this->getObject('Magento\Checkout\Block\Cart\Shipping');
            $cartConfig = $shippingHelper->getCheckoutConfig();
            $data = $cartConfig;
            $data['country'] = $this->getAddressRegion();
            $data['shippingMethods'] = $this->getShippingMethods();
        }

        return $this->jsonHelper->jsonEncode($data);
    }
}