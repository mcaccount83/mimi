<?php return array(
    'root' => array(
        'name' => 'almasaeed2010/adminlte',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '5e19c765f0f8f67b19f0d9fcea33cb10c2680df6',
        'type' => 'template',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'almasaeed2010/adminlte' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '5e19c765f0f8f67b19f0d9fcea33cb10c2680df6',
            'type' => 'template',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'bower-asset/jquery' => array(
            'pretty_version' => '3.6.4',
            'version' => '3.6.4.0',
            'reference' => '91ef2d8836342875f2519b5815197ea0f23613cf',
            'type' => 'bower-asset',
            'install_path' => __DIR__ . '/../bower-asset/jquery',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
