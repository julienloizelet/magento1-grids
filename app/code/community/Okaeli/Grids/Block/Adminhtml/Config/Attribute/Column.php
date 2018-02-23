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
class Okaeli_Grids_Block_Adminhtml_Config_Attribute_Column extends
    Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Helper
     * @var Okaeli_Grids_Helper_Data
     */
    protected $_helper;

    /**
     * @var Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Attribute_Product
     */
    protected $_attributeRenderer;

    /**
     * @var Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_After_Product
     */
    protected $_afterRenderer;
    /**
     * @var Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Align
     */
    protected $_alignRenderer;

    /**
     * @var string
     */
    protected $_attributeRendererClass = 'core/html_select';
    /**
     * @var string
     */
    protected $_afterRendererClass = 'core/html_select';
    /**
     * @var string
     */
    protected $_alignRendererClass = 'okaeli_grids/adminhtml_widget_grid_column_renderer_select_align';

    protected function _getAttributeRenderer()
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                $this->_getAttributeRendererClass(), '',
                array('is_render_to_js_template' => true)
            );
            $this->_attributeRenderer->setClass('attribute_group_select');
            $this->_attributeRenderer->setExtraParams('style="width:120px"');
        }

        return $this->_attributeRenderer;
    }

    protected function _getAfterRenderer()
    {
        if (!$this->_afterRenderer) {
            $this->_afterRenderer = $this->getLayout()->createBlock(
                $this->_getAfterRendererClass(), '',
                array('is_render_to_js_template' => true)
            );
            $this->_afterRenderer->setClass('after_group_select');
            $this->_afterRenderer->setExtraParams('style="width:120px"');
        }

        return $this->_afterRenderer;
    }

    protected function _getAlignRenderer()
    {
        if (!$this->_alignRenderer) {
            $this->_alignRenderer = $this->getLayout()->createBlock(
                $this->_getAlignRendererClass(), '',
                array('is_render_to_js_template' => true)
            );
            $this->_alignRenderer->setClass('align_group_select');
            $this->_alignRenderer->setExtraParams('style="width:120px"');
        }

        return $this->_alignRenderer;
    }

    public function _prepareToRender()
    {
        $helper = $this->_getHelper();
        $this->addColumn(
            'attribute', array(
                'label' => $helper->__('Added Column'),
                'style' => 'width:100px',
                'class' => 'required-entry',
                'renderer' => $this->_getAttributeRenderer(),
            )
        );
        $this->addColumn(
            'after', array(
                'label' => $helper->__('After (which column)'),
                'style' => 'width:100px',
                'class' => 'required-entry',
                'renderer' => $this->_getAfterRenderer(),
            )
        );
        $this->addColumn(
            'align', array(
                'label' => $helper->__('Align'),
                'style' => 'width:50px',
                'class' => 'required-entry',
                'renderer' => $this->_getAlignRenderer(),

            )
        );
        $this->addColumn(
            'width', array(
                'label' => $helper->__('Width (in px)'),
                'style' => 'width:50px',
                'class' => 'required-entry validate-number'
            )
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = $helper->__('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('attribute')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getAfterRenderer()->calcOptionHash($row->getData('after')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getAlignRenderer()->calcOptionHash($row->getData('align')),
            'selected="selected"'
        );
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
     * @return string
     */
    protected function _getAttributeRendererClass()
    {
        return $this->_attributeRendererClass;
    }

    /**
     * @return string
     */
    protected function _getAfterRendererClass()
    {
        return $this->_afterRendererClass;
    }

    /**
     * @return string
     */
    protected function _getAlignRendererClass()
    {
        return $this->_alignRendererClass;
    }

}
