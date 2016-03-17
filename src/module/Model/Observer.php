<?php

class EcomDev_SphinxSeo_Model_Observer
{
    public function attachSeoAttributeForm(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block->getLayout()
            && ($seoBlock = $block->getLayout()->getBlock('sphinx.attribute.edit.form.seo'))) {
            $seoBlock->attach($observer->getForm());
        }
    }

    public function setUrlSlugs(Varien_Event_Observer $observer)
    {
        Mage::getModel('ecomdev_sphinxseo/attribute_seo')
            ->setAttribute($observer->getAttribute())
            ->setDataFromArray($observer->getData('data'));
    }

    public function validateSeoAttribute(Varien_Event_Observer $observer)
    {
        $seo = Mage::getModel('ecomdev_sphinxseo/attribute_seo');
        $seo->setAttribute($observer->getAttribute());
        if (($result = $seo->validate()) !== true) {
            $observer->getProxy()->result = $result;
        }
    }

    public function afterAttributeLoad(Varien_Event_Observer $observer)
    {
        $seo = Mage::getModel('ecomdev_sphinxseo/attribute_seo');
        $seo->setAttribute($observer->getAttribute());
        $seo->loadAttribute();
    }

    public function afterAttributeSave(Varien_Event_Observer $observer)
    {
        $seo = Mage::getModel('ecomdev_sphinxseo/attribute_seo');
        $seo->setAttribute($observer->getAttribute());
        $seo->saveAttribute();
    }

    public function renderWholeContent(Varien_Event_Observer $observer)
    {
        $result = $observer->getResult();
        $layout = $observer->getLayout();
        $loader = $observer->getLoader();
        $navigation = $observer->getNavigation();

        if (($content = $layout->getBlock('content'))
            && $navigation
            && $loader
            && $loader->getListBlock()) {
            $result->products = false;
            $loader->toHtml();
            $result->content = $content->toHtml();
            $result->filters = $navigation->toHtml();
        }
    }

    public function checkSeoText(Varien_Event_Observer $observer)
    {
        $request = $observer->getRequest();
        $category = $observer->getCategory();
        $ignoredParams = ['p' => true, 'dir' => true, 'order' => true];

        $query = array_diff_key($request->getQuery(), $ignoredParams);

        $filterConditions = [];

        if ($query) {
            foreach ($query as $name => $value) {
                if (strpos($value, ',') === false) {
                    $value = [$value];
                } else {
                    $value = explode(',', $value);
                }

                foreach ($value as $condition) {
                    if ($condition === '') {
                        continue;
                    }

                    $filterConditions[] = sprintf('%s=%s', $name, $condition);
                }
            }

            $seoText = Mage::getModel('ecomdev_sphinxseo/text')->loadByConditions(
                $filterConditions,
                $category->getId(),
                $category->getStoreId()
            );

            if ($seoText->getId()) {
                $category->addData($seoText->getValues());
            }
        }



    }
}
