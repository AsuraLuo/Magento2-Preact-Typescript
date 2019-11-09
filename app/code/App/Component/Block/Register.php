<?php
namespace App\Component\Block;

class Register extends \Magento\Framework\View\Element\Template
{
    protected $urlBuilder;
    private $jsonHelper;
    protected $urlEncoder;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        parent::__construct($context);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->urlEncoder =$urlEncoder;
        $this->jsonHelper =$JsonHelper;
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

    public function getRegisterJson($version = 'pc'){
        $data =[];
        $data['country'] = $this->getAddressRegion();
        $addressHelper = $this->getObject('Magento\Customer\Helper\Address');
        $data['address_count'] = $addressHelper->getStreetLines();
        $nameHelper = $this->getObject('Magento\Customer\Block\Widget\Name');
        $data['prefix'] = [
            'enabled' => $nameHelper->showPrefix(),
            'required' => $nameHelper->isPrefixRequired(),
            'options' => $nameHelper->getPrefixOptions()
        ];
        $data['suffix'] = [
            'enabled' => $nameHelper->showSuffix(),
            'required' => $nameHelper->isSuffixRequired(),
            'options' => $nameHelper->getSuffixOptions()
        ];
        $genderHelper = $this->getObject('Magento\Customer\Block\Widget\Gender');
        $genderOptions = [];
        $options = $genderHelper->getGenderOptions();
        foreach ($options as $option) {
            $genderOptions[] = [
                'name' => $option->getLabel(),
                'value' => $option->getValue()
            ];
        }
        $data['gender'] = [
            'required' => $genderHelper->isRequired(),
            'options' => $genderOptions
        ];

        return $this->jsonHelper->jsonEncode($data);
    }
}