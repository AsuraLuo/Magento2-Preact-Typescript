<?php
namespace App\Component\Block;

class History extends \Magento\Framework\View\Element\Template
{
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $JsonHelper
    )
    {
        parent::__construct($context);
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

    public function getHistoryJson($version = 'pc') {
        $data = [];
        $historyHelper = $this->getObject('Magento\Sales\Block\Order\History');
        $orderData = [];
        $orders = $historyHelper->getOrders();
        
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
        
        $data['history_order'] = $orderData;

        return $this->jsonHelper->jsonEncode($data);
    }
}