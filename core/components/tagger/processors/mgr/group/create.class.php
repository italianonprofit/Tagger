<?php
/**
 * Create a Group
 * 
 * @package tagger
 * @subpackage processors
 */
class TaggerGroupCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'TaggerGroup';
    public $languageTopics = array('tagger:default');
    public $objectType = 'tagger.group';
    /** @var TaggerGroup $object */
    public $object;

    public function beforeSet() {
        $fieldType = $this->getProperty('field_type');
        $showAutotag = (int) $this->getProperty('show_autotag', 0);

        if ($fieldType != 'tagger-field-tags') {
            $this->setProperty('show_autotag', 0);
        }

        if ($showAutotag != 1) {
            $this->setProperty('hide_input', 0);
        }

        $c = $this->modx->newQuery('TaggerGroup');
        $c->sortby('position', 'DESC');
        $c->limit(1);

        /** @var TaggerGroup $group */
        $group = $this->modx->getObject('TaggerGroup', $c);

        if ($group) {
            $this->setProperty('position', $group->position + 1);
        } else {
            $this->setProperty('position', 0);
        }

        return parent::beforeSet();
    }

    public function beforeSave() {
        $name = $this->getProperty('name');
        $alias = $this->getProperty('alias');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('tagger.err.group_name_ns'));
        } else if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon('tagger.err.group_name_ae'));
        }

        if (!empty($alias)) {
            $alias = $this->object->cleanAlias($alias);
            if ($this->doesAlreadyExist(array('alias' => $alias))) {
                $this->addFieldError('alias',$this->modx->lexicon('tagger.err.group_alias_ae'));
            }
        }

        if (!(($this->object->show_autotag == 1) && ($this->object->hide_input == 1) && ($this->object->tag_limit == 1))) {
            $this->object->set('as_radio', 0);
        }

        // aggiorno regola htaccess
        $this->updateHtaccess();

        return parent::beforeSave();
    }

    /**
     *
     */
    private function updateHtaccess(){

        $filepath = MODX_BASE_PATH.".htaccess";
        $f = fopen($filepath, "r+");
        $oldstr = file_get_contents($f);
        $str_to_insert = "RewriteRule ^sfoglia/asdasd-([^/]*)\/$ /sfoglia/?asd[]=$1 [L,QSA]\r";
        $specificLine = "#findme";


// read lines with fgets() until you have reached the right one
//insert the line and than write in the file.

        $alreadyInsert = false;
        while (($buffer = fgets($f)) !== false) {
            if (strpos($buffer, $str_to_insert) !== false) {
                $alreadyInsert = true;
            }
        }
        if(!$alreadyInsert){
            //echo "inserisco...";
            rewind($f);
            while (($buffer = fgets($f)) !== false) {
                if (strpos($buffer, $specificLine) !== false) {
                    //echo "found";
                    $pos = ftell($f);
                    $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
                    file_put_contents($filepath, $newstr);
                    break;
                }
            }
        }
        fclose($f);
    }
}
return 'TaggerGroupCreateProcessor';
