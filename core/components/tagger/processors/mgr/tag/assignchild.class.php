<?php
/**
 * Add child to Tag
 * 
 * @package tagger
 * @subpackage processors
 */
class TaggerAssignChildProcessor extends modObjectCreateProcessor {
    public $classKey = 'TaggerTagCollection';
    public $languageTopics = array('tagger:default');
    public $objectType = 'tagger.tagresource';

    public function beforeSet() {

        $prop = $this->getProperties();
        if(empty($prop['parent_id'])){
            return $this->failure('parent tag not set');
        }
        if(empty($prop['child_id'])){
            return $this->failure('child tag not set');
        }
        $alreadyExist = $this->modx->getObject($this->classKey,array(
            'child_id'=>$prop['child_id'],
            'parent_id'=>$prop['parent_id'],
        ));
        if($alreadyExist) return "Associazione già esistente";
        return true;
    }

}
return 'TaggerAssignChildProcessor';