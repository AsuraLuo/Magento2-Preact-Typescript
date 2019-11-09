<?php
namespace App\Component\Block;

class Checkout extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;
    private $shippingConfig;
    private $paymentConfig;
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->jsonHelper = $JsonHelper;
        $this->shippingConfig = $shippingConfig;
        $this->paymentConfig = $paymentConfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getAddressRegion() {
        $result = [];
        $countryHelper = $this->createObject('Magento\Directory\Api\CountryInformationAcquirerInterface');
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
                    // base_amount price_excl_tax price_incl_tax
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

    public function getPaymentMethods()
    {
        $methods = [];
        $payments = $this->paymentConfig->getActiveMethods();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->scopeConfig->getValue('payment/' . $paymentCode . '/title');
            
            $methods[] = [
                'label' => $paymentTitle,
                'value' => $paymentCode
            ];
        }
        return $methods;
    }

    public function getCheckoutJson($version = 'pc')
    {
        $data = array();
        $checkoutHelper = $this->createObject('Magento\Checkout\Block\Onepage');
        $checkoutConfig = $checkoutHelper->getCheckoutConfig();
        $data = $checkoutConfig;
        $data['country'] = $this->getAddressRegion();
        $data['shippingMethods'] = $this->getShippingMethods();
        $data['paymentMethods'] = $this->getPaymentMethods();

        return $this->jsonHelper->jsonEncode($data);
    }
}