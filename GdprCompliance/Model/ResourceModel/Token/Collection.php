<?php

namespace Zealousweb\GdprCompliance\Model\ResourceModel\Token;

/**
 * Token resource collection model
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct() 
    {
        $this->_init('Zealousweb\GdprCompliance\Model\Token', 'Zealousweb\GdprCompliance\Model\ResourceModel\Token');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
