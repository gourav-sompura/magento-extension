<?php
namespace Zealousweb\GdprCompliance\Model;

class Token extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Zealousweb\GdprCompliance\Model\ResourceModel\Token');
    }
}
