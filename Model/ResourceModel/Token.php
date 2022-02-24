<?php

namespace Zealousweb\GdprCompliance\Model\ResourceModel;

/**
 * Token resource model
 */
class Token extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('customer_delete_token', 'id');
    }
}
