<?php
/**
 * Create a Tag
 * 
 * @package tagger
 * @subpackage processors
 */
class TaggerTagCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'TaggerTag';
    public $languageTopics = array('tagger:default');
    public $objectType = 'tagger.tag';
    /** @var TaggerTag $object */
    public $object;

    public function beforeSave() {
        $name = $this->getProperty('tag');
        $group = $this->getProperty('group');
        $alias = $this->getProperty('alias');
        $classKey = $this->getProperty('classKey');

        if (empty($name) || empty($group) || empty($classKey)) {
            if (empty($group)) {
                $this->addFieldError('group',$this->modx->lexicon('tagger.err.group_name_ns'));
            }

            if (empty($name)) {
                $this->addFieldError('tag',$this->modx->lexicon('tagger.err.tag_name_ns'));
            }
            if (empty($classKey)) {
                $this->addFieldError('classKey',$this->modx->lexicon('tagger.err.classKey_name_ns'));
            }
        } else {
            if ($this->doesAlreadyExist(array('tag' => $name, 'group' => $group))) {
                $this->addFieldError('tag',$this->modx->lexicon('tagger.err.tag_name_ae'));
            }
        }

        if (!empty($alias)) {
            $alias = $this->object->cleanAlias($alias);
            if ($this->doesAlreadyExist(array('alias' => $alias, 'group' => $group))) {
                $this->addFieldError('alias',$this->modx->lexicon('tagger.err.tag_alias_ae'));
            }
        }

        return parent::beforeSave();
    }
}
return 'TaggerTagCreateProcessor';
