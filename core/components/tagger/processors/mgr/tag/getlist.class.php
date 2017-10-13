<?php
/**
 * Get list Tags
 *
 * @package tagger
 * @subpackage processors
 */
class TaggerTagGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'TaggerTag';
    public $languageTopics = array('tagger:default');
    public $defaultSortField = 'tag';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'tagger.tag';

    public function initialize() {
        $initialized = parent::initialize();

        $this->setDefaultProperties(array(
            'forTagfield' => false,
        ));

        return $initialized;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');

        if (!empty($query)) {
            $c->where(array(
                    'LCASE(tag) LIKE LCASE("%'.$query.'%")'
            ));
        }

        $group = intval($this->getProperty('group'));

        if (!empty($group)) {
            $c->where(array(
                'group' => $group
            ));
        }

        return $c;
    }
    public function prepareRow(xPDOObject $object)
    {
        $arr = $object->toArray();
        $arr['searchable_by'] = implode(",",json_decode($arr['searchable_by']));
        return $arr;
    }

    public function outputArray(array $array,$count = false) {
        if ($count === false) { $count = count($array); }

        $forTagfield = $this->getProperty('forTagfield', false);

        if ($forTagfield == true) {
            return '{"success": true, "total":"'.$count.'","results":'.$this->modx->toJSON($array).'}';
        }

        return '{"success": true, "total":"'.$count.'","results":'.$this->modx->toJSON($array).'}';
    }
}
return 'TaggerTagGetListProcessor';