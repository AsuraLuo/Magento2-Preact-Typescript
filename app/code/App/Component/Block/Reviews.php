<?php
namespace App\Component\Block;

class Reviews extends \Magento\Framework\View\Element\Template
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

    public function getReviewJson ($version = 'pc') {
        $data =[];
        $reviewHelper = $this->createObject('Magento\Review\Block\Customer\ListCustomer');
        $reviewCollection = $reviewHelper->getReviews();
        $reviews = [];
        foreach ($reviewCollection as $review) {
            $reviews[] = [
                'product_name' => $review->getName(),
                'product_url' => $reviewHelper->getProductUrl($review),
                'rating_sum' => (int)$review->getSum(),
                'rating_count' => (int)$review->getCount(),
                'rating_detail' => $review->getDetail(),
                'rating_url' => $reviewHelper->getReviewUrl($review),
                'create_date' => $reviewHelper->dateFormat($reviewHelper->getReviewCreatedAt()),
            ];
        }

        $data['reviews'] = $reviews;

        return $this->jsonHelper->jsonEncode($data);
    }
}