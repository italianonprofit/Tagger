<?php
$xpdo_meta_map['TaggerTagCollection']= array (
  'package' => 'tagger',
  'version' => '1.1',
  'table' => 'tagger_tag_collections',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'groups' => NULL,
    'tags' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'groups' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'tags' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '1024',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
);
