<?php

/**
 * Cybage Quotation Plugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available on the World Wide Web at:
 * htt
  /**p://opensource.org/licenses/osl-3.0.php
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

namespace Cybage\Quotation\Controller\Item;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Customer\Controller\AbstractAccount
{

    protected $_quotationitem;
    protected $_managerinterface;
    protected $_customer;
    protected $_customerId;
    protected $_event;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Cybage\Quotation\Model\ResourceModel\QuotationItem\Collection $quotationitem, \Magento\Framework\Message\ManagerInterface $managerinterface, \Magento\Customer\Model\Session $customer, \Magento\Framework\Event\ManagerInterface $event)
    {

        $this->_customer = $customer;
        $this->_customerId = $this->_customer->getCustomerId();
        $this->_quotationitem = $quotationitem;
        $this->_managerinterface = $managerinterface;
        $this->_event = $event;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->_quotationitem;
        $quotationId = 0;
        $collection->addFieldToFilter(['main_table.id', 'main_table.parent_id'], [$id, $id]);
        $collection->getSelect()->join(['quotation' => 'b2b_quotation'], "quotation.id=main_table.quotation_id AND quotation.customer_id={$this->_customerId}", ['customer_id']);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($collection->count()) {
            try {
                foreach ($collection as $value) {
                    $quotationId = $value->getQuotationId();
                    $value->delete();
                    $value->save();
                }
                $this->_event->dispatch('btob_quotation_item_update_after', ['id' => $quotationId]);
                $this->_managerinterface->addSuccess('Product successfully deleted from quotation');
            } catch (\Exception $exc) {
                $this->_managerinterface->addError($exc->getMessage());
            }
        }
        //$resultRedirect->setPath('quotation');
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
