<?php
/**
 * This file is part of AnyRate extension.
 *
 * @category    IteraResearch
 * @package     IteraResearch_AnyRate
 * @copyright   Copyright (c) 2003-2015 Itera Research, Inc. All rights reserved. (http://www.itera-research.com/)
 * @license     http://www.gnu.org/licenses Lesser General Public License
 */

class IteraResearch_AnyRate_Block_Adminhtml_Form_Field_Group extends Mage_Core_Block_Html_Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $_customerGroups;

    /**
     * Retrieve customer groups option array
     *
     * @return array
     */
    protected function _getCustomerGroups()
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = Mage::getResourceModel('customer/group_collection')
                ->loadData()->toOptionArray();
        }
        return $this->_customerGroups;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getCustomerGroups() as $group) {
                $this->addOption($group['value'], $group['label']);
            }
        }
        return parent::_toHtml();
    }
}
