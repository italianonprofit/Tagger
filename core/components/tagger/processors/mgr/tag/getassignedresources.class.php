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
        //$c->leftJoin('Organizations', 'Organizations', array('Organizations.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Organizations"'));
        //$c->leftJoin('Cooperatives', 'Cooperatives', array('Cooperatives.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "Cooperatives"'));
        $c->leftJoin('INPSummary', 'INPSummary', array('INPSummary.id = TaggerTagResource.resource AND TaggerTagResource.classKey = "INPSummary"'));
        $c->leftJoin('PreForm', 'PreForm', array('INPSummary.preform_id = PreForm.id'));
        $c->leftJoin('modUser', 'modUser', array('INPSummary.user_id = modUser.id'));

        $c->select(array(
            $this->modx->getSelectColumns('TaggerTagResource','TaggerTagResource'),
            //$this->modx->getSelectColumns('Organizations','Organizations','Organizations_'),
            //$this->modx->getSelectColumns('Cooperatives','Cooperatives','Cooperatives_'),
            $this->modx->getSelectColumns('INPSummary','INPSummary','INPSummary_'),
            $this->modx->getSelectColumns('PreForm','PreForm','PreForm_'),
            "INPSummary.user_id as INPSummary_user_id",
            "modUser.username as modUser_username",
            "TaggerTag.alias as alias"
        ));

        $c->where(array(
            'TaggerTagResource.tag' => $tagId
        ));
        if (!empty($query)) {
            $c->where(array(
                'PreForm.name:LIKE' => '%'.$query.'%'
            ));
        }

        //$c->groupby("TaggerTagResource.resource");
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