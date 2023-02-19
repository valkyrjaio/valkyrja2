<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Auth\Gates;

use Valkyrja\Auth\Auth;
use Valkyrja\Auth\Gate as Contract;
use Valkyrja\Auth\Policy;
use Valkyrja\Auth\Repository;

/**
 * Class Gate.
 *
 * @author Melech Mizrachi
 */
class Gate implements Contract
{
    /**
     * The auth service.
     */
    protected Auth $auth;

    /**
     * The repository.
     */
    protected Repository $repository;

    /**
     * Gate constructor.
     *
     * @param Auth       $auth       The auth service
     * @param Repository $repository The repository
     */
    public function __construct(Auth $auth, Repository $repository)
    {
        $this->auth       = $auth;
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function before(string &$action, string &$policy = null): ?bool
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function after(bool $isAuthorized, string $action, string $policy = null): ?bool
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function isAuthorized(string $action, string $policy = null): bool
    {
        if ($beforeAuthorized = $this->before($action, $policy)) {
            return $beforeAuthorized;
        }

        $isAuthorized = $this->checkIsAuthorized($action, $policy);

        return $this->after($isAuthorized, $action, $policy)
            ?? $isAuthorized;
    }

    /**
     * Check if the action/policy combo are authorized.
     *
     * @param string                    $action The action to check if authorized for
     * @param class-string<Policy>|null $policy [optional] The policy
     */
    protected function checkIsAuthorized(string $action, string $policy = null): bool
    {
        return $this->auth->getPolicy(
            $policy,
            $this->repository->getUser()::class,
            $this->repository->getAdapter()::class
        )
            ->isAuthorized($action);
    }
}
