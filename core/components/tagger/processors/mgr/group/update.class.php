<?php
/**
 * Update a Group
 * 
 * @package tagger
 * @subpackage processors
 */

class TaggerGroupUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'TaggerGroup';
    public $languageTopics = array('tagger:default');
    public $objectType = 'tagger.group';
    /** @var TaggerGroup $object */
    public $object;

    public function beforeSave() {
        $name = $this->getProperty('name');
        $alias = $this->getProperty('alias');

        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon('tagger.err.group_name_ns'));

        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name',$this->modx->lexicon('tagger.err.group_name_ae'));
        }

        $fieldType = $this->getProperty('field_type');
        $showAutotag = (int) $this->getProperty('show_autotag', 0);

        if ($fieldType != 'tagger-field-tags') {
            $this->object->set('show_autotag', 0);
        }

        if ($showAutotag != 1) {
            $this->object->set('hide_input', 0);
        }

        if (!empty($alias)) {
            $alias = $this->object->cleanAlias($alias);
            if ($this->modx->getCount($this->classKey, array('alias' => $alias, 'id:!=' => $this->object->id)) > 0) {
                $this->addFieldError('alias',$this->modx->lexicon('tagger.err.group_alias_ae'));
            } else {
                $this->object->set('alias', $alias);
            }
        }

        if (!(($this->object->show_autotag == 1) && ($this->object->hide_input == 1) && ($this->object->tag_limit == 1))) {
            $this->object->set('as_radio', 0);
        }

        // aggiorno regola htaccess
        $this->updateHtaccess();

        return parent::beforeSave();
    }

    private function updateHtaccess(){
        $newname = str_replace("filtro-","",$this->object->alias);
        $this->modx->log(1,"aggiorno...");
        $filepath = MODX_BASE_PATH.".htaccess";
        $f = fopen($filepath, "r+");
        $oldstr = file_get_contents($filepath);
        $str_to_insert = "RewriteRule ^sfoglia/".$newname."-([^/]*)\/$ /sfoglia/?".$newname."[]=$1 [L,QSA]\r";
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
return 'TaggerGroupUpdateProcessor';