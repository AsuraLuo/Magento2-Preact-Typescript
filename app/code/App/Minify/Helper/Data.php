<?php
namespace App\Minify\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlInterface;
    
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    public $moduleList;
    
    /**
     * Flag used to determine if Minify extension is enabled in config.
     *
     * @var null|bool
     */
    public $minifyEnabled = null;
    
    /**
     * Flag used to determine if Cache Compatibility option is set in config.
     *
     * @var null|bool
     */
    public $cacheCompatibility = null;
    
    /**
     * Flag used to determine if Maximum HTML Minification is set in config.
     *
     * @var null|bool
     */
    public $maxMinification = null;
    
    /**
     * Flag used to determine if Remove Important Comments is set in config.
     *
     * @var null|bool
     */
    public $removeComments = null;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        $this->logger       = $context->getLogger();
        $this->urlInterface = $context->getUrlBuilder();
        $this->moduleList   = $moduleList;
        
        parent::__construct($context);
    }
    
    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $moduleCode = 'App_Minify';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @return string
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Method returns status of minify extension. Is it enabled or not?
     *
     * @return bool
     */
    public function getMinifyEnabled()
    {
        if ($this->minifyEnabled === null) {
            $this->minifyEnabled = $this->getConfig(
                'app_minify/general/enabled'
            );
        }
    
        return $this->minifyEnabled;
    }
    
    /**
     * Method returns status of cache comatibility option.
     *
     * @return bool
     */
    public function getCacheCompatibility()
    {
        if ($this->cacheCompatibility === null) {
            $this->cacheCompatibility = $this->getConfig(
                'app_minify/general/compatibility'
            );
        }
    
        return $this->cacheCompatibility;
    }
    
    /**
     * Method returns status of maximum HTML minification option.
     *
     * @return bool
     */
    public function getMaxMinification()
    {
        if ($this->maxMinification === null) {
            $this->maxMinification = $this->getConfig(
                'app_minify/general/max_minification'
            );
        }
    
        return $this->maxMinification;
    }
    
    /**
     * Method returns status of Remove Important Comments option.
     *
     * @return bool
     */
    public function getRemoveComments()
    {
        if ($this->removeComments === null) {
            $this->removeComments = $this->getConfig(
                'app_minify/general/remove_comments'
            );
        }
    
        return $this->removeComments;
    }
    
    /**
     * Method calls a HTML minifier with options.
     *
     * @param string $html
     * @param bool $removeComments
     * @param bool $cacheCompatibility
     * @param bool $maxMinification
     */
    public function minifyHtml(
        $html,
        $removeComments = true,
        $cacheCompatibility = false,
        $maxMinification = false
    ) {
        $options = [
            'removeComments'     => $removeComments,
            'cacheCompatibility' => $cacheCompatibility,
            'maxMinification'    => $maxMinification
        ];
        
        try {
            return \App\Minify\Helper\Html::minify($html, $options);
        } catch (\Exception $e) {
            $url = $this->urlInterface->getCurrentUrl();
            $this->logger->debug(
                'You have HTML/CSS/JS error on your web page.'
            );
            $this->logger->debug('Page URL: ' . $url);
            $this->logger->debug('Exception Message: ' . $e->getMessage());
            $this->logger->debug('Exception Trace: ' . $e->getTraceAsString());
            return $html;
        }
    }
}
