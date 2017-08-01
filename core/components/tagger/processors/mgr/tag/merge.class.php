<?php
/**
 * Remove a Tag.
 * 
 * @package tagger
 * @subpackage processors
 */
class TaggerTagMergeProcessor extends modObjectProcessor {
    public $classKey = 'TaggerTag';
    public $languageTopics = array('tagger:default');
    public $objectType = 'tagger.tag';
    public $tags;

    public function process() {
        $validate = $this->validate();
        if ($validate !== true) {
            return $this->failure($validate);
        }

        $toUpdate = array_shift($this->tags);
        $name = $this->getProperty('name', '');

        foreach ($this->tags as $tag) {
            $tagResources = $this->modx->getCollection('TaggerTagResource', array('tag' => $tag));

            /** @var TaggerTagResource $tagResource */
            foreach ($tagResources as $tagResource) {
                $newRelation = $this->modx->newObject('TaggerTagResource');
                $newRelation->set('tag', $toUpdate);
                $newRelation->set('resource', $tagResource->resource);
                $newRelation->set('classKey', $tagResource->classKey);

                if(!($tagResource->remove())){
                    $this->failure("errore durante rimozione TaggerTagResource");
                    return false;
                }
                if(!($newRelation->save())){
                    $this->failure("errore durante salvataggio nuovo TaggerTagResource");
                    return false;
                }
            }

            $tagObject = $this->modx->getObject($this->classKey, $tag);
            if(!$tagObject){
                $this->failure("TaggerTag non trovato con tagId = ".$tag);
                return false;
            }
            if(!($tagObject->remove())){
                $this->failure("errore durante rimozione TaggerTag");
                return false;
            }
        }

        /** @var TaggerTag $toUpdate */
        $toUpdate = $this->modx->getObject($this->classKey, $toUpdate);
        if(!$toUpdate){
            $this->failure("TaggerTag toUpdate non trovato");
            return false;
        }
        $toUpdate->set('tag', $name);

        if(!($toUpdate->save())){
            $this->failure("errore durante salvataggio TaggerTag toUpdate");
            return false;
        }

        return $this->success();
    }

    public function validate() {
        $tags = $this->getProperty('tags',null);
        $tags = $this->modx->tagger->explodeAndClean($tags);

        if (empty($tags)) {
            return $this->modx->lexicon('tagger.err.tags_ns');
        }

        $name = $this->getProperty('name', '');
        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('tagger.err.tag_name_ns'));
            return false;
        }

        $exists = $this->modx->getCount($this->classKey, array('tag:=' => $name, 'id:NOT IN' => $tags));
        if ($exists > 0) {
            $this->addFieldError('name', $this->modx->lexicon('tagger.err.tag_name_ae'));
            return false;
        }

        $this->tags = $tags;

        return true;
    }
}
return 'TaggerTagMergeProcessor';