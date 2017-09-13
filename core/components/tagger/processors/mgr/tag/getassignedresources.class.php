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
    public $defaultSortField = 'TaggerTag.tag';
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

        $c->innerJoin('TaggerTag', 'TaggerTag', array('TaggerTag.id = TaggerTagResource.tag'));
        $c->leftJoin('Organizations', 'Organizations', array('Organizations.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Organizations"'));
        $c->leftJoin('Cooperatives', 'Cooperatives', array('Cooperatives.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Cooperatives"'));
        $c->leftJoin('INPSummary', 'INPSummary', array('INPSummary.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "INPSummary"'));
        $c->leftJoin('modUser', 'modUser', array('INPSummary.user_id = modUser.id'));

        $c->select(array(
            $this->modx->getSelectColumns('TaggerTagResource','TaggerTagResource'),
            "Organizations.name as Organizations_pagetitle",
            "Cooperatives.name as Cooperatives_pagetitle",
            "INPSummary.user_id as INPSummary_user_id",
            "modUser.username as modUser_username",
            "TaggerTag.alias as alias"
        ));
        $c->where(array(
            'TaggerTagResource.tag' => $tagId
        ));
        if (!empty($query)) {
            $c->where(array(
                'Organizations_pagetitle:LIKE' => '%'.$query.'%',
                'OR:Cooperatives_pagetitle:LIKE' => '%'.$query.'%',
                'OR:pagetitle:LIKE' => '%'.$query.'%'
            ));
        }

        $c->groupby("TaggerTagResource.resource");
        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $objArray =  $object->toArray();
        if($objArray['classKey'] == "Organizations"){
            $objArray['pagetitle'] = "(".$objArray['classKey'].") ".$objArray['Organizations_pagetitle'];
            $objArray['id'] = $objArray['resource'];
        }if($objArray['classKey'] == "Cooperatives"){
            $objArray['id'] = $objArray['resource'];
            $objArray['pagetitle'] = "(".$objArray['classKey'].") ".$objArray['Cooperatives_pagetitle'];
        }if($objArray['classKey'] == "INPSummary"){
            $objArray['id'] = $objArray['resource'];
            $objArray['pagetitle'] = "(".$objArray['classKey'].") ".$objArray['modUser_username'];
        }
        return $objArray;
    }

}
return 'TaggerAssignedResourcesGetListProcessor';