<?php
namespace Zealousweb\GdprCompliance\Block;

class Terms extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Zealousweb\GdprCompliance\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Zealousweb\GdprCompliance\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Zealousweb\GdprCompliance\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Check is terms and condition checkbox are required to in form
     *
     * @return parent
     */
    protected function _toHtml()
    {
        if ($this->helper->canShowTermsBox($this->getFormId())) {
            return parent::_toHtml();
        }
    }

    /**
     * Return terms and condition checkbox label
     *
     * @return string
     */
    public function getTermsConditionLabel()
    {
        return $this->helper->parseVariables(
            $this->helper->getTermsLabel()
        );
    }
}
