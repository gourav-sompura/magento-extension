<?php

namespace Zealousweb\GdprCompliance\Controller\Account;

class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Zealousweb\GdprCompliance\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Zealousweb\GdprCompliance\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Zealousweb\GdprCompliance\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Send delete confirmation email
     * @return $this
     */
    public function execute()
    {
        if (!$this->helper->isCustomerLogin()) {
            $this->_redirect('customer/account/login');
            return false;
        }

        $customer = $this->helper->getLoginCustomer();
        $expDate = $this->helper->getTokenExpiryDate();
        $token = $this->helper->createToken($customer, $expDate);
        $this->helper->saveCustomerToken($customer, $token, $expDate);

        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];
        $templateVars = [
            'customer_name' => $customer->getFirstname().' '.$customer->getLastname(),
            'deletelink' => $this->storeManager
                ->getStore()
                ->getBaseUrl().'gdpr/account/deleteconfirm/id/'.$customer->getId().'/token/'.$token
        ];
        $from = [
            'email' => $this->helper->getConfig('trans_email/ident_general/email'),
            'name' => $this->helper->getConfig('trans_email/ident_general/name')
        ];

        $this->inlineTranslation->suspend();

        $templateIdentifier = $this->helper->
            getConfig(\Zealousweb\GdprCompliance\Helper\Data::GDPR_DELETE_ACCOUNT_TEMPLATE);
        $transport = $this->transportBuilder->setTemplateIdentifier($templateIdentifier)
        ->setTemplateOptions($templateOptions)
        ->setTemplateVars($templateVars)
        ->setFrom($from)
        ->addTo($customer->getEmail())
        ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        $this->messageManager->addSuccess(__("Your delete account confirmation email has been sent."));
        $this->_redirect('customer/account/edit');
    }
}
