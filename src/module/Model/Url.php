<?php

class EcomDev_SphinxSeo_Model_Url
    extends Mage_Core_Model_Abstract
{
    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/url');
    }

    /**
     * Returns url slugs for filters
     *
     * @param string[] $filterCodes
     * @return array|Iterator
     */
    public function getFilterSlugs(array $filterCodes)
    {
        return $this->_getResource()->findSlugs($filterCodes, Mage::app()->getStore()->getId());
    }
    
    /**
     * Trims request path
     *
     * @param string $requestPath
     * @param array $query
     * @param int $storeId
     * @return array
     */
    public function trimRequestPath($requestPath, $query, $storeId)
    {
        if (strpos($requestPath, '/') === false || isset($query['q'])) {
            return [];
        }

        $parts = explode('/', trim($requestPath, '/'));

        if (count($parts) === 1) {
            return [];
        }

        $lastPart = end($parts);
        reset($parts);
        $suffix = '';
        if (strpos($lastPart, '.') !== false && substr($requestPath, -1) !== '/') {
            $suffix = substr($lastPart, strpos($lastPart, '.'));
            $parts[count($parts) - 1] = substr($lastPart, 0, -strlen($suffix));
        }

        $slugs = $this->_getResource()->findFilters($parts, $storeId);

        $validSlugs = [];

        if ($slugs) {
            foreach (array_reverse($parts) as $index => $part) {
                if (isset($slugs[$part]) && ($index === 0 || isset($validSlugs[$index - 1]))) {
                    $validSlugs[$index] = $part;
                } elseif ($index > 0) {
                    break;
                }
            }
        }

        if (!$validSlugs) {
            return [];
        }

        $data = [];

        $newPath = array_slice($parts, 0, count($parts) - count($validSlugs));
        $data['path_info'] = '/' . implode('/', $newPath);

        if ($suffix) {
            $data['path_info'] .= $suffix;
        }

        $data['query'] = $query;

        foreach ($validSlugs as $slug) {
            $info = $slugs[$slug];
            if (isset($data['query'][$info['filter']])) {
                $data['query'][$info['filter']] .= ',' . $info['value'];
                continue;
            }

            $data['query'][$info['filter']] = $info['value'];
        }

        return $data;
    }
}
