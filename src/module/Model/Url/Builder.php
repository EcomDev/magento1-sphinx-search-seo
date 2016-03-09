<?php

class EcomDev_SphinxSeo_Model_Url_Builder
    extends EcomDev_Sphinx_Model_Url_Builder
{
    protected $pathReplace = [];

    protected $optionSlugs = [];

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
        $matchSlugs = array_intersect_key($this->optionSlugs, $query);

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

        $url = parent::buildUrl($query, $withRel, $separator);

        return str_replace('{slug}', $replacement, $url);
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

        $this->loadSlugs($filterToLoad);
        return parent::initFacets($facets, $activeFilters);
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
    protected function loadSlugs($filterToLoad)
    {
        if (!$filterToLoad) {
            return $this;
        }

        foreach (Mage::getSingleton('ecomdev_sphinxseo/url')->getFilterSlugs($filterToLoad) as $slug) {
            $this->optionSlugs[$slug['filter']][$slug['value']] = $slug['slug'];
        }

        return $this;
    }


}
