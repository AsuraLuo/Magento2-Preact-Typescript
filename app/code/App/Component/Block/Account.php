<?php
namespace App\Component\Block;

class Account extends \Magento\Framework\View\Element\Template
{
    private $urlBuilder;
    private $jsonHelper;
    private $urlEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper,
        \Magento\Framework\Url\EncoderInterface $urlEncoder
    )
    {
        parent::__construct($context);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->urlEncoder =$urlEncoder;
        $this->jsonHelper =$JsonHelper;
    }

    public function createObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getObject($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function getAccountJson($version = 'pc'){
        $data = [];
        $recentHelper = $this->getObject('Magento\Sales\Block\Order\Recent');
        $orderData = [];
        $orders = $recentHelper->getOrders();
        
        foreach ($orders as $order) {
            $orderData[] = [
                'orderId' => $order->getRealOrderId(),
                'date' => $order->getCreatedAt(),
                'shipTo' => $order->getShippingAddress() ? $order->getShippingAddress()->getName() : '&nbsp;',
                'total' => $order->getGrandTotal(),
                'status' => $order->getStatusLabel(),
                'entityId' => $order->getEntityId(),
                'view_url' => $this->getUrl('sales/order/view', ['order_id' => $order->getId()]),
                'reorder_url' => $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()])
            ];
        }
        
        $data['rencent_orders'] = $orderData;
        $data['sales_url'] = $this->urlBuilder->getUrl('sales/order/history');

        return $this->jsonHelper->jsonEncode($data);
    }
}