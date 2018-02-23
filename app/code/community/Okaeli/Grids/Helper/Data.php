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
class Okaeli_Grids_Helper_Data extends Okaeli_Grids_Helper_Config
{
    /**
     * @var array
     */
    protected $_emulatedGrid = array();
    /**
     * @var array
     */
    protected $_fieldsFromTable = array();

    /**
     * Create a grid block
     * @param string $type
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    public function emulateGrid($type)
    {
        if (!isset($this->_emulatedGrid[$type])) {
            /** @var Mage_Core_Model_App_Emulation $appEmulation */
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                Mage_Core_Model_App::ADMIN_STORE_ID,
                Mage_Core_Model_App_Area::AREA_ADMINHTML
            );
            switch ($type) {
                case self::PRODUCT_TYPE:
                    /** @var Mage_Adminhtml_Block_Catalog_Product_Grid $gridBlock */
                    $gridBlock = Mage::app()->getLayout()->createBlock('adminhtml/catalog_product_grid');
                    break;
                case self::CUSTOMER_TYPE:
                    /** @var Mage_Adminhtml_Block_Customer_Grid $gridBlock */
                    $gridBlock = Mage::app()->getLayout()->createBlock('adminhtml/customer_grid');
                    break;
                case self::ORDER_TYPE:
                    /** @var Mage_Adminhtml_Block_Sales_Order_Grid $gridBlock */
                    $gridBlock = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_grid');
                    break;
                case self::PAGE_TYPE:
                    /** @var Mage_Adminhtml_Block_Cms_Page_Grid $gridBlock */
                    $gridBlock = Mage::app()->getLayout()->createBlock('adminhtml/cms_page_grid');
                    break;
                case self::BLOCK_TYPE:
                    /** @var Mage_Adminhtml_Block_Cms_Block_Grid $gridBlock */
                    $gridBlock = Mage::app()->getLayout()->createBlock('adminhtml/cms_block_grid');
                    break;
                default:
                    $gridBlock = false;
            }

            if (is_object($gridBlock)) {
                Mage::dispatchEvent('okaeli_grids_emulate_grid_before_html', array('grid' => $gridBlock));
                //@see https://magento.stackexchange.com/a/215272/50208 for an explanation of the following line
                $gridBlock->setTemplate('');
                $gridBlock->toHtml();
                Mage::dispatchEvent('okaeli_grids_emulate_grid_after_html', array('grid' => $gridBlock));
            }

            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            $this->_emulatedGrid[$type] = $gridBlock;
        }

        return $this->_emulatedGrid[$type];
    }

    /**
     * Get Fields from table
     * @param $alias
     * @return mixed
     */
    public function getFieldsFromTable($alias)
    {
        if (!isset($this->_fieldsFromTable[$alias])) {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $tableName = $resource->getTableName($alias);
            $fields = $readConnection->describeTable($tableName);
            $this->_fieldsFromTable[$alias] = $fields;
        }

        return $this->_fieldsFromTable[$alias];
    }
}
