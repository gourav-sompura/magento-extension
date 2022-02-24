<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zealousweb\GdprCompliance\Model;

/**
 * Terms and config provider
 */
class TermsConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var \Zealousweb\GdprCompliance\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Zealousweb\GdprCompliance\Helper\Data $helper
     */
    public function __construct(
        \Zealousweb\GdprCompliance\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Append Terms Config in checkout
     *
     * @return $output
     */
    public function getConfig()
    {
        $output['termsConfig'] = [
            'isActive' => $this->helper->isModuleEnable(),
            'display_message' => $this->getTermsConditionLabel(),
            'forms' => explode(',', $this->helper->getConfig(\Zealousweb\GdprCompliance\Helper\Data::GDPR_FORMS))
        ];
        return $output;
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
