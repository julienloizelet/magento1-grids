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
class Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Attribute_Product extends
    Okaeli_Grids_Block_Adminhtml_Widget_Grid_Column_Renderer_Select_Attribute
{

    /**
     * List of attributes we exclude from grid
     * @var array
     */
    protected $_excludedAttributes = array('category_ids');
    /**
     * @var string
     */
    protected $_resourceModelClass = 'catalog/product_attribute_collection';

}
