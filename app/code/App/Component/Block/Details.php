<?php
namespace App\Component\Block;

class Details extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        parent::__construct($context);
        $this->jsonHelper =$JsonHelper;
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function geDetailsJson ($version = 'pc') {
        $data =[];
        $reviewHelper = $this->createObject('Magento\Review\Block\Customer\View');
        $product = $reviewHelper->getProductData();
        $review = $reviewHelper->getReviewData();

        $ratingData = [];
        $rating = $reviewHelper->getRating();
        foreach ($rating as $_rating) {
            $ratingData[] = [
                'code' => $_rating->getRatingCode(),
                'percent' => ceil($_rating->getPercent())
            ];
        }

        $data['product_id'] = $product->getId();
        $data['product_url'] = $product->getProductUrl();
        $data['product_name'] = $product->getName();
        $data['review_title'] = $review->getTitle();
        $data['review_data'] = $ratingData;
        $data['review_detail'] = nl2br($review->getDetail());
        $data['review_html'] = $reviewHelper->getReviewsSummaryHtml($product);
        $data['review_time'] = $reviewHelper->dateFormat($review->getCreatedAt());
        $data['back_url'] = $reviewHelper->getBackUrl();

        return $this->jsonHelper->jsonEncode($data);
    }
}