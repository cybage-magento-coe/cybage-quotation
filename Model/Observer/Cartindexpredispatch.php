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

namespace Cybage\Quotation\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Cartindexpredispatch implements ObserverInterface
{

    private $_customer;
    protected $_checkoutSession;
    protected $_quote;

    public function __construct(\Magento\Customer\Model\Session $customer, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Quote\Model\Quote $quote)
    {
        //$this->_quotation = $quotation;
        $this->_customer = $customer;
        $this->_checkoutSession = $checkoutSession;
        $this->_quote = $quote;
    }

    public function execute(Observer $observer)
    {
        //$orderInstance = $observer->getOrder();
        //$incrementId = $orderInstance->getIncrementId();
        $quotatioinId = $this->_customer->getQuotationId();

        if ($quotatioinId) {
            $this->deleteQuoteItems();
            $this->_customer->unsQuotationId();
        }
    }
    
    /**
     * Deletes existing cart items
     */
    private function deleteQuoteItems()
    {
        $checkoutSession = $this->_checkoutSession;
        $this->_quote->load($checkoutSession->getQuote()->getId())->delete();
        $checkoutSession->clearQuote();
    }

}
