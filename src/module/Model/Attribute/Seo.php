<?php

class EcomDev_SphinxSeo_Model_Attribute_Seo
    extends Mage_Core_Model_Abstract
{
    /**
     * Attribute model
     *
     * @var EcomDev_Sphinx_Model_Attribute
     */
    private $attribute;

    /**
     * @var string
     */
    private $disallowedChars = ':/?#[]@!$&\'()*+,= ';


    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/attribute_seo');
    }

    /**
     * @return bool
     */
    public function isOption()
    {
        return $this->attribute->isOption();
    }

    /**
     * Returns option hash
     *
     * @return string[]
     * @throws Mage_Core_Exception
     */
    public function getOptionHash()
    {
        if (!$this->attribute->hasData('option_hash_cache')) {
            $hash = [];
            foreach ($this->attribute->getAttribute()->getSource()->getAllOptions() as $option) {
                if ($option['value'] === '') {
                    continue;
                }
                $hash[$option['value']] = $option['label'];
            }

            $this->attribute->setData('option_hash_cache', $hash);
        }

        return $this->attribute->getData('option_hash_cache');
    }

    /**
     * Hash of store to id map
     *
     * @return string[]
     */
    public function getStoreHash()
    {
        if (!$this->attribute->hasData('store_hash_cache')) {
            $hash = [];
            /** @var Mage_Core_Model_Store $store */
            foreach (Mage::app()->getStores() as $store) {
                $hash[$store->getId()] = $store->getName();
            }

            if (count($hash) === 1) {
                $hash = [];
            }

            $this->attribute->setData('store_hash_cache', $hash);
        }

        return $this->attribute->getData('store_hash_cache');
    }

    /**
     * Returns url slug hash
     *
     * @return string[]
     */
    public function getUrlSlugHash()
    {
        if (!$this->attribute->hasData('url_slugs')) {
            $this->setUrlSlug([]);
        }

        return $this->attribute->getData('url_slugs');
    }

    /**
     * Sets attribute
     *
     * @param EcomDev_Sphinx_Model_Attribute $attribute
     * @return $this
     */
    public function setAttribute(EcomDev_Sphinx_Model_Attribute $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function validate()
    {
        if (!$this->isOption() || !$this->getUrlSlugHash()) {
            return true;
        }

        $options = $this->getOptionHash();
        $stores = $this->getStoreHash();

        $errors = [];

        foreach ($this->getUrlSlugHash() as $optionId => $slugInfo) {
            if (!isset($options[$optionId])) {
                $errors[] = Mage::helper('ecomdev_sphinxseo')->__(
                    'Requested url slug option id "%s" does not exists ',
                    $optionId
                );
                continue;
            }

            if (!$this->validateUrlSlug($slugInfo['slug'])) {
                $errors[] = Mage::helper('ecomdev_sphinxseo')->__(
                    'Slug "%s" containing one of the prohibited characters or empty. Characters that are not allowed: "%s"',
                    $slugInfo['slug'],
                    $this->disallowedChars
                );
                continue;
            }

            if (isset($slugInfo['store_slug']) && is_array($slugInfo['store_slug'])) {
                foreach ($slugInfo['store_slug'] as $store => $value) {
                    if (!isset($stores[$store])) {
                        $errors[] = Mage::helper('ecomdev_sphinxseo')->__(
                            'Specified url slug store "%s" is unknown',
                            $store
                        );
                        continue 2;
                    }

                    if (!$this->validateUrlSlug($value)) {
                        $errors[] = Mage::helper('ecomdev_sphinxseo')->__(
                            'Slug "%s" containing one of the prohibited characters or empty. Characters that are not allowed: "%s"',
                            $value,
                            $this->disallowedChars
                        );

                        continue 2;
                    }
                }
            }
        }

        return (empty($errors) ? true : $errors);
    }

    public function setDataFromArray(array $data)
    {
        if (isset($data['url_slugs']) && is_array($data['url_slugs'])) {
            $this->getAttribute()->setData('url_slugs', $data['url_slugs']);
        }

        return $this;
    }

    /**
     * Validates url slug
     *
     * All characters in general are allowed, except system delimiters and space
     *
     * @param string $slug
     * @return bool
     */
    private function validateUrlSlug($slug)
    {
        if (trim($slug) === '') {
            return false;
        }

        // Disallow all non segment chars including sub-delimiters as in RFC3986 and also space char
        $disallowedCharList = str_split($this->disallowedChars);
        foreach ($disallowedCharList as $char) {
            if (strpos($slug, $char)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns attribute
     *
     * @return EcomDev_Sphinx_Model_Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    public function saveAttribute()
    {
        $this->_getResource()->saveAttribute($this);
        return $this;
    }

    public function loadAttribute()
    {
        $this->_getResource()->loadAttribute($this);
        return $this;
    }

    /**
     * Sets url slugs
     *
     * @param array $slugs
     * @return array
     */
    public function setUrlSlug(array $slugs)
    {
        $this->getAttribute()->setData('url_slugs', $slugs);
        return $this;
    }
}
