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

namespace Cybage\Quotation\Block;

class Quotationdetails extends \Magento\Framework\View\Element\Template
{

    private $_quotation;
    private $_action;
    private $_quotationItem;
    protected $_resource;
    protected $_eavEntity;
    protected $_eavEntityAttribute;
    protected $_quotationComment;
    protected $_customer;
    protected $_customerId;
    protected $registry;
    protected $response;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = array(), \Cybage\Quotation\Model\Quotation $quotation, \Cybage\Quotation\Model\ResourceModel\QuotationItem\Collection $quotationItem, \Magento\Framework\App\Action\Action $action, \Magento\Framework\App\ResourceConnection $resource, \Magento\Eav\Model\Entity $eavEntity, \Magento\Eav\Model\Entity\Attribute $eavEntityAttribute, \Cybage\Quotation\Model\ResourceModel\QuotationComment\Collection $quotationComment, \Magento\Customer\Model\Session $customer, \Magento\Framework\Registry $registry, \Magento\Framework\App\Response\Http $response)
    {
        $this->_quotation = $quotation;
        $this->_quotationItem = $quotationItem;
        $this->_action = $action;
        $this->_resource = $resource;
        $this->_eavEntity = $eavEntity;
        $this->_eavEntityAttribute = $eavEntityAttribute;
        $this->_quotationComment = $quotationComment;
        $this->_customer = $customer;
        $this->_customerId = $this->_customer->getCustomerId();
        $this->registry = $registry;
        $this->response = $response;
        parent::__construct($context, $data);
    }

    public function getQuotationDetails()
    {
        $this->registry->register('quotatin_details', true);
        $productEntityTable = $this->_resource->getTableName('catalog_product_entity');
        $productEntityVarcharTable = $this->_resource->getTableName('catalog_product_entity_varchar');

        $entityTypeId = $this->_eavEntity
                ->setType('catalog_product')
                ->getTypeId();
        $prodNameAttrId = $this->_eavEntityAttribute
                ->loadByCode($entityTypeId, 'name')
                ->getAttributeId();
        $quotationId = $this->_action->getRequest()->getParam('id');
        $quotation = $this->_quotation
                ->getCollection()
                ->addFieldToFilter('main_table.id', $quotationId)
                ->addFieldToFilter('main_table.customer_id', $this->_customerId);
        $quotation->getSelect()
                ->join(['item' => 'b2b_quotation_item'], 'item.quotation_id = main_table.id', ['id as item_id', 'product_id', 'qty', 'product_price', 'proposed_price', 'sku', 'options', 'parent_id']);
        $quotation->getSelect()
                ->join(['product' => $productEntityTable,], 'item.product_id=product.entity_id', ['sku', 'type_id'])
                ->join(['cpev' => $productEntityVarcharTable], 'cpev.entity_id=product.entity_id AND cpev.attribute_id=' . $prodNameAttrId, ['name' => 'value']);
//        echo '<pre>';
//        print_r($quotation->getData());
//        echo '</pre>';
//        die();
        if (empty($quotation->getData())) {
            $this->response->setRedirect($this->_storeManager->getStore()->getBaseUrl() . 'quotation');
        }else{
            return $quotation->getData();
        }
        
    }

    public function getComments($quotattionId = null)
    {
        if ($quotattionId) {
            $customerEntityTable = $this->_resource->getTableName('customer_entity');
            $collection = $this->_quotationComment->addFieldToFilter('quotation_id', $quotattionId);
            return $collection;
        }
    }

    public function isUpdateAllowed($quotationstatus = null)
    {
        $allowArray = [2, 7];
        if (in_array($quotationstatus, $allowArray)) {
            return true;
        }
        return false;
    }

}
