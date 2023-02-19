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

namespace Valkyrja\Auth\Managers;

use Valkyrja\Auth\Adapter;
use Valkyrja\Auth\Auth as Contract;
use Valkyrja\Auth\AuthenticatedUsers;
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\Constants\HeaderValue;
use Valkyrja\Auth\Exceptions\AuthRuntimeException;
use Valkyrja\Auth\Exceptions\InvalidAuthenticationException;
use Valkyrja\Auth\Factory;
use Valkyrja\Auth\Gate;
use Valkyrja\Auth\LockableUser;
use Valkyrja\Auth\Policy;
use Valkyrja\Auth\Repository;
use Valkyrja\Auth\TokenizedRepository;
use Valkyrja\Auth\User;
use Valkyrja\Http\Constants\Header;
use Valkyrja\Http\Request;

/**
 * Class Auth.
 *
 * @author Melech Mizrachi
 */
class Auth implements Contract
{
    /**
     * The adapters.
     *
     * @var Adapter[]
     */
    protected static array $adapters = [];

    /**
     * Repositories.
     *
     * @var Repository[]
     */
    protected static array $repositories = [];

    /**
     * The gates cache.
     *
     * @var Gate[]
     */
    protected static array $gates = [];

    /**
     * The policies cache.
     *
     * @var Policy[]
     */
    protected static array $policies = [];

    /**
     * The default adapter.
     *
     * @var class-string<Adapter>
     */
    protected string $defaultAdapter;

    /**
     * The default repository.
     *
     * @var class-string<Repository>
     */
    protected string $defaultRepository;

    /**
     * The default gate.
     *
     * @var class-string<Gate>
     */
    protected string $defaultGate;

    /**
     * The default policy.
     *
     * @var class-string<Policy>
     */
    protected string $defaultPolicy;

    /**
     * The default user entity.
     *
     * @var class-string<User>
     */
    protected string $defaultUserEntity;

    /**
     * Auth constructor.
     *
     * @param Factory      $factory The factory
     * @param Request      $request The request
     * @param Config|array $config  The config
     */
    public function __construct(
        protected Factory $factory,
        protected Request $request,
        protected Config|array $config
    ) {
        $this->defaultAdapter    = $config['adapter'];
        $this->defaultRepository = $config['repository'];
        $this->defaultGate       = $config['gate'];
        $this->defaultUserEntity = $config['userEntity'];
        $this->defaultPolicy     = $config['policy'];

        $this->tryAuthenticating();
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): Config|array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function getAdapter(string $name = null): Adapter
    {
        $name ??= $this->defaultAdapter;

        return self::$adapters[$name]
            ??= $this->factory->createAdapter($name, $this->config);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(string $user = null, string $adapter = null): Repository
    {
        /** @var User|string $user */
        /** @var Repository $repository */
        $user ??= $this->defaultUserEntity;
        $name = $user::getAuthRepository() ?? $this->defaultRepository;

        return self::$repositories[$name]
            ??= $this->factory->createRepository($this->getAdapter($adapter), $name, $user, $this->config);
    }

    /**
     * @inheritDoc
     */
    public function getGate(string $name = null, string $user = null, string $adapter = null): Gate
    {
        $name ??= $this->defaultGate;

        return self::$gates[$name]
            ??= $this->factory->createGate($this->getRepository($user, $adapter), $name);
    }

    /**
     * @inheritDoc
     */
    public function getPolicy(string $name = null, string $user = null, string $adapter = null): Policy
    {
        $name ??= $this->defaultPolicy;

        return static::$policies[$name]
            ??= $this->factory->createPolicy($this->getRepository($user, $adapter), $name);
    }

    /**
     * @inheritDoc
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * @inheritDoc
     */
    public function requestWithAuthToken(Request $request, string $user = null, string $adapter = null): Request
    {
        $repository = $this->getRepository($user, $adapter);

        if (! ($repository instanceof TokenizedRepository)) {
            throw new AuthRuntimeException(
                "The repository for $user should be an instance of "
                . TokenizedRepository::class
                . '. '
                . $repository::class
                . ' provided.'
            );
        }

        return $request->withHeader(
            Header::AUTHORIZATION,
            HeaderValue::BEARER . ' ' . $repository->getToken()
        );
    }

    /**
     * @inheritDoc
     */
    public function requestWithoutAuthToken(Request $request): Request
    {
        return $request->withoutHeader(Header::AUTHORIZATION);
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated(): bool
    {
        return $this->getRepository()->isAuthenticated();
    }

    /**
     * @inheritDoc
     */
    public function getUser(): User
    {
        return $this->getRepository()->getUser();
    }

    /**
     * @inheritDoc
     */
    public function setUser(User $user): static
    {
        $this->getRepository()->setUser($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsers(): AuthenticatedUsers
    {
        return $this->getRepository()->getUsers();
    }

    /**
     * @inheritDoc
     */
    public function setUsers(AuthenticatedUsers $users): static
    {
        $this->getRepository()->setUsers($users);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(User $user): static
    {
        $this->getRepository()->authenticate($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticateFromSession(): static
    {
        $this->getRepository()->authenticateFromSession();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticateFromRequest(Request $request): static
    {
        $this->getRepository()->authenticateFromRequest($request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unAuthenticate(User $user = null): static
    {
        $this->getRepository()->unAuthenticate($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSession(): static
    {
        $this->getRepository()->setSession();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unsetSession(): static
    {
        $this->getRepository()->unsetSession();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function register(User $user): static
    {
        $this->getRepository()->register($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forgot(User $user): static
    {
        $this->getRepository()->forgot($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reset(string $resetToken, string $password): static
    {
        $this->getRepository()->reset($resetToken, $password);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lock(LockableUser $user): static
    {
        $this->getRepository()->lock($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unlock(LockableUser $user): static
    {
        $this->getRepository()->unlock($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function confirmPassword(string $password): static
    {
        $this->getRepository()->confirmPassword($password);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isReAuthenticationRequired(): bool
    {
        return $this->getRepository()->isReAuthenticationRequired();
    }

    /**
     * Try authenticating.
     */
    protected function tryAuthenticating(): void
    {
        try {
            $repository = $this->getRepository();

            if ($this->config['useSession']) {
                // Try to authenticate from session
                $repository->authenticateFromSession();

                return;
            }

            // Try to login from the user session
            $repository->authenticateFromRequest($this->request);
        } catch (InvalidAuthenticationException) {
            // No need to throw an error here as we're just attempting an authentication in the constructor.
            // To determine authenticated state properly usage of isAuthenticated is recommended.
            // Could revisit adding an exception here depending on the error.
        }
    }
}
