<?php

namespace Zealousweb\GdprCompliance\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GDPR_ENABLE = 'gdpr/general/active';
    const GDPR_ALLOWDELETE = 'gdpr/delete/allowdelete';
    const GDPR_DELETEMESSAGE = 'gdpr/delete/deletemessage';
    const GDPR_DELETE_ACCOUNT_TEMPLATE = 'gdpr/delete/delete_email_template';
    const GDPR_FORMS = 'gdpr/terms/forms';
    const GDPR_TERMS_LABEL = 'gdpr/terms/termslabel';
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Zealousweb\GdprCompliance\Model\Token
     */
    protected $customerToken;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Zealousweb\GdprCompliance\Model\Token $customerToken
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Zealousweb\GdprCompliance\Model\Token $customerToken,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote $quote
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerToken = $customerToken;
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
        $this->quote = $quote;
    }

    /**
     * Retrieve store config value
     * @param  string $path
     * @return mixed
     */
    public function getConfig($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id = null)
    {
        return $this->scopeConfig->getValue($path, $scope, $id);
    }

    /**
     * Check if Module is enable or disable
     *
     * @return boolean
     */
    public function isModuleEnable()
    {
        return $this->getConfig(self::GDPR_ENABLE);
    }

    /**
     * Check customer can delete account or not
     *
     * @return Bool
     */
    public function canCustomerDeleteAccount()
    {
        return $this->getConfig(self::GDPR_ALLOWDELETE);
    }

    /**
     * Get delete message
     *
     * @return string
     */
    public function getDeleteMessage()
    {
        return $this->getConfig(self::GDPR_DELETEMESSAGE);
    }

    /**
     * Get login customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getLoginCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Check is customer is login or not
     *
     * @return boolean
     */
    public function isCustomerLogin()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get Token Expiration Date
     *
     * @return Datetime
     */
    public function getTokenExpiryDate()
    {
        return date('Y-m-d H:i:s', strtotime("+1 day"));
    }

    /**
     * Create delete account token
     *
     * @param  \Magento\Customer\Model\Custmer $customer
     * @return string
     */
    public function createToken($customer, $expDate)
    {
        $id = $customer->getId();
        $email = $customer->getEmail();
        return sha1($id.$email.$expDate);
    }

    /**
     * Validate Token
     *
     * @param  \Magento\Customer\Model\Custmer $customer
     * @param  string $token
     * @return bool
     */
    public function validateToken($customer, $token)
    {
        $previousToken = $this->getCustomerToken($customer->getId());
        if (!empty($previousToken->getData())) {
            $expDate = $previousToken->getExpDate();
            $expiry_date = new \DateTime($expDate);
            $current_date = new \DateTime();
            if ($expiry_date > $current_date) {
                if ($token == $previousToken->getToken()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Save Customer Token
     *
     * @param  \Magento\Customer\Model\Custmer $customer
     * @param  string $token
     * @param  Datetime $exp_date
     */
    public function saveCustomerToken($customer, $token, $exp_date)
    {
        $previousToken = $this->getCustomerToken($customer->getId());
        if (!empty($previousToken->getData())) {
            $previousToken->delete();
        }

        $customerToken = $this->customerToken;
        $customerToken->setCustomerId($customer->getId());
        $customerToken->setToken($token);
        $customerToken->setExpDate($exp_date);
        $customerToken->save();
    }

    /**
     * Retrive customer token
     *
     * @param  Int $id
     * @return \Zealousweb\GdprCompliance\Model\Token
     */
    public function getCustomerToken($id)
    {
        return $this->customerToken->getCollection()->addFieldToFilter('customer_id', $id)->getFirstItem();
    }

    /**
     * Delete Customer token
     *
     * @param  Int $id
     */
    public function deleteToken($id)
    {
        $token = $this->customerToken->getCollection()->addFieldToFilter('customer_id', $id)->getFirstItem();
        if (!empty($token->getData())) {
            $token->delete();
        }
    }

    /**
     * Can Terms and condition checkbox show on perticular form
     *
     * @param  string $formId
     * @return bool
     */
    public function canShowTermsBox($formId)
    {
        /* This code used with apple singin extension */
        if($formId == 'apple_singin_form'){
            $formId = 'user_login';
        }
        /* END */

        $forms = explode(',', $this->getConfig(self::GDPR_FORMS));
        if (in_array($formId, $forms) && $this->isModuleEnable()) {
            return true;
        }
        return false;
    }

    /**
     * Parse Variable
     *
     * @param  string $string 
     * @return string $string 
     */
    public function parseVariables($string)
    {
        return $this->filterProvider->getBlockFilter()
        ->setStoreId($this->storeManager->getStore()->getId())
        ->filter($string);
    }

    /**
     * Delete customer's current sales quote
     *
     * @param  Int $customerId
     * @return $this
     */
    public function deleteActiveQuotes($customerId)
    {
        $quotes = $this->quote
        ->getCollection()
        ->addFieldToFilter('customer_id',$customerId)
        ->addFieldToFilter('is_active',1);
        
        foreach ($quotes as $quote) {
            $quote->delete();
        }
    }

    /**
     * Get terms and conditions label.
     * @return string
     */
    public function getTermsLabel()
    {
        $termsLabel = $this->getConfig(self::GDPR_TERMS_LABEL);

        if(empty($termsLabel)) {
            $termsLabel = __('I have read all the terms and conditions.');            
        }
        
        return $termsLabel;
    }
}
