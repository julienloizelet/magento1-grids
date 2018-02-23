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
class Okaeli_Grids_Block_Adminhtml_Config_Attribute_Column_Customer extends
    Okaeli_Grids_Block_Adminhtml_Config_Attribute_Column
{
    /**
     * @var string
     */
    protected $_attributeRendererClass = 'okaeli_grids/adminhtml_widget_grid_column_renderer_select_attribute_customer';
    /**
     * @var string
     */
    protected $_afterRendererClass = 'okaeli_grids/adminhtml_widget_grid_column_renderer_select_after_customer';

}
