<?php

namespace Zealousweb\GdprCompliance\Controller\Account;

class Deleteconfirm extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Zealousweb\GdprCompliance\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Zealousweb\GdprCompliance\Helper\Data $helper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Zealousweb\GdprCompliance\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->customer = $customer;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Check Customer token and delete customer if token match
     *
     * @return $this
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();
        if (!isset($params['id'])) {
            $this->messageManager->addError(__("Invalid Request. Please contact administrator."));
            $this->_redirect('customer/account/login');
        }

        if($this->getRequest()->isXmlHttpRequest()){

            $response = [];
            $response['success'] = 0;

            if (isset($params['id']) && $params['id'] != '') {
                $id = $params['id'];
                $customer = $this->customer->load($id);
                $token = isset($params['auth_token']) ? $params['auth_token'] : "";
                if ($this->helper->validateToken($customer, $token)) {
                    $this->registry->register('isSecureArea', true);
                    $this->helper->deleteActiveQuotes($id);
                    $customer->delete();
                    $this->helper->deleteToken($id);
                    $this->messageManager->addSuccess(__("Your account has been delete successfully."));
                    $response['success'] = 1;
                } else {
                    $this->messageManager->addError(__("Invalid Token. Please contact administrator."));
                }
            } else {
                $this->messageManager->addError(__("Invalid Request. Please contact administrator."));
            }
                
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        } else {

            if (isset($params['id']) && $params['id'] != '') {
                $id = $params['id'];
                $customer = $this->customer->load($id);
                $token = isset($params['token']) ? $params['token'] : "";
                if (!$this->helper->validateToken($customer, $token)) {
                    $this->messageManager->addError(__("Invalid Token. Please contact administrator."));
                    $this->_redirect('customer/account/login');
                }
            }

            $this->resultPage = $this->resultPageFactory->create();  
            return $this->resultPage;    
        }
    }
}
