<?php
$xpdo_meta_map['TaggerTagCollection']= array (
  'package' => 'tagger',
  'version' => '1.1',
  'table' => 'tagger_tag_collections',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'parent_id' => NULL,
    'child_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'parent_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'child_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'aggregates' => 
  array (
    'ParentTag' => 
    array (
      'class' => 'TaggerTag',
      'local' => 'parent_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ChildTag' => 
    array (
      'class' => 'ChildTag',
      'local' => 'child_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
