<?php
/**
 * Okaeli_Grids  Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 3
 * that is bundled with this package in the file LICENSE
 *
 * @category Okaeli
 * @package Okaeli_Grids
 * @copyright  Copyright (c)  2018 Julien Loizelet
 * @author     Julien Loizelet <julienloizelet@okaeli.com>
 * @license    GNU GENERAL PUBLIC LICENSE Version 3
 *
 */

/**
 *
 * @category Okaeli
 * @package  Okaeli_Grids
 * @module   Grids
 * @author   Julien Loizelet <julienloizelet@okaeli.com>
 *
 */
class Okaeli_Grids_Model_Observer extends Mage_Core_Model_Observer
{
    /**
     * Helper
     * @var Okaeli_Grids_Helper_Data
     */
    protected $_helper;
    /**
     * @var array
     */
    protected $_isAttributeWithCode = array();

    /**
     * @var array
     */
    protected $_isFieldWithCode = array();

    const DEFAULT_WIDTH = '50';
    const WIDTH_UNIT = 'px';
    const ENTITY_CUSTOMER_CODE = 'customer';
    const TABLE_ALIAS_ORDER = 'sales/order';
    const TABLE_ALIAS_PAGE = 'cms/page';
    const TABLE_ALIAS_BLOCK = 'cms/block';
    const TABLE_ALIAS_SUBSCRIBER = 'newsletter/subscriber';
    const TABLE_ALIAS_INVOICE = 'sales/invoice';

