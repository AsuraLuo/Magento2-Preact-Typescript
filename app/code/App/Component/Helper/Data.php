<?php
namespace App\Component\Helper;

use Magento\Framework\App\ObjectManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $objManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->objManager = ObjectManager::getInstance();
        parent::__construct($context);
    }

    public function getObject($className) {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    public function createObject($className) {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($className);
    }

    public function getConnection() {
        if ($this->conn)
            return $this->conn;
        $this->conn = $this->getObject('Magento\Framework\App\ResourceConnection')->getConnection();

        return $this->conn;
    }

    public function query($sql, $params = null) {
        $stmt = $this->prequery($sql, $params);
        return $stmt->fetchAll();
    }

    public function prequery($sql, $params = null) {
        $stmt = $this->getConnection()->prepare($sql);
        if ($params != null) {
            $params = is_array($params) ? $params : [$params];
            $stmt->execute($params);

        } else {
            $stmt->execute();
        }

        return $stmt;
    }
}