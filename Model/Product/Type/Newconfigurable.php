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
namespace Cybage\Quotation\Model\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

class Newconfigurable extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable
{
    private $catalogConfig;
    protected $registry;
    
    public function __construct(\Magento\Catalog\Model\Product\Option $catalogProductOption, \Magento\Eav\Model\Config $eavConfig, \Magento\Catalog\Model\Product\Type $catalogProductType, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Registry $coreRegistry, \Psr\Log\LoggerInterface $logger, ProductRepositoryInterface $productRepository, \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory, \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory, \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory, \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory, \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory, \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor, \Magento\Framework\Cache\FrontendInterface $cache, \Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb, $filesystem, $coreRegistry, $logger, $productRepository, $typeConfigurableFactory, $eavAttributeFactory, $configurableAttributeFactory, $productCollectionFactory, $attributeCollectionFactory, $catalogProductTypeConfigurable, $scopeConfig, $extensionAttributesJoinProcessor, $cache);
    }

    

    /**
     * Retrieve array of "subproducts"
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  array $requiredAttributeIds
     * @return array
     */
    public function getUsedProducts($product, $requiredAttributeIds = null)
    {
        \Magento\Framework\Profiler::start(
            'CONFIGURABLE:' . __METHOD__,
            ['group' => 'CONFIGURABLE', 'method' => __METHOD__]
        );
        if (!$product->hasData($this->_usedProducts) || $this->registry->registry('quotatin_details')) {
            $usedProducts = [];
            $collection = $this->getUsedProductCollection($product)
                ->setFlag('has_stock_status_filter', true)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('weight')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('status')
                ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                ->addFilterByRequiredOptions()
                ->setStoreId($product->getStoreId());

            if (is_array($requiredAttributeIds)) {
                foreach ($requiredAttributeIds as $attributeId) {
                    $attribute = $this->getAttributeById($attributeId, $product);
                    if (!is_null($attribute)) {
                        $collection->addAttributeToFilter($attribute->getAttributeCode(), ['notnull' => 1]);
                    }
                }
            }

            foreach ($collection as $item) {
                /** @var \Magento\Catalog\Model\Product $item */
                $this->getGalleryReadHandler()->execute($item);
                $usedProducts[] = $item;
            }

            $product->setData($this->_usedProducts, $usedProducts);
        }
        \Magento\Framework\Profiler::stop('CONFIGURABLE:' . __METHOD__);
        return $product->getData($this->_usedProducts);
    }
    
     /**
     * Get Config instance
     * @return Config
     * @deprecated
     */
    private function getCatalogConfig()
    {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->catalogConfig;
    }
}