    /**
     * Add column(s) in grid
     * @param Varien_Event_Observer $observer
     */
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();
        if ($grid instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::CUSTOMER_TYPE);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::ORDER_TYPE, true);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::INVOICE_TYPE, true);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Cms_Page_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::PAGE_TYPE, true);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Cms_Block_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::BLOCK_TYPE, true);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
            $this->_addAttributeToGrid($grid, Okaeli_Grids_Helper_Config::SUBSCRIBER_TYPE, true);
        }
    }

    /**
     * Add attribute(s) to collection
     * @param Varien_Event_Observer $observer
     */
    public function beforeEavCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
            $this->_addAttributeToCollection($collection, Okaeli_Grids_Helper_Config::PRODUCT_TYPE);
        }

        /** @var Mage_Customer_Model_Resource_Customer_Collection */
        if ($collection instanceof Mage_Customer_Model_Resource_Customer_Collection) {
            $this->_addAttributeToCollection($collection, Okaeli_Grids_Helper_Config::CUSTOMER_TYPE);
        }
    }

    /**
     * Join order grid with order table
     * @param Varien_Event_Observer $observer
     */
    public function beforeOrderGridCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getOrderGridCollection();
        if (!isset($collection)) {
            return;
        }

        $helper = $this->_getHelper();
        if ($helper->isEnabled(Okaeli_Grids_Helper_Config::ORDER_TYPE)) {
            $orderGridFields = $helper->getFieldsFromTable('sales/order_grid');
            $orderFields = $helper->getFieldsFromTable(self::TABLE_ALIAS_ORDER);
            $fieldsToJoin = array_keys(array_diff_key($orderFields, $orderGridFields));
            $arrayForJoin = array();
            foreach ($fieldsToJoin as $fieldToJoin) {
                $arrayForJoin[$fieldToJoin] = 'so.' . $fieldToJoin;
            }

            $collection->getSelect()->joinLeft(
                array('so' => $collection->getTable(self::TABLE_ALIAS_ORDER)),
                'so.increment_id = main_table.increment_id',
                $arrayForJoin
            );
        }
    }

    /**
     * Join invoice grid with invoice table
     * @param Varien_Event_Observer $observer
     */
    public function beforeInvoiceGridCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getOrderInvoiceGridCollection();
        if (!isset($collection)) {
            return;
        }

        $helper = $this->_getHelper();
        if ($helper->isEnabled(Okaeli_Grids_Helper_Config::INVOICE_TYPE)) {
            $invoiceGridFields = $helper->getFieldsFromTable('sales/invoice_grid');
            $invoiceFields = $helper->getFieldsFromTable(self::TABLE_ALIAS_INVOICE);
            $fieldsToJoin = array_keys(array_diff_key($invoiceFields, $invoiceGridFields));
            $arrayForJoin = array();
            foreach ($fieldsToJoin as $fieldToJoin) {
                $arrayForJoin[$fieldToJoin] = 'si.' . $fieldToJoin;
            }

            $collection->getSelect()->joinLeft(
                array('si' => $collection->getTable(self::TABLE_ALIAS_INVOICE)),
                'si.order_id = main_table.order_id',
                $arrayForJoin
            );
        }
    }

    /**
     * Get the helper
     * @return Okaeli_Grids_Helper_Data
     */
    protected function _getHelper()
    {
        if ($this->_helper === null) {
            $helper = Mage::helper('okaeli_grids');
            $this->_helper = ($helper) ? $helper : false;
        }

        return $this->_helper;
    }

    /**
     * Add column to grid
     * @param $grid
     * @param string $type
     */
    protected function _addAttributeToGrid($grid, $type, $flat = false)
    {
        $helper = $this->_getHelper();
        try {
            if ($helper->isEnabled($type) && ($settings = $helper->getAttributesSettings($type)) &&
                is_array($settings)
            ) {
                foreach ($settings as $setting) {
                    if (isset($setting['attribute']) && isset($setting['after']) && isset($setting['align'])) {
                        $code = $setting['attribute'];
                        $attribute = ($flat) ? $this->_isFieldWithCode(
                            $type,
                            $code
                        ) : $this->_isAttributeWithCode($type, $code);

                        if ($attribute) {
                            $attributeLabel = ((is_object($attribute)) &&
                                ($storeLabel = $attribute->getStoreLabel(Mage_Core_Model_App::ADMIN_STORE_ID))) ?
                                $storeLabel : $code;
                            $after = $setting['after'];
                            $width = isset($setting['width']) ? $setting['width'] : self::DEFAULT_WIDTH;
                            $width .= self::WIDTH_UNIT;
                            $align = $setting['align'];
                            Mage::dispatchEvent('okaeli_grids_column_add_before', array('grid' => $grid));
                            $grid->addColumnAfter(
                                $code,
                                array(
                                    'header' => $attributeLabel,
                                    'index' => $code,
                                    'width' => $width,
                                    'align' => $align
                                ),
                                $after
                            );
                            Mage::dispatchEvent('okaeli_grids_column_add_after', array('grid' => $grid));
                        } else {
                            $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_WRONG_ATTRIBUTE_CODE . $code);
                        }
                    } else {
                        $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_WRONG_SETTINGS);
                    }
                }
            } else {
                $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_DISABLED);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $helper->debugLog($e->getMessage());
        }
    }

    /**
     * Add attribute to collection (eav model)
     *
     * @param Varien_Data_Collection_Db $collection
     * * @param string $type
     * @return Varien_Data_Collection_Db
     */
    protected function _addAttributeToCollection($collection, $type)
    {
        $helper = $this->_getHelper();
        try {
            if ($helper->isEnabled($type) && ($settings = $helper->getAttributesSettings($type)) &&
                is_array($settings)
            ) {
                foreach ($settings as $setting) {
                    if (isset($setting['attribute'])) {
                        $code = $setting['attribute'];
                        if ($attribute = $this->_isAttributeWithCode($type, $code)) {
                            $collection->addAttributeToSelect(array($code));
                        } else {
                            $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_WRONG_ATTRIBUTE_CODE . $code);
                        }
                    } else {
                        $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_WRONG_SETTINGS);
                    }
                }

                Mage::dispatchEvent('okaeli_grids_eav_collection_after', array('collection' => $collection));
            } else {
                $helper->debugLog(Okaeli_Grids_Helper_Config::LOG_MESSAGE_DISABLED);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $helper->debugLog($e->getMessage());
        }
    }

    /**
     * Check if attribute exists
     * @param string $type
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute|bool
     */
    protected function _isAttributeWithCode($type, $code)
    {
        if (!isset($this->_isAttributeWithCode[$type][$code])) {
            switch ($type) {
                case Okaeli_Grids_Helper_Config::PRODUCT_TYPE:
                    $entity = Mage_Catalog_Model_Product::ENTITY;
                    break;
                case Okaeli_Grids_Helper_Config::CUSTOMER_TYPE:
                    $entity = self::ENTITY_CUSTOMER_CODE;
                    break;
                default:
                    $entity = false;
            }

            /** @var  $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute = ($entity) ? Mage::getModel('eav/entity_attribute')->loadByCode($entity, $code) : false;
            $this->_isAttributeWithCode[$type][$code] =
                (is_object($attribute) && $attribute->getId()) ? $attribute : false;
        }

        return $this->_isAttributeWithCode[$type][$code];
    }

    /**
     * Check if field exists in a table
     * @param string $type
     * @param string $code
     * @return string|bool
     */
    protected function _isFieldWithCode($type, $code)
    {
        if (!isset($this->_isFieldWithCode[$type][$code])) {
            switch ($type) {
                case Okaeli_Grids_Helper_Config::ORDER_TYPE:
                    $tableAlias = self::TABLE_ALIAS_ORDER;
                    break;
                case Okaeli_Grids_Helper_Config::PAGE_TYPE:
                    $tableAlias = self::TABLE_ALIAS_PAGE;
                    break;
                case Okaeli_Grids_Helper_Config::BLOCK_TYPE:
                    $tableAlias = self::TABLE_ALIAS_BLOCK;
                    break;
                case Okaeli_Grids_Helper_Config::SUBSCRIBER_TYPE:
                    $tableAlias = self::TABLE_ALIAS_SUBSCRIBER;
                    break;
                case Okaeli_Grids_Helper_Config::INVOICE_TYPE:
                    $tableAlias = self::TABLE_ALIAS_INVOICE;
                    break;
                default:
                    $tableAlias = false;
            }

            $helper = $this->_getHelper();
            $fields = $helper->getFieldsFromTable($tableAlias);
            $field = false;
            if (is_array($fields)) {
                $columns = array_keys($fields);
                $field = (in_array($code, $columns)) ? $code : false;
            }

            $this->_isFieldWithCode[$type][$code] = $field;
        }

        return $this->_isFieldWithCode[$type][$code];
    }
}