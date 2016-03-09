<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_Tab_Condition
    extends EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_AbstractTab
{
    protected function _getName()
    {
        return $this->__('Filters');
    }

    protected function _createFields()
    {
        $this
            ->_addField(
                'filter', 'js',
                $this->__('Applicable Filters'),
                [
                    'required' => false,
                    'js_class' => 'EcomDev.Sphinx.SeoFilter',
                    'js_template' => $this->getChildHtml('container'),
                    'js_options' => [
                        'attributes' => $this->getDataObject()->getApplicableAttributeHash(),
                        'attributeOptions' => $this->getDataObject()->getAttributeOptionHash(),
                        'row_template' => $this->getChildHtml('row')
                    ]
                ]
            );

        return $this;
    }

}
