<?php

class EcomDev_SphinxSeo_Adminhtml_Sphinxseo_TextController
    extends EcomDev_Sphinx_Controller_Adminhtml
{
    protected $_prefix = 'text';
    protected $_idField = 'text_id';
    protected $_menu = 'catalog/sphinx/seotext';

    /**
     * Returns an instance of assortment model
     *
     * @return EcomDev_Sphinx_Model_Attribute
     */
    protected function _getModel()
    {
        return Mage::getModel('ecomdev_sphinxseo/text');
    }

    /**
     * Initializes admin titles
     *
     * @param null|EcomDev_Sphinx_Model_Attribute $currentObject
     * @return string
     */
    protected function _initTitles(EcomDev_Sphinx_Model_AbstractModel $currentObject = null)
    {
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Sphinx Search'))
            ->_title($this->__('SEO Texts'));

        if ($currentObject !== null) {
            $this->_title(
                $currentObject->getId() ?
                    $this->__('Edit SEO Text "%s"', $currentObject->getName()) :
                    $this->__('New SEO Text')
            );
        }

        return $this;
    }

    public function categoriesJsonAction()
    {
        $this->_initObject();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ecomdev_sphinxseo/adminhtml_text_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     */
    public function wysiwygAction()
    {
        $this->_initObject();
        
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));

        $this->getResponse()->setBody($content->toHtml());
    }


    /**
     * Returns object title for error messages
     *
     * @return string
     */
    protected function _getObjectTitle()
    {
        return $this->__('SEO Text');
    }

    /**
     * Hook on before set data for object
     *
     * @param EcomDev_Sphinx_Model_AbstractModel $object
     * @param array $postData
     * @return $this
     */
    protected function _beforeSetData(EcomDev_Sphinx_Model_AbstractModel $object, $postData)
    {
        $categoryIds = [];
        if ($this->getRequest()->has('category_ids')) {
            $categoryIds = $this->getRequest()->getPost('category_ids');
            $categoryIds = array_filter(array_map('trim', explode(',', $categoryIds)));
        }

        $postData['category_ids'] = $categoryIds;
        return $postData;
    }


}
