<?php
namespace App\Component\Block;

use Magento\Framework\DataObject;
use Magento\Framework\Locale\Format;
class Product extends \Magento\Framework\View\Element\Template
{
    private $currentStore;
    private $storeManger;
    private $jsonHelper;
    private $scopeConfig;
    private $urlEncoder;
    private $catalogData;
    private $imageHelper;
    private $helper;
    private $configurableHelper;
    private $configurableAttributeData;
    private $localeFormat;
    private $variationPrices;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableHelper,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        Format $localeFormat = null,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices $variationPrices = null
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManger = $storeManger;
        $this->request = $request;
        $this->jsonHelper = $JsonHelper;
        $this->urlEncoder = $urlEncoder;
        $this->catalogData = $catalogData;
        $this->imageHelper = $imageHelper;
        $this->helper = $helper;
        $this->configurableHelper = $configurableHelper;
        $this->configurableAttributeData = $configurableAttributeData;
        $this->localeFormat = $localeFormat ?: $this->getObject(Format::class);
        $this->variationPrices = $variationPrices ?: $this->getObject('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices');
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getCurrentStore()
    {
        if($this->currentStore)
        {
            return $this->currentStore;
        }
        $this->currentStore =$this->storeManger->getStore();
        
        return $this->currentStore;
    }

    public function getProductInfo($version)
    {
        $data = array();
        $params = $this ->request->getParams();
        $id = $params['id'];
        $emailHelper = $this->getObject('Magento\Catalog\Helper\Product');
        $product = $this->getObject('Magento\Catalog\Model\Product')->load($id);
        $type = $product->getTypeId();
        $data['product_id'] = $id;
        $data['product_type'] = $type;
        $data['product_sku'] = $product->getSku();
        $data['email_to'] = $emailHelper->getEmailToFriendUrl($product);

        $priceInfo = $product->getPriceInfo();
        $prices= $this->variationPrices->getFormattedPrices($priceInfo);
        $data['prices'] = $prices;

        $tierPrices = [];
        $tierPriceModel =  $priceInfo->getPrice('tier_price');
        $tierPricesList = $tierPriceModel->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = [
                'qty' => $this->localeFormat->getNumber($tierPrice['price_qty']),
                'price' => $this->localeFormat->getNumber($tierPrice['price']->getValue()),
                'percentage' => $this->localeFormat->getNumber(
                    $tierPriceModel->getSavePercent($tierPrice['price'])
                ),
            ];
        }
        $data['prices']['tierPrices'] = $tierPrices;

        $viewHelper = $this->createObject('Magento\Catalog\Block\Product\View');
        $data['default_qty'] = $viewHelper->getProductDefaultQty();
        $data['product_action'] = $viewHelper->getSubmitUrl($product);

        $reviewHelper = $this->createObject('Magento\Review\Block\Product\Review');
        $data['review_url'] = $reviewHelper->getProductReviewUrl();
        $data['product_grallery'] = $this->getProductGallery();
        $data['configurable'] = $type === 'configurable' ? $this->getConfigurable($product) : [];

        return $data;
    }

    public function getConfigurable($product) {
        $store = $this->getCurrentStore();
        $allowProducts = $this->configurableHelper->getAllowProducts();
        $options = $this->helper->getOptions($product, $allowProducts);
        $attributesData = $this->configurableAttributeData->getAttributesData($product, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'optionPrices' => $this->getOptionPrices($allowProducts),
            'prices' => $this->variationPrices->getFormattedPrices($this->configurableHelper->getProduct()->getPriceInfo()),
            'images' => $this->getOptionImages($allowProducts),
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        return $config;
    }

    public function getOptionImages($allowProducts)
    {
        $images = [];
        foreach ($allowProducts as $product) {
            $productImages = $this->helper->getGalleryImages($product) ?: [];
            foreach ($productImages as $image) {
                $images[$product->getId()][] = [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => $image->getLabel(),
                    'position' => $image->getPosition(),
                    'isMain' => $image->getFile() == $product->getImage(),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ];
            }
        }

        return $images;
    }

    public function getOptionPrices($allowProducts)
    {
        $prices = [];
        foreach ($allowProducts as $product) {
            $tierPrices = [];
            $priceInfo = $product->getPriceInfo();
            $tierPriceModel =  $priceInfo->getPrice('tier_price');
            $tierPricesList = $tierPriceModel->getTierPriceList();
            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = [
                    'qty' => $this->localeFormat->getNumber($tierPrice['price_qty']),
                    'price' => $this->localeFormat->getNumber($tierPrice['price']->getValue()),
                    'percentage' => $this->localeFormat->getNumber(
                        $tierPriceModel->getSavePercent($tierPrice['price'])
                    ),
                ];
            }

            $prices[$product->getId()] = [
                'oldPrice' => [
                    'amount' => $this->localeFormat->getNumber(
                        $priceInfo->getPrice('regular_price')->getAmount()->getValue()
                    ),
                ],
                'basePrice' => [
                    'amount' => $this->localeFormat->getNumber(
                        $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->localeFormat->getNumber(
                        $priceInfo->getPrice('final_price')->getAmount()->getValue()
                    ),
                ],
                'tierPrices' => $tierPrices,
            ];
        }

        return $prices;
    }

    public function getProductGallery () {
        $gallery = [];
        $galleryHelper = $this->createObject('Magento\Catalog\Block\Product\View\Gallery');
        $images = $galleryHelper->getGalleryImages();

        foreach ($images as $image) {
            $imageItem = new DataObject([
                'thumb' => $image->getData('small_image_url'),
                'img' => $image->getData('medium_image_url'),
                'full' => $image->getData('large_image_url'),
                'caption' => ($image->getLabel() ?: $galleryHelper->getProduct()->getName()),
                'position' => $image->getData('position'),
                'isMain'   => $galleryHelper->isMainImage($image),
                'type' => str_replace('external-', '', $image->getMediaType()),
                'videoUrl' => $image->getVideoUrl(),
            ]);
            foreach ($galleryHelper->getGalleryImagesConfig()->getItems() as $imageConfig) {
                $imageItem->setData(
                    $imageConfig->getData('json_object_key'),
                    $image->getData($imageConfig->getData('data_object_key'))
                );
            }
            $gallery[] = $imageItem->toArray();
        }

        if (empty($gallery)) {
            $gallery[] = [
                'thumb' => $this->imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $this->imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $this->imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => '0',
                'isMain' => true,
                'type' => 'image',
                'videoUrl' => null,
            ];
        }

        return $gallery;
    }

    public function getProductJson($version = 'pc')
    {
        $result = $this->getProductInfo($version);
        $home = array(
            'home'=>
                [
                    'label' => 'Home',
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
        );
        $path = $this->catalogData->getBreadcrumbPath();
        $path = array_merge($home, $path);
        $result['breadcrumbs'] = $path;

        return $this->jsonHelper->jsonEncode($result);
    }
}