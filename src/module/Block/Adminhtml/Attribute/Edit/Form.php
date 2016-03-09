<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Attribute_Edit_Form
    extends EcomDev_Sphinx_Block_Adminhtml_Edit_Form
{
    /**
     * Seo attribute wrapper
     *
     * @var EcomDev_SphinxSeo_Model_Attribute_Seo
     */
    private $seo;

    protected function _createFields()
    {
        if ($this->getDataObject()->isOption()) {
            $this->_addFieldset('seo', $this->__('Search Engine Optimization'))
                ->_addField('url_slugs', 'js', $this->__('Url Slugs'), [
                    'required' => false,
                    'js_class' => 'EcomDev.Sphinx.SeoUrl',
                    'js_template' => $this->getChildHtml('container'),
                    'js_options' => [
                        'options' => $this->getDataObject()->getOptionHash(),
                        'stores' => $this->getDataObject()->getStoreHash(),
                        'row_template' => $this->getChildHtml('row')
                    ]
                ]);
        }
    }

    public function attach($form)
    {
        $this->setForm($form);
        $this->_createFields();
    }

    /**
     * Return seo attribute
     *
     * @return EcomDev_SphinxSeo_Model_Attribute_Seo
     */
    public function getDataObject()
    {
        if ($this->seo === null) {
            $this->seo = Mage::getModel('ecomdev_sphinxseo/attribute_seo');
            $this->seo->setAttribute(parent::getDataObject());
        }

        return $this->seo;
    }


}
