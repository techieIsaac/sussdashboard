<?php
declare(strict_types=1);

/**
 * This file is part of Siwebapp.
 *
 * (c) Sobbayi Interactive Team <developers@sobbayi.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Siwebapp;

use Exception;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application as MvcApplication;

/**
 * Siwebapp Application
 */
class Application
{
    const APPLICATION_PROVIDER = 'bootstrap';

    /**
     * @var MvcApplication
     */
    protected $app;

    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * Project root path
     *
     * @var string
     */
    protected $rootPath;

    /**
     * @param string $rootPath
     *
     * @throws Exception
     */
    public function __construct(string $rootPath)
    {
        $this->di = new FactoryDefault();
        $this->app = $this->createApplication();
        $this->rootPath = $rootPath;

        $this->di->setShared(self::APPLICATION_PROVIDER, $this);

        $this->initializeProviders();
    }

    /**
     * Run Application
     *
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        /** @var ResponseInterface $response */
        $response = $this->app->handle($_SERVER['REQUEST_URI']);

        return (string)$response->getContent();
    }

    /**
     * Get Project root path
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @return MvcApplication
     */
    protected function createApplication(): MvcApplication
    {
        return new MvcApplication($this->di);
    }

    /**
     * @throws Exception
     */
    protected function initializeProviders(): void
    {
        $filename = $this->rootPath . '/config/providers.php';
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception('The providers initializer does not exist or is not readable.');
        }

        $providers = include_once $filename;
        foreach ($providers as $providerClass) {
            /** @var ServiceProviderInterface $provider */
            $provider = new $providerClass;
            $provider->register($this->di);
        }
    }
}
