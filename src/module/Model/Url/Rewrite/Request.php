<?php

class EcomDev_SphinxSeo_Model_Url_Rewrite_Request
{
    /**
     * Original url rewrite
     *
     * @var Mage_Core_Model_Url_Rewrite_Request
     */
    private $rewrite;

    /**
     * Original url rewrite
     *
     * @var Mage_Core_Model_Url_Rewrite_Request
     */
    private $requestRewrite;

    /**
     * Request object
     *
     * @var Mage_Core_Controller_Request_Http
     */
    private $request;

    /**
     * Our custom url model
     *
     * @var EcomDev_SphinxSeo_Model_Url
     */
    private $url;

    /**
     * Url builder
     *
     * @var EcomDev_Sphinx_Model_Url_Builder
     */
    private $urlBuilder;

    public function __construct(array $args)
    {
        $class = (
            Mage::helper('core')->isModuleEnabled('Enterprise_UrlRewrite')
                ? 'enterprise_urlrewrite/url_rewrite'
                : 'core/url_rewrite_request'
        );

        $this->request = !empty($args['request']) ? $args['request'] : Mage::app()->getFrontController()->getRequest();
        $this->rewrite = !empty($args['rewrite']) ? $args['rewrite'] : Mage::getModel('core/url_rewrite');
        $this->requestRewrite = Mage::getModel($class, $args);
        $this->url = Mage::getSingleton('ecomdev_sphinxseo/url');
        $this->urlBuilder = Mage::getSingleton('ecomdev_sphinx/url_builder');
    }

    public function rewrite()
    {
        if (null === $this->rewrite->getStoreId() || false === $this->rewrite->getStoreId()) {
            $this->rewrite->setStoreId(Mage::app()->getStore()->getId());
        }

        $currentStoreId = $this->rewrite->getStoreId();
        $pathInfo = $this->request->getPathInfo();
        $data = $this->url->trimRequestPath(
            $pathInfo,
            $this->request->getQuery(),
            $currentStoreId
        );

        if (!$data && ($fromStore = $this->request->getQuery('___from_store'))) {
            $stores = Mage::app()->getStores(false, true);
            if (isset($stores[$fromStore])) {
                $data = $this->url->trimRequestPath(
                    $pathInfo,
                    $this->request->getQuery(),
                    $stores[$fromStore]->getId()
                );
            }
        }

        if (!$data) {
            $data = $this->url->getRequestPathRewrite($pathInfo, $currentStoreId);
        }

        if (!$data) {
            $this->requestRewrite->rewrite();
            return $this;
        }

        $this->request->setPathInfo($data['path_info']);
        $this->requestRewrite->rewrite();

        if (!$this->request->getAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS)) {
            $this->request->setPathInfo($pathInfo);
            $this->requestRewrite->rewrite();
            return $this;
        }

        $this->request->setQuery($data['query']);
        $this->urlBuilder->setPathReplace($pathInfo, $data['path_info']);
        return $this;
    }

}
