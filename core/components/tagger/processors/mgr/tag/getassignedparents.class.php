<?php
/**
 * Get list of Assigned Resources
 *
 * @package tagger
 * @subpackage processors
 */
class TaggerAssignedParentsGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'TaggerTagCollection';
    public $languageTopics = array('tagger:default');
    public $defaultSortField = 'TaggerTagCollection.id';
    public $defaultSortDirection = 'ASC';
    //public $objectType = 'modResource';

    public function beforeQuery() {

        $tagId = (int) $this->getProperty('tagId');

        if (empty($tagId) || $tagId == 0) {
            return $this->modx->lexicon('tagger.err.tag_assigned_childs_tag_ns');
        }

        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        $tagId = $this->getProperty('tagId');

        $c->innerJoin('TaggerTag', 'TaggerTagChilds', array('TaggerTagChilds.id = TaggerTagCollection.child_id'));
        $c->innerJoin('TaggerTag', 'TaggerTagParent', array('TaggerTagParent.id = TaggerTagCollection.parent_id'));
        $c->select(array(
            $this->modx->getSelectColumns($this->classKey,$this->classKey),
            $this->modx->getSelectColumns("TaggerTag","TaggerTagChilds",'TaggerTagChilds_'),
            $this->modx->getSelectColumns("TaggerTag","TaggerTagParent",'TaggerTagParent_'),
        ));
        $c->where(array(
            'TaggerTagChilds.id' => $tagId
        ));

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $objArray =  $object->toArray();

        return $objArray;
    }

}
return 'TaggerAssignedParentsGetListProcessor';