<?php

class EcomDev_SphinxSeo_Model_Url_Builder
    extends EcomDev_Sphinx_Model_Url_Builder
{
    protected $pathReplace = [];

    protected $optionSlugs = [];

    protected $fullSlug = [];

    protected $filterCodes = [];

    public function setPathReplace($oldPath, $newPath)
    {
        $this->pathReplace[$oldPath] = $newPath;
    }

    /**
     * Builds url with overridden query
     *
     * @param array $query
     * @param bool $withRel
     * @param string $separator
     * @return string
     */
    protected function buildUrl($query, $withRel = false, $separator = '&amp;')
    {
        if ($query && $this->filterCodes) {
            $possibleMatch = array_intersect_key($query, $this->filterCodes);

            if (isset($this->fullSlug[json_encode($possibleMatch)])) {
                $slug = $this->fullSlug[json_encode($possibleMatch)] . '/';
                $url = Mage::getUrl('', ['_direct' => $slug, '_query' => array_diff_assoc($query, $possibleMatch)]);
                if ($withRel) {
                    return [$url, $this->getRel(array_diff_assoc($query, $possibleMatch))];
                }

                return $url;
            }
        }

        $matchSlugs = [];

        if (!isset($query['q'])) {
            $matchSlugs = array_intersect_key($this->optionSlugs, $query);
        }

        $slugPath = [];
        $replacement = '';
        foreach ($matchSlugs as $filter => $options) {
            $queryOptions = explode(',', $query[$filter]);
            $queryOptions = array_combine($queryOptions, $queryOptions);
            foreach (array_intersect_key($options, $queryOptions) as $option => $slug) {
                unset($queryOptions[$option]);
                $slugPath[] = $slug;
            }

            if ($queryOptions) {
                $query[$filter] = implode(',', $queryOptions);
            } else {
                unset($query[$filter]);
            }
        }

        if ($slugPath) {
            $replacement = '/' . implode('/', $slugPath);
        }

        $url = parent::buildUrl($query, false, $separator);

        $url = str_replace('{slug}', $replacement, $url);

        if ($withRel) {
            $rel = $this->getRel($query);
            return [$url, $rel];
        }

        return $url;
    }

    /**
     * Initializes facets
     *
     * @param array $facets
     * @param array $activeFilters
     * @return $this
     */
    public function initFacets(array $facets, array $activeFilters)
    {
        /** @var EcomDev_Sphinx_Model_Sphinx_FacetInterface $facet */
        $filterToLoad = [];
        foreach ($facets as $facet) {
            if (!$facet->isVisible()) {
                continue;
            }
            $filterToLoad[] = $facet->getFilterField();
        }

        $this->processFacetCodes($filterToLoad);
        return parent::initFacets($facets, $activeFilters);
    }

    /**
     * Processes facet codes
     *
     * @param string[] $facetCodes
     *
     * @return $this
     */
    protected function processFacetCodes($facetCodes)
    {
        $this->loadSlugs($facetCodes);
        return parent::processFacetCodes($facetCodes);
    }


    /**
     * Hook for processing path changes
     *
     * @param string $path
     * @return string
     */
    protected function processPath($path)
    {
        if ($this->pathReplace) {
            $path = str_replace(array_keys($this->pathReplace), array_values($this->pathReplace), $path);
        }

        if (substr($path, -1) === '/') {
            $path = substr($path, 0, -1) . '{slug}/';
            return $path;
        }

        if (($lastSlash = strrpos($path, '/')) !== false
            && ($extension = strpos($path, '.', $lastSlash)) !== false) {
            $path = substr($path, 0, $extension) . '{slug}' . substr($path, $extension);
            return $path;
        }

        return $path . '{slug}';
    }

    /**
     * Load slugs for filters
     *
     * @param string $filterToLoad
     * @return $this
     */
    public function loadSlugs($filterToLoad)
    {
        if (!$filterToLoad) {
            return $this;
        }

        $this->filterCodes = array_combine($filterToLoad, $filterToLoad);

        if ($category = Mage::registry('current_category')) {
            $this->fullSlug = Mage::getSingleton('ecomdev_sphinxseo/text')->getSingleTextSlugs(
                $category->getId(),
                Mage::app()->getStore()->getId(),
                $filterToLoad
            );
        }

        foreach (Mage::getSingleton('ecomdev_sphinxseo/url')->getFilterSlugs($filterToLoad) as $slug) {
            $this->optionSlugs[$slug['filter']][$slug['value']] = $slug['slug'];
        }

        return $this;
    }


}
