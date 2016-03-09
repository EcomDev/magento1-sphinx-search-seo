<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Grid
    extends EcomDev_Sphinx_Block_Adminhtml_Grid
{

    protected $_sortField = 'text_id';
    protected $_sortDirection = 'desc';
    protected $_filterVar = 'sphinxseo_text_filter';
    protected $_prefix = 'sphinxseo_text';
    protected $_objectId = 'text_id';

    protected function _getCollectionInstance()
    {
        return Mage::getModel('ecomdev_sphinxseo/text')->getCollection();
    }

    /**
     * Prepares grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->_addNumberColumn('text_id', $this->__('ID'), '50px');
        $this->_addTextColumn('name', $this->__('Name'));
        $this->_addOptionsColumn('is_active', $this->__('Is Active'), 'ecomdev_sphinx/source_yesno', '100px');
        $this->_addTextColumn('priority', $this->__('Priority'));
        $this->_addActionColumn($this->__('Action'), array('edit' => $this->__('Edit')));

        return parent::_prepareColumns();
    }

}
