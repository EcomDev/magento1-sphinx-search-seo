<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit
    extends EcomDev_Sphinx_Block_Adminhtml_Edit_Form_Container
{
    /**
     * Object identifier field name in request
     *
     * @var string
     */
    protected $_objectId = 'text_id';

    /**
     * Object name field in request
     *
     * @var string
     */
    protected $_objectName = 'name';

    /**
     * Returns new header label
     *
     * @return string
     */
    protected function _getNewHeaderLabel()
    {
        return $this->__('Add SEO Text');
    }

    /**
     * Returns edit header label
     *
     * @param string $name
     * @return string
     */
    protected function _getEditHeaderLabel($name)
    {
        return $this->__('Edit SEO Text "%s"', $name);
    }

    protected function _prepareLayout()
    {
        if ($this->getLayout() && $head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadExtJs(true);
            $head->setCanLoadTinyMce(true);
        }

        return parent::_prepareLayout();
    }


}
