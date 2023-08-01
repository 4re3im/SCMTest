<?php

/**
 * Configuration Manager
 * Handles env variables
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Configuration;

use Dotenv\Dotenv as EnvironmentHandler;

class ConfigurationManager
{
    /**
     * ConfigurationManager constructor
     * Loads the configuration from .env file
     *
     * Define MASTER_ENV_PATH in .env to merge
     * it with the Hub Env File
     *
     * @param $configPath string
     */
    public function __construct($configPath)
    {
        $configuration = new EnvironmentHandler($configPath);
        $configuration->load();
        $configuration->required([
            'HUB_BASE_URL',
            'HUB_APP_ORIGIN_URL',
            'HUB_APP_SECRET_KEY'
        ])->notEmpty();
        $this->loadMasterEnvFile();
    }

    private function loadMasterEnvFile()
    {
        $masterEnvFilePath = getenv('MASTER_ENV_PATH');

        if ($masterEnvFilePath) {
            $configuration = new EnvironmentHandler($masterEnvFilePath);
            $configuration->overload();
        }
    }

    /**
     * Return value set in .env
     *
     * @param $key
     * @param bool $defaultTo
     * @return array|false|null|string
     */
    public static function get($key, $defaultTo = false)
    {
        $value = getenv($key);
        return $value ? $value : $defaultTo;
    }
}
