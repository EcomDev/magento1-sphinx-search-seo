<?php

/**
 * Url attribute indexer
 *
 */
class EcomDev_SphinxSeo_Model_Indexer_Text
    extends Mage_Index_Model_Indexer_Abstract
{
    const EVENT_MATCH_RESULT_KEY = 'sphinxseo_text_match_result';
    const EVENT_MATCH_SKIP_KEY = 'sphinxseo_text_skip';
    const EVENT_TEXT_IDS = 'sphinxseo_text_ids';

    /**
     * Matched entity events
     *
     * @var array
     */
    protected $_matchedEntities = array(
        EcomDev_SphinxSeo_Model_Text::ENTITY => array(
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
        $this->_init('ecomdev_sphinxseo/indexer_text');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('ecomdev_sphinx')->__('Sphinx SEO Text');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('ecomdev_sphinx')->__('Indexes Category SEO text');
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
            case EcomDev_SphinxSeo_Model_Text::ENTITY:
                $container = $event->getDataObject();
                $event->addNewData(self::EVENT_TEXT_IDS, [$container->getId()]);
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
