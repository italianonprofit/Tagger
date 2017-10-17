<?php
/**
 * Remove a Tag.
 * 
 * @package tagger
 * @subpackage processors
 */
class TaggerUnassignChildRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'TaggerTagCollection';
    public $languageTopics = array('tagger:default');


    public function initialize() {
        $prop = $this->getProperties();
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

        if ($this->checkRemovePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

}
return 'TaggerUnassignChildRemoveProcessor';