<?php
/**
 * Get list of Assigned Resources
 *
 * @package tagger
 * @subpackage processors
 */
class TaggerAssignedResourcesGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'TaggerTagResource';
    public $languageTopics = array('tagger:default');
    public $defaultSortField = 'tag';
    public $defaultSortDirection = 'ASC';
    //public $objectType = 'modResource';

    public function beforeQuery() {

        $tagId = (int) $this->getProperty('tagId');

        if (empty($tagId) || $tagId == 0) {
            return $this->modx->lexicon('tagger.err.tag_assigned_resources_tag_ns');
        }

        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        $tagId = $this->getProperty('tagId');
        // MODIFICA INP
        //$c->leftJoin('TaggerTagResource', 'TagResource', array('modResource.id = TagResource.resource AND TagResource.classKey = modResource.class_key'));
        $c->innerJoin('TaggerTag', 'TaggerTag', array('TaggerTag.id = TaggerTagResource.tag'));
        $c->leftJoin('Organizations', 'Organizations', array('Organizations.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Organizations"'));
        $c->leftJoin('Cooperatives', 'Cooperatives', array('Cooperatives.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Cooperatives"'));
        // FINE MODIFICA INP
        $c->where(array(
            'TaggerTagResource.tag' => $tagId
        ));

        if (!empty($query)) {
            $c->where(array(
                'onp_pagetitle:LIKE' => '%'.$query.'%',
                'OR:coop_pagetitle:LIKE' => '%'.$query.'%',
                'OR:pagetitle:LIKE' => '%'.$query.'%'
            ));
        }

        $c->select(array(
            $this->modx->getSelectColumns('TaggerTagResource','TaggerTagResource'),
            "Organizations.name as onp_pagetitle",
            "Cooperatives.name as coop_pagetitle",
            "TaggerTag.alias as alias"
        ));
        $c->groupby("TaggerTagResource.resource");
        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $objArray =  $object->toArray();
        if($objArray['classKey'] == "Organizations"){
            $objArray['pagetitle'] = "(".$objArray['resource'].") ".$objArray['onp_pagetitle'];
        }if($objArray['classKey'] == "Cooperatives"){
            $objArray['pagetitle'] = "(".$objArray['resource'].") ".$objArray['coop_pagetitle'];
        }
        return $objArray;
    }

}
return 'TaggerAssignedResourcesGetListProcessor';