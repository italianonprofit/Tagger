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
    private $oldValues = array();

    public function beforeSet()
    {
        $this->oldValues = $this->object->toArray();
        return parent::beforeSet(); // TODO: Change the autogenerated stub
    }

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
        
        return parent::beforeSave();
    }

    public function afterSave()
    {
        // Aggiorno indici
        $this->updateIndex();
        // aggiorno htaccess
        $this->updateHtaccess();

        return parent::afterSave(); // TODO: Change the autogenerated stub
    }
    private function updateHtaccess(){

        // Genero Alias htaccess
        $name = $this->object->cleanAlias($this->object->name);
        $filepath = MODX_BASE_PATH.".htaccess";
        $f = fopen($filepath, "r+");
        $oldstr = file_get_contents($filepath);

        if ((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
            $str_to_remove = "RewriteRule ^sfoglia/".$this->object->cleanAlias($this->oldValues['name'])."-([^/]*)\/$ /sfoglia/?".$this->oldValues['alias']."[]=$1 [L,QSA]\r";
            $str_to_insert = "RewriteRule ^sfoglia/".$name."-([^/]*)\/$ /sfoglia/?".$this->object->alias."[]=$1 [L,QSA]\r";

        } else {
            $str_to_remove = "RewriteRule ^sfoglia/".$this->object->cleanAlias($this->oldValues['name'])."-([^/]*)\/$ /sfoglia/?".$this->oldValues['alias']."[]=$1 [L,QSA]\n";
            $str_to_insert = "RewriteRule ^sfoglia/".$name."-([^/]*)\/$ /sfoglia/?".$this->object->alias."[]=$1 [L,QSA]\n";

        }
       $specificLine = "#findme";
        // Verify if old value exist and replace it
        while (($buffer = fgets($f)) !== false) {
            if (strpos($buffer, $str_to_remove) !== false) {
                $pos = ftell($f);
                $newstr = substr_replace($oldstr, '', $pos-strlen($buffer), strlen($str_to_remove));
                file_put_contents(MODX_BASE_PATH.".htaccess", $newstr);
                //parent::afterSave();
            }
        }
        rewind($f);
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
                    file_put_contents(MODX_BASE_PATH.".htaccess", $newstr);
                    break;
                }
            }
        }
        fclose($f);
    }
    private function updateIndex(){
        $outputTag = array();
        // prelevo solo i tag di schede sintetiche approvate
        $tagsResp = $this->modx->runProcessor('extras/gettags',array(
            'group'=>$this->object->id,
            'onlyApproved'=>1
        ),array(
            'processors_path'=>MODX_CORE_PATH.'components/inp/processors/'
        ));
        if($tagsResp instanceof modProcessorResponse){
            $tagsResults = $tagsResp->getObject();
            //$this->modx->log(1,"GRUPPO: ".$group->name);
            //$this->modx->log(1,"Count Tag approved: ".count($tagsResults));
            foreach($tagsResults as &$tag){
                $outputTag[$tag['id']] = array(
                    'uri'=>$this->cleanAlias($this->object->name)."-".$this->cleanAlias($tag['tag'])."/",
                    'tag'=>$tag['tag'],
                    'groupAlias'=>$this->object->alias,
                    'groupName'=>$this->object->name
                );
            }

        }
        // CREO INDICI TAG
        foreach($outputTag as $id=>$tagArr){
            // Verifico se esiste già una relazione SearchIndex <-> TaggerTag per il tag
            // altrimenti creo il SearchIndex e SearchIndexTag
            $c = $this->modx->newQuery('SearchIndexTag');
            $c->where(array(
                'SearchIndexTag.tag_id:='=>$id,
            ));
            $alreadyExist = $this->modx->getObject('SearchIndexTag',$c);
            if($alreadyExist){
                // recupero indice id
                $obj = $this->modx->getObject('SearchIndex',$alreadyExist->index_id);
                if($obj->uri != $tagArr['uri']){
                    $obj->set('uri',$tagArr['uri']);
                    $obj->save();
                    //echo "Record {$tagArr['name']} {$tagArr['uri']} esiste già con ID = ".$alreadyExist->id." -> AGGIORNO URL:".$tagArr['uri'];
                    //echo "<hr>";
                }
                continue;
            }else{
                $obj = $this->modx->newObject('SearchIndex',array(
                    'name'=>$tagArr['tag'],
                    'uri'=>$this->cleanAlias($tagArr['uri']),
                    'type'=>'tag',
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ));
                $obj->save();
            }


            // Creo relazione Indice <-> Tag
            $alreadyExistIndexTag = $this->modx->getObject('SearchIndexTag',array(
                'index_id:='=>$obj->get('id'),
                'tag_id:='=>$id,
            ));
            if($alreadyExistIndexTag){
                echo "SearchIndexTag esiste ";
                echo "<hr>";
                $SearchIndexTag = $alreadyExistIndexTag;
            }else{
                $SearchIndexTag = $this->modx->newObject('SearchIndexTag',array(
                    'index_id'=>$obj->get('id'),
                    'tag_id'=>$id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ));
                $SearchIndexTag->save();
            }

            // Creo Widget del tag
            $res = $this->modx->newObject('modResource');
            $alias = $tagArr['alias'];
            $widget = $this->modx->getObject('SearchWidget',array(
                'name:='=>$tagArr['groupName'],
                'OR:alias:='=>$tagArr['groupAlias']
            ));
            if(!($widget)){
                $widget = $this->modx->newObject('SearchWidget',array(
                    'name'=>$tagArr['groupName'],
                    'alias'=>$tagArr['groupAlias'],
                    'type'=>'menu',
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ));
                $widget->save();
            }


            // Creo WidgetIndex
            $widgetIndex = $this->modx->getObject('SearchWidgetIndex',array(
                'index_id'=>$obj->get('id'),
                'widget_id'=>$widget->get('id')
            ));
            if(!($widgetIndex instanceof SearchWidgetIndex)){
                $widgetIndex = $this->modx->newObject('SearchWidgetIndex',array(
                    'index_id'=>$obj->get('id'),
                    'widget_id'=>$widget->get('id'),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ));
                $widgetIndex->save();
            }

        }
    }



    private function cleanAlias($name) {
        $res = new modResource($this->modx);
        $name = str_replace('/', '-', $name);
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        return $res->cleanAlias($name);
    }
}
return 'TaggerGroupUpdateProcessor';