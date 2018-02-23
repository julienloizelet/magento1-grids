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
class Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Attribute extends
    Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select
{
    /**
     * @var array
     */
    protected $_attributes;

    /**
     * List of attributes we exclude from grid
     * @var array
     */
    protected $_excludedAttributes = array();

    /**
     * @var string
     */
    protected $_resourceModelClass;

    /**
     * @var string
     */
    protected $_tableAlias;

    /**
     * Retrieve product attributes
     *
     * @return array|string
     */
    protected function _getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = array();
            if ($resourceClass = $this->_getResourceModelClass()) {
                $attributes = Mage::getResourceModel($resourceClass);

                foreach ($attributes as $attribute) {
                    $code = $attribute->getAttributeCode();
                    if (!in_array($code, $this->_excludedAttributes)) {
                        $this->_attributes[$code] = $code;
                    }
                }
            } elseif ($tableAlias = $this->_getTableAlias()) {
                $helper = $this->_getHelper();
                $fields = $helper->getFieldsFromTable($tableAlias);
                if (is_array($fields)) {
                    $columns = array_keys($fields);
                    foreach ($columns as $column) {
                        if (!in_array($column, $this->_excludedAttributes)) {
                            $this->_attributes[$column] = $column;
                        }
                    }
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getAttributes() as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get resource model class
     * @return string
     */
    protected function _getResourceModelClass()
    {
        return $this->_resourceModelClass;
    }

    /**
     * Get table alias
     * @return string
     */
    protected function _getTableAlias()
    {
        return $this->_tableAlias;
    }

}
