<?php



$schemas = [

  'utilisateur' => [
    'id' => ['type' => 'INTEGER', 'primary' => true, 'auto_increment' => true],
    'nom' => ['type' => 'VARCHAR(100)', 'not_null' => true],
    'prenom' => ['type' => 'VARCHAR(100)', 'not_null' => true],
    'cni' => ['type' => 'VARCHAR(20)', 'not_null' => true, 'unique' => true],
    'lieu_naissance' => ['type' => 'VARCHAR(255)', 'not_null' => true, 'unique' => true],
    'date_naissance' => ['type' => 'VARCHAR(255)', 'not_null' => true, 'unique' => true],
    'url_copie_cni' => ['type' => 'VARCHAR(255)',  'unique' => true],
  ]
];

return $schemas;
