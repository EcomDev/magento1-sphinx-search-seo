<?php

/**
 * Url attribute indexer
 *
 */
class EcomDev_SphinxSeo_Model_Indexer_Url_Attribute
    extends Mage_Index_Model_Indexer_Abstract
{
    const EVENT_MATCH_RESULT_KEY = 'sphinxseo_url_attribute_match_result';
    const EVENT_MATCH_SKIP_KEY = 'sphinxseo_url_attribute_skip';
    const EVENT_ATTRIBUTE_IDS = 'sphinxseo_url_attribute_ids';

    /**
     * Matched entity events
     *
     * @var array
     */
    protected $_matchedEntities = array(
        EcomDev_Sphinx_Model_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );

    /**
     * Initializes a resource model
     *
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/indexer_url_attribute');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('ecomdev_sphinx')->__('Sphinx Attribute Url');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('ecomdev_sphinx')->__('Indexes custom url slugs for attribute');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case EcomDev_Sphinx_Model_Attribute::ENTITY:
                $container = $event->getDataObject();
                $event->addNewData(self::EVENT_ATTRIBUTE_IDS, [$container->getId()]);
                break;
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data[self::EVENT_MATCH_RESULT_KEY]) && empty($data[self::EVENT_MATCH_SKIP_KEY])) {
            $this->callEventHandler($event);
        }
    }
}
