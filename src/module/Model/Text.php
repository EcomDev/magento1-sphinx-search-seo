<?php

class EcomDev_SphinxSeo_Model_Text
    extends EcomDev_Sphinx_Model_AbstractModel
{
    const XML_PATH_APPLICABLE_CATEGORY_ATTRIBUTES = 'ecomdev/sphinxseo/category/attributes';

    const ENTITY = 'sphinxseo_text';

    /**
     * Entity used to invoke indexation process
     *
     * @var string
     */
    protected $_indexerEntity = self::ENTITY;

    /**
     * Event object for customizations
     *
     * @var string
     */
    protected $_eventObject = 'text';

    /**
     * Event prefix for customizations
     *
     * @var string
     */
    protected $_eventPrefix = 'ecomdev_sphinxseo_text';


    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/text');
    }

    /**
     * Sets data into model from post array
     *
     * @param array $data
     */
    protected function _setDataFromArray(array $data)
    {
        $attributes = ['name', 'store_id', 'is_active', 'priority', 'category_ids', 'filter', 'url_slug'];

        if (isset($data['category_ids']) && is_string($data['category_ids'])) {
            $data['category_ids'] = implode(',', $this->parseCategories($data['category_ids']));
        }

        $this->importData($data, $attributes);
        
        if (isset($data['category'])) {
            $values = [];
            foreach ($this->getApplicableCategoryAttributeCodes() as $code) {
                if (isset($data['category'][$code]) && $data['category'][$code] !== '') {
                    $values[$code] = $data['category'][$code];
                }
            }

            $this->setValues($values);
        }
    }

    /**
     * Initializes validation of the functionality
     *
     * @return bool
     */
    protected function _initValidation()
    {
        $this->_addEmptyValueValidation('name', $this->__('Name'));
        $this->_addEmptyValueValidation('values', $this->__('Category Data'));
        $this->_addValueValidation(
            'values',
            $this->__('Category data should be a valid JSON'),
            function ($value) {
                return is_array($value);
            }
        );

        $this->_addValueValidation(
            'url_slug',
            $this->__('You must assign single category if url slug is specified'),
            function ($value) {
                if (empty($value)) {
                    return true;
                }

                return count($this->getCategoryIds()) === 1;
            }
        );
    }

    /**
     * List of assigned category ids
     *
     * @return int[]
     */
    public function getCategoryIds()
    {
        if (is_array($categoryIds = $this->getData('category_ids'))) {
            return array_unique(array_filter($categoryIds));
        }

        if (is_string($categoryIds)) {
            return $this->parseCategories($categoryIds);
        }

        return [];
    }

    /**
     * Parses category identifiers
     *
     * @return string[]
     */
    private function parseCategories($categoryString)
    {
        return array_unique(array_filter(explode(',', $categoryString)));
    }

    /**
     * Returns applicable attribute hash
     *
     *
     * @return string[]
     */
    public function getApplicableAttributeHash()
    {
        if (!$this->hasData('_applicable_attribute_hash')) {
            $applicableAttributes = [];
            foreach (Mage::getResourceSingleton('ecomdev_sphinx/attribute_collection')
                         ->addFieldToFilter('is_layered', 1) as $attribute) {
                if ($attribute->isOption()) {
                    $applicableAttributes[$attribute->getAttributeCode()] = $attribute->getAttributeName();
                }
            }

            foreach (Mage::getResourceModel('ecomdev_sphinx/field_collection')
                    ->addFieldToFilter('is_active', 1) as $field) {
                /** @var $field EcomDev_Sphinx_Model_Field */
                $applicableAttributes[$field->getCode()] = Mage::helper('ecomdev_sphinxseo')->__(
                    '[Virtual] %s', $field->getName()
                );
            }

            $this->setData('_applicable_attribute_hash', $applicableAttributes);
        }

        return $this->getData('_applicable_attribute_hash');
    }

    /**
     * Returns applicable attribute hash
     *
     *
     * @return string[]
     */
    public function getApplicableAttributeIds()
    {
        if (!$this->hasData('_applicable_attribute_ids')) {
            $applicableAttributes = [];
            foreach (Mage::getResourceSingleton('ecomdev_sphinx/attribute_collection')
                         ->addFieldToFilter('is_layered', 1) as $attribute) {
                if ($attribute->isOption()) {
                    $applicableAttributes[$attribute->getAttributeCode()] = $attribute->getId();
                }
            }

            $this->setData('_applicable_attribute_ids', $applicableAttributes);
        }

        return $this->getData('_applicable_attribute_ids');
    }

    /**
     * Returns attribute hash options
     *
     * @return string[]
     */
    public function getAttributeOptionHash()
    {

        if (!$this->hasData('_attribute_option_hash')) {
            $attributeOptionHash = [];
            $attributeIds = $this->getApplicableAttributeIds();
            $map = array_flip($attributeIds);

            $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
            $options->addFieldToFilter('attribute_id', ['in' => $attributeIds])
                ->setStoreFilter(0)
                ->setPositionOrder()
                ->unshiftOrder('value', Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection::SORT_ORDER_ASC)
            ;

            foreach ($options->getData() as $option) {
                $attributeOptionHash[$map[$option['attribute_id']]][$option['option_id']] = $option['value'];
            }

            foreach (Mage::getResourceModel('ecomdev_sphinx/field_collection')
                         ->addFieldToFilter('is_active', 1) as $field) {
                /** @var $field EcomDev_Sphinx_Model_Field */
                $attributeOptionHash[$field->getCode()] = $field->getOptionHash();
            }

            $this->setData('_attribute_option_hash', $attributeOptionHash);
        }
        return $this->_getData('_attribute_option_hash');
    }

    /**
     * Returns applicable category codes
     *
     * @return string[]
     */
    public function getApplicableCategoryAttributeCodes()
    {
        if (!$this->hasData('_applicable_category_attribute_codes')) {
            $codes = [];
            $node = Mage::getConfig()->getNode(self::XML_PATH_APPLICABLE_CATEGORY_ATTRIBUTES);
            if ($node) {
                foreach ($node->children() as $child) {
                    $codes[] = $child->getName();
                }
            }

            $this->setData('_applicable_category_attribute_codes', $codes);
        }

        return $this->_getData('_applicable_category_attribute_codes');
    }

    /**
     * @param $filterConditions
     * @param $filterNames
     * @param $categoryId
     * @param $storeId
     */
    public function loadByConditions($filterConditions, $categoryId, $storeId)
    {
        if (empty($filterConditions)) {
            return $this;
        }

        $textId = $this->getResource()->findTextId($filterConditions, $categoryId, $storeId);
        if ($textId) {
            $this->load($textId);
        }

        return $this;
    }

    /**
     * Returns single text url slugs
     *
     * @param string $categoryId
     * @param string $storeId
     * @param string[] $filterCodes
     *
     * @return array
     */
    public function getSingleTextSlugs($categoryId, $storeId, $filterCodes)
    {
        return $this->getResource()->getSingleTextSlugs($categoryId, $storeId, $filterCodes);
    }
}
