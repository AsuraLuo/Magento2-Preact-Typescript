<?php
namespace App\Component\Block;

use Magento\Framework\Locale\Format;
class Common extends \Magento\Framework\View\Element\Template
{   
    private $stores;
    private $checkoutsesion;
    private $directoryHelper;
    private $currentStore;
    private $jsonHelper;
    protected $urlBuilder;
    protected $storeManager;
    protected $formKey;
    protected $urlEncoder;
    protected $customerSession;
    protected $categoryCollectionFactory;
    protected $terms;
    protected $minPopularity;
    protected $maxPopularity;
    protected $queryCollectionFactory;
    private $localeFormat;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutsesion,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Search\Model\ResourceModel\Query\CollectionFactory $queryCollectionFactory,
        Format $localeFormat = null
    )
    {
        parent::__construct($context);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->directoryHelper = $directoryHelper;
        $this->urlEncoder = $urlEncoder;
        $this->formKey = $formKey;
        $this->jsonHelper = $JsonHelper;
        $this->customerSession = $customerSession;
        $this->checkoutsesion = $checkoutsesion;
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->localeFormat = $localeFormat ?: $this->getObject(Format::class);
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
        if ($this->currentStore) {
            return $this->currentStore;
        }

        $this->currentStore = $this->storeManager->getStore();
        return $this->currentStore;
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    public function getCheckoutSession()
    {
        return $this->checkoutsesion;
    }

    public function getStores()
    {
        if ($this->stores) {
            return $this->stores;
        }
        $this->stores = $this->storeManager->getStores();
        return $this->stores;
    }

    public function getStoresSubCategories()
    {
        $categoryHelper = $this->createObject('Magento\Catalog\Helper\Category');
        $categories = $categoryHelper ->getStoreCategories($sorted = false, $asCollection = false, $toLoad = true);
        $arr = array();
        foreach ($categories as $category) {
            $loadCategory = $this->createObject('Magento\Catalog\Model\Category')->load($category->getId());
            $subCategories = $loadCategory->getChildrenCategories();
            $subArr = array();

            foreach ($subCategories as $subCategory) {
                if (!$subCategory->getIsActive()) { 
                    continue;
                }
                
                $sub_category_code = [
                    'id' => $subCategory->getId(),
                    'name' => $subCategory->getName(),
                    'url' => $categoryHelper->getCategoryUrl($subCategory)
                ];

                $subArr[] = $sub_category_code;
            }
            
            $category_code = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'url' => $categoryHelper->getCategoryUrl($category),
                'sub_category' => $subArr
            ];
            
            $arr[] = $category_code;
        }

        return $arr;
    }

    public function getConfigData($path)
    {
        return $this->getCurrentStore()->getConfig($path);
    }

    public function getHeaderLinks()
    {
        $headerLinks = array(
            0 => array(
                'name' => 'My Account',
                'url' => $this->getUrl('customer/account')
            ),
            1 => array(
                'name' => 'My Wishlist',
                'url' => $this->getUrl('wishlist')
            ),
            2 => array(
                'name' => 'Sign In',
                'url' => $this->getUrl('customer/account/login'),
            ),
            3 => array(
                'name' => 'Create An Account',
                'url' => $this->getUrl('customer/account/create')
            )
        );

        return $headerLinks;
    }

    public function  getFooterLinks() {
        $headerLinks = array(
            0 => array(
                'name' => 'Search Terms',
                'url' => $this->getUrl('search/term/popular')
            ),
            1 => array(
                'name' => 'Privacy And Cookie Policy',
                'url' => $this->getUrl('privacy-policy-cookie-restriction-mode')
            ),
            2 => array(
                'name' => 'Orders And Returns',
                'url' => $this->getUrl('sales/guest/form'),
            ),
            3 => array(
                'name' => 'Contact Us',
                'url' => $this->getUrl('contact')
            ),
            4 => array(
                'name' => 'Advanced Search',
                'url' => $this->getUrl('catalogsearch/advanced')
            )
        );

        return $headerLinks;
    }

    public function getCommonJson($version = 'pc')
    {
        $data = array();
        $stores = $this->getStores();
        $currentStore = $this->getCurrentStore();
        $objectManger = \Magento\Framework\App\ObjectManager::getInstance();
        $storeArr = array();
        $refer = $this->urlEncoder->encode($this->urlBuilder->getCurrentUrl());
        $form_key = $this->formKey->getFormKey();
        $data['form_key'] = $form_key;

        $cookieHelper = $this->createObject('Magento\Framework\View\Element\Js\Cookie');
        $data['cookie'] = [
            'expires' => null,
            'path' => $cookieHelper->getPath(),
            'domain' =>  $cookieHelper->getDomain(),
            'secure' =>  false,
            'lifetime' =>  $cookieHelper->getLifetime() 
        ];
        
        $pageCacheHelper = $this->createObject('Magento\PageCache\Block\Javascript');
        $pageCache = $pageCacheHelper->getScriptOptions();
        $data['pageCache'] = $this->jsonHelper->jsonDecode($pageCache);

        $translateHelper = $this->createObject('Magento\Translation\Block\Js');
        $data['translate'] = [
            'enabled' => $translateHelper->dictionaryEnabled(),
            'dictionaryFile' => 'js-translation.json',
            'version' => $translateHelper->getTranslationFileVersion()
        ];

        $storeCode = $currentStore->getCode();;
        $data['store_code'] = $storeCode;
        $data['country_id'] = $this->directoryHelper->getDefaultCountry($storeCode);

        foreach ($stores as $store) {
            $ret = $store->getData();
            $ret['value'] = $ret['code'];
            $ret['url'] = $this->urlBuilder->getUrl("stores/store/switch", array('___store' => $ret['code'], 'form_key' => $form_key, 'uenc' => $refer));
            $storeArr[] = $ret;
        }

        $data['uenc'] = $refer;
        $data['priceFormat'] = $this->localeFormat->getPriceFormat();
        $data['stores'] = $storeArr;
        $data['base_url'] = $this->urlBuilder->getUrl('/');
        $data['media_path'] = $currentStore->getBaseUrl('media');
        $data['pub_path'] = $this->getViewFileUrl('/');
        $welcomeMsg = __("welcome to our online website")->render();
        $data['login_url'] = $this->urlBuilder->getUrl('customer/account/login', array('uenc' => $refer));
        $data['create_url'] = $this->urlBuilder->getUrl('customer/account/create');
        $data['is_login'] = false;
        if ($this->getCustomerSession()->isLoggedIn()) {
            $data['is_login'] = true;
            $customer = $this->getCustomerSession()->getCustomer();
            $name = $customer->getName();
            $data['email'] = $customer->getEmail();
            $welcomeMsg = __("welcome %1,enjoy your shopping", $name)->render();
        }
        $data['header_links'] = $this->getHeaderLinks();
        $data['footer_links'] = $this->getFooterLinks();
        $data['welcome'] = $welcomeMsg;

        $logoObject = $this->createObject('Magento\Theme\Block\Html\Header\Logo');
        $logo = $logoObject ->getLogoSrc();
        $data['logo']['src'] = $logo;
        $data['logo']['url'] = $this->urlBuilder->getUrl('/');
        $data['logo']['width'] = $logoObject->getLogoWidth();
        $data['logo']['height'] = $logoObject->getLogoHeight();
        $data['logo']['alt'] = $logoObject->getLogoAlt();

        $pageTitle = $this->createObject('Magento\Theme\Block\Html\Title');
        $title = $pageTitle->getPageHeading();
        $data['title'] = $title;

        $minicartHelper = $this->createObject('Magento\Checkout\Block\Cart\Sidebar');
        $minicart = $minicartHelper->getConfig();
        $data['minicart'] = $minicart;

        $categories = $this->getStoresSubCategories();
        $data['categories'] = $categories;

        $searchTerm = $this->createObject('Magento\Search\Block\Term');
        if (empty($this->terms)) {
            $this->terms = [];
            $terms = $this->queryCollectionFactory->create()->setPopularQueryFilter(
                $this->storeManager->getStore()->getId()
            )->setPageSize(
                6
            )->load()->getItems();
            if (count($terms) > 0) {
                $this->maxPopularity = reset($terms)->getPopularity();
                $this->minPopularity = end($terms)->getPopularity();
                $range = $this->maxPopularity - $this->minPopularity;
                foreach ($terms as $term) {
                    if (!$term->getPopularity()) {
                        continue;
                    }

                    $term->setRatio(($term->getPopularity() - $this->minPopularity) / $range);
                    $this->terms['query_text'] = $term->getData('query_text');
                    $this->terms['url'] = $searchTerm -> getSearchUrl($term);
                    $data['searchterms'][]= $this->terms;
                }
            }

        }
        
        $recentSearch = [];
        $recent_searches = $this->queryCollectionFactory->create()->setRecentQueryFilter(
            $this->storeManager->getStore()->getId()
        )->setPageSize(
            6
        )->load()->getItems();
        if (count($recent_searches) > 0) {
            foreach ($recent_searches as $recent_search) {
                $recentSearch['query_text'] = $recent_search->getData('query_text');
                $recentSearch['url'] = $searchTerm -> getSearchUrl($recent_search);
                $data['recent_searches'][]= $recentSearch;
            }
        }

        $footer = $this->createObject('Magento\Theme\Block\Html\Footer');
        $copyright = $footer ->getCopyright();
        $data['copyright'] = $copyright;
        $data['cart_qty'] = $this->getCheckoutSession()->getQuote()->getItemsSummaryQty();

        $currencyHelper = $this->createObject('Magento\Directory\Model\PriceCurrency');
        $currentStore = $this->getCurrentStore();
        $current_currency = $currentStore->getCurrentCurrencyCode();
        $available_currency = $currentStore->getAvailableCurrencyCodes();
        $data['current_code'] = $current_currency;
        foreach ($available_currency as $item) {
            $currency = [
                'name' => $currencyHelper->getCurrencySymbol(null, $item) . ' - ' . $item,
                'value' => $item,
                'url' => $this->urlBuilder->getUrl('currency', array('currency' => $item, 'uenc' => $refer))
            ];

            $data['currency'][] = $currency;
        }

        return $this->jsonHelper->jsonEncode($data);
    }
}