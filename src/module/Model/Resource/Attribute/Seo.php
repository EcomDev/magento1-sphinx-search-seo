<?php

class EcomDev_SphinxSeo_Model_Resource_Attribute_Seo
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/option_url', 'option_id');
    }

    public function saveAttribute(EcomDev_SphinxSeo_Model_Attribute_Seo $seo)
    {
        if (!$seo->isOption() || !$seo->getOptionHash()) {
            return $this;
        }

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['option_id IN(?)' => array_keys($seo->getOptionHash())]
        );

        $insertData = [];

        foreach ($seo->getUrlSlugHash() as $optionId => $info) {
            $insertData[] = [
                'option_id' => $optionId,
                'store_id' => 0,
                'slug' => $info['slug']
            ];

            if (isset($info['store_slug']) && is_array($info['store_slug'])) {
                foreach($info['store_slug'] as $storeId => $slug) {
                    $insertData[] = [
                        'option_id' => $optionId,
                        'store_id' => $storeId,
                        'slug' => $slug
                    ];
                }
            }
        }

        $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insertData, [
            'option_id', 'slug'
        ]);
        return $this;
    }

    public function loadAttribute(EcomDev_SphinxSeo_Model_Attribute_Seo $seo)
    {
        if (!$seo->getAttribute()->getId() || !$seo->isOption() || !$seo->getOptionHash()) {
            return $this;
        }

        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), ['option_id', 'store_id', 'slug']);
        $select->where('option_id IN(?)', array_keys($seo->getOptionHash()));
        $slugs = [];

        foreach ($select->query() as $row) {
            if (!isset($slugs[$row['option_id']])) {
                $slugs[$row['option_id']] = [];
            }
            if ($row['store_id'] === '0') {
                $slugs[$row['option_id']]['slug'] = $row['slug'];
            } else {
                $slugs[$row['option_id']]['store_slugs'][$row['store_id']] = $row['slug'];
            }
        }

        $seo->setUrlSlug($slugs);

        return $this;
    }
}
