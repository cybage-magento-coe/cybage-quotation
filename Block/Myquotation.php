<?php

/**
 * Cybage Quotation Plugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available on the World Wide Web at:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to access it on the World Wide Web, please send an email
 * To: Support_Magento@cybage.com.  We will send you a copy of the source file.
 *
 * @category   Quotation Plugin
 * @package    Cybage_Quotation
 * @copyright  Copyright (c) 2014 Cybage Software Pvt. Ltd., India
 *             http://www.cybage.com/pages/centers-of-excellence/ecommerce/ecommerce.aspx
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Cybage Software Pvt. Ltd. <Support_Magento@cybage.com>
 */

namespace Cybage\Quotation\Block;

class Myquotation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer/quotation/list.phtml';

    /**
     * @var \Cybage\Quotation\Model\ResourceModel\Quotation\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

   

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;
    
    protected $_quotationHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cybage\Quotation\Model\ResourceModel\Quotation\CollectionFactory $quotationCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cybage\Quotation\Model\ResourceModel\Quotation\CollectionFactory $quotationCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Cybage\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        
        $this->_orderCollectionFactory = $quotationCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotations'));
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        
//        $currentCustomerId = $this->currentCustomer->getCustomerId();
//
//        $quotationCollection = $this->_quotation->getCollection()
//                ->addFieldToFilter('customer_id', $currentCustomerId);
//        $data = $quotationCollection->getData();
        
        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )/*->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )*/->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($id = null)
    {
        return $this->getUrl('quotation/view/index/', ['id' => $id]);
    }

    

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
    
    public function getQuotationStatus($status)
    {
        return $this->_quotationHelper->getQuotationStatus($status);
    }
}
