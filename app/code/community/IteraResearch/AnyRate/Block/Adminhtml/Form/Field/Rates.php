<?php
/**
 * This file is part of AnyRate extension.
 *
 * @category    IteraResearch
 * @package     IteraResearch_AnyRate
 * @copyright   Copyright (c) 2003-2015 Itera Research, Inc. All rights reserved. (http://www.itera-research.com/)
 * @license     http://www.gnu.org/licenses Lesser General Public License
 */

class IteraResearch_AnyRate_Block_Adminhtml_Form_Field_Rates extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var IteraResearch_AnyRate_Block_Adminhtml_Form_Field_Group
     */
    protected $_groupRenderer;

    /**
     * Retrieve customer groups renderer
     *
     * @return IteraResearch_AnyRate_Block_Adminhtml_Form_Field_Group
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'anyrate/adminhtml_form_field_group', '',
                array('is_render_to_js_template' => true)
            );
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', array(
            'label' => Mage::helper('adminhtml')->__('Title'),
            'style' => 'width:180px',
            'class' => 'required-entry',
        ));
        $this->addColumn('price', array(
            'label' => Mage::helper('adminhtml')->__('Price'),
            'style' => 'width:40px',
            'class' => 'required-entry validate-number validate-zero-or-greater'
        ));
        $this->addColumn('customer_group', array(
            'label' => Mage::helper('adminhtml')->__('Customer Group'),
            'renderer' => $this->_getGroupRenderer(),
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Rate');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group')),
            'selected="selected"'
        );
    }
}
