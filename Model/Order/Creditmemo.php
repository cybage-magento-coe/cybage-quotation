<?php
namespace Cybage\Quotation\Model\Order;
class Creditmemo extends \Magento\Sales\Model\Order\Creditmemo
{
    /**
     * Returns base_discount_amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getOrder()->getDiscountAmount();
    }
    
    /**
     * Returns grand_total
     *
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->getOrder()->getGrandTotal();
    }
    
        public function getBaseDiscountAmount()
    {
        return $this->getOrder()->getBaseDiscountAmount();
    }

    public function getBaseGrandTotal()
    {
        return $this->getOrder()->getBaseGrandTotal();
    }

    public function getBaseSubtotal()
    {
        return $this->getOrder()->getBaseSubtotal();
    }

    public function getSubtotal()
    {
        return $this->getOrder()->getSubtotal();
    }

    public function getSubtotalInclTax()
    {
        return $this->getOrder()->getSubtotalInclTax();
    }

    public function getBaseSubtotalInclTax()
    {
        return $this->getOrder()->getBaseSubtotalInclTax();
    }

    
    public function beforeSave()
    {
        parent::beforeSave();
        $this->setBaseDiscountAmount($this->getOrder()->getBaseDiscountAmount());
        $this->setDiscountAmount($this->getOrder()->getDiscountAmount());
        
        $this->setBaseGrandTotal($this->getOrder()->getBaseGrandTotal());
        $this->setGrandTotal($this->getOrder()->getGrandTotal());
        
        $this->setBaseSubtotal($this->getOrder()->getBaseSubtotal());
        $this->setSubtotal($this->getOrder()->getSubtotal());
        
        $this->setSubtotalInclTax($this->getOrder()->getSubtotalInclTax());
        $this->setBaseSubtotalInclTax($this->getOrder()->getBaseSubtotalInclTax());
        
        return $this;
    }
}
