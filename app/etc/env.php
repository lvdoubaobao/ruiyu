<?php
return array (
  'backend' => 
  array (
    'frontName' => 'MztAdmin',
  ),
  'crypt' => 
  array (
    'key' => '15f632b3d46a7f67bb7dd4e90c2d5f3e',
  ),
  /*'cache' => 
  array (
    'frontend' => 
    array (
      'default' => 
      array (
        'id_prefix' => 'm2_',
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => '127.0.0.1',
          'port' => '6379',
          'persistent' => '',
          'database' => 1,
          
          'connect_retries' => 2,
          'read_timeout' => 1,
          'automatic_cleaning_factor' => 0,
          'compress_tags' => 1,
          'compress_data' => 1,
          'compress_threshold' => 20480,
          'compression_lib' => 'gzip',
        ),
      ),
      'page_cache' => 
      array (
        'id_prefix' => 'm2_',
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => '127.0.0.1',
          'port' => '6379',
          'persistent' => '',
          'database' => 2,
       
          'connect_retries' => 2,
          'read_timeout' => 1,
          'automatic_cleaning_factor' => 0,
          'compress_tags' => 1,
          'compress_data' => 1,
          'compress_threshold' => 20480,
          'compression_lib' => 'gzip',
        ),
      ),
    ),
  ),*/
  'session' => 
  array (
    'save' => 'files',
    'redis' => 
    array (
      'host' => '127.0.0.1',
      'port' => '6379',
      'password' => '',
      'timeout' => '5',
      'persistent_identifier' => '',
      'database' => '0',
      'compression_threshold' => '2048',
      'compression_library' => 'gzip',
      'log_level' => '1',
      'max_concurrency' => '6',
      'break_after_frontend' => '0',
      'break_after_adminhtml' => '0',
      'first_lifetime' => '600',
      'bot_first_lifetime' => '60',
      'bot_lifetime' => '7200',
      'disable_locking' => '1',
      'min_lifetime' => '60',
      'max_lifetime' => '2592000',
    ),
  ),
  'db' => 
  array (
    'table_prefix' => '',
    'connection' => 
    array (
      'default' => 
      array (
        'host' => '127.0.0.1',
        'dbname' => 'mzt56743_db',
        'username' => 'mzt56743_db',
        'password' => 'WskHxSmiN8JZhwR8',
        'active' => '1',
      
      ),
    ),
  ),
  'resource' => 
  array (
    'default_setup' => 
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'cache_types' => 
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'full_page' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'translate' => 1,
    'config_webservice' => 1,
    'compiled_config' => 1,
  ),
  'install' => 
  array (
    'date' => 'Mon, 13 May 2019 06:31:38 +0000',
  ),
);
