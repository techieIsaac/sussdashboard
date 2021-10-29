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

namespace Siwebapp\Providers;

use Phalcon\Crypt;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class CryptProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'crypt';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        /** @var string $cryptSalt */
        $cryptSalt = $di->getShared('config')->path('application.cryptSalt');

        $di->set($this->providerName, function () use ($cryptSalt) {
            $crypt = new Crypt();
            $crypt->setKey($cryptSalt);

            return $crypt;
        });
    }
}