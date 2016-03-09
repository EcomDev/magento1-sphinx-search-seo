<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_Tab_Main
    extends EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_AbstractTab
{
    protected function _getName()
    {
        return $this->__('General Options');
    }

    protected function _createFields()
    {
        $this
            ->_addField(
                'name', 'text',
                $this->__('Name')
            )
            ->_addField(
                'store_id', 'select',
                $this->__('Applicable Store View'),
                [
                    'required' => false,
                    'option_model' => 'ecomdev_sphinxseo/source_text_store'
                ]
            )
            ->_addField(
                'is_active', 'select',
                $this->__('Is Active?'),
                [
                    'required' => true,
                    'option_model' => 'ecomdev_sphinx/source_yesno'
                ]
            )
            ->_addField(
                'priority', 'text',
                $this->__('Priority'),
                [
                    'required' => false
                ]
            );

        return $this;
    }

}
