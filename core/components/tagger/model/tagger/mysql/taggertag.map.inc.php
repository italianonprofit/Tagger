<?php
$xpdo_meta_map['TaggerTag']= array (
  'package' => 'tagger',
  'version' => '1.1',
  'table' => 'tagger_tags',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'tag' => NULL,
    'alias' => NULL,
    'group' => NULL,
    'parent' => NULL,
    'searchable_by' => 'null',
    'rank' => 0,
  ),
  'fieldMeta' => 
  array (
    'tag' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'alias' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'group' => 
    array (
      'dbtype' => 'integer',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
    ),
    'parent' => 
    array (
      'dbtype' => 'integer',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'int',
      'null' => true,
    ),
    'searchable_by' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => 'null',
    ),
    'rank' => 
    array (
      'dbtype' => 'integer',
      'attributes' => 'unsigned',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'iTagGroup' => 
    array (
      'alias' => 'iTagGroup',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'tag' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'group' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'iTag' => 
    array (
      'alias' => 'iTag',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'tag' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'iParent' => 
    array (
      'alias' => 'iParent',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'iAlias' => 
    array (
      'alias' => 'iAlias',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'alias' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'iGroup' => 
    array (
      'alias' => 'iGroup',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'group' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Resources' => 
    array (
      'class' => 'TaggerTagResource',
      'local' => 'id',
      'foreign' => 'tag',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Parent' => 
    array (
      'class' => 'TaggerTag',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Group' => 
    array (
      'class' => 'TaggerGroup',
      'local' => 'group',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
