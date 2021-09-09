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

use Exception;
use Valkyrja\Auth\Adapter;
use Valkyrja\Auth\Auth as Contract;
use Valkyrja\Auth\AuthenticatedUsers;
use Valkyrja\Auth\Constants\Header;
use Valkyrja\Auth\Exceptions\InvalidAuthenticationException;
use Valkyrja\Auth\Exceptions\InvalidPasswordConfirmationException;
use Valkyrja\Auth\Exceptions\InvalidRegistrationException;
use Valkyrja\Auth\Gate;
use Valkyrja\Auth\LockableUser;
use Valkyrja\Auth\Repository;
use Valkyrja\Auth\TokenizedRepository;
use Valkyrja\Auth\User;
use Valkyrja\Container\Container;
use Valkyrja\Http\Request;
use Valkyrja\Support\Type\Cls;

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
     * The container service.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The request.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The config.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * The default adapter.
     *
     * @var string
     */
    protected string $defaultAdapter;

    /**
     * The default repository.
     *
     * @var string
     */
    protected string $defaultRepository;

    /**
     * The default gate.
     *
     * @var string
     */
    protected string $defaultGate;

    /**
     * The default user entity.
     *
     * @var string
     */
    protected string $defaultUserEntity;

    /**
     * Auth constructor.
     *
     * @param Container $container The container
     * @param Request   $request   The request
     * @param array     $config    The config
     */
    public function __construct(Container $container, Request $request, array $config)
    {
        $this->container         = $container;
        $this->request           = $request;
        $this->config            = $config;
        $this->defaultAdapter    = $config['adapter'];
        $this->defaultRepository = $config['repository'];
        $this->defaultGate       = $config['gate'];
        $this->defaultUserEntity = $config['userEntity'];

        $this->tryAuthenticating();
    }

    /**
     * Set the config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get an adapter by name.
     *
     * @param string|null $name [optional] The adapter
     *
     * @return Adapter
     */
    public function getAdapter(string $name = null): Adapter
    {
        $name ??= $this->defaultAdapter;

        return self::$adapters[$name]
            ?? self::$adapters[$name] = $this->__getAdapter($name);
    }

    /**
     * Get a repository by user entity name.
     *
     * @param string|null $user    [optional] The user
     * @param string|null $adapter [optional] The adapter
     *
     * @return Repository
     */
    public function getRepository(string $user = null, string $adapter = null): Repository
    {
        /** @var User|string $user */
        /** @var Repository $repository */
        $user ??= $this->defaultUserEntity;
        $name = $user::getAuthRepository() ?? $this->defaultRepository;

        return self::$repositories[$name]
            ?? self::$repositories[$name] = $this->__getRepository($name, $user, $adapter);
    }

    /**
     * Get a gate by name.
     *
     * @param string|null $name    [optional] The name
     * @param string|null $user    [optional] The user
     * @param string|null $adapter [optional] The adapter
     *
     * @return Gate
     */
    public function getGate(string $name = null, string $user = null, string $adapter = null): Gate
    {
        $name ??= $this->defaultGate;

        return self::$gates[$name]
            ?? self::$gates[$name] = $this->__getGate($name, $user, $adapter);
    }

    /**
     * Get a request with auth token header.
     *
     * @param Request     $request The request
     * @param string|null $user    [optional] The user
     * @param string|null $adapter [optional] The adapter
     *
     * @return Request
     */
    public function requestWithAuthToken(Request $request, string $user = null, string $adapter = null): Request
    {
        return $request->withHeader(
            Header::AUTH_TOKEN,
            $this->getRepository($user, $adapter)->getUser()::asTokenized()
        );
    }

    /**
     * Get a request without auth token header.
     *
     * @param Request $request The request
     *
     * @return Request
     */
    public function requestWithoutAuthToken(Request $request): Request
    {
        return $request->withoutHeader(Header::AUTH_TOKEN);
    }

    /**
     * Determine if a user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->getRepository()->isAuthenticated();
    }

    /**
     * Get the authenticated user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->getRepository()->getUser();
    }

    /**
     * Set the authenticated user.
     *
     * @param User $user The user
     *
     * @return static
     */
    public function setUser(User $user): self
    {
        $this->getRepository()->setUser($user);

        return $this;
    }

    /**
     * Get the authenticated users.
     *
     * @return AuthenticatedUsers
     */
    public function getUsers(): AuthenticatedUsers
    {
        return $this->getRepository()->getUsers();
    }

    /**
     * Set the authenticated users.
     *
     * @param AuthenticatedUsers $users The users
     *
     * @return static
     */
    public function setUsers(AuthenticatedUsers $users): self
    {
        $this->getRepository()->setUsers($users);

        return $this;
    }

    /**
     * Authenticate a user with credentials.
     *
     * @param User $user The user
     *
     * @throws InvalidAuthenticationException
     *
     * @return static
     */
    public function authenticate(User $user): self
    {
        $this->getRepository()->authenticate($user);

        return $this;
    }

    /**
     * Authenticate a user from an active session.
     *
     * @throws InvalidAuthenticationException
     *
     * @return static
     */
    public function authenticateFromSession(): self
    {
        $this->getRepository()->authenticateFromSession();

        return $this;
    }

    /**
     * Authenticate a user from a request.
     *
     * @param Request $request The request
     *
     * @throws InvalidAuthenticationException
     *
     * @return static
     */
    public function authenticateFromRequest(Request $request): self
    {
        $this->getRepository()->authenticateFromRequest($request);

        return $this;
    }

    /**
     * Un-authenticate any active users.
     *
     * @param User|null $user [optional] The user to un-authenticate
     *
     * @return static
     */
    public function unAuthenticate(User $user = null): self
    {
        $this->getRepository()->unAuthenticate($user);

        return $this;
    }

    /**
     * Set the authenticated user in the session.
     *
     * @return static
     */
    public function setSession(): self
    {
        $this->getRepository()->setSession();

        return $this;
    }

    /**
     * Unset the authenticated user from the session.
     *
     * @return static
     */
    public function unsetSession(): self
    {
        $this->getRepository()->unsetSession();

        return $this;
    }

    /**
     * Register a new user.
     *
     * @param User $user The user
     *
     * @throws InvalidRegistrationException
     *
     * @return static
     */
    public function register(User $user): self
    {
        $this->getRepository()->register($user);

        return $this;
    }

    /**
     * Forgot password.
     *
     * @param User $user The user
     *
     * @return static
     */
    public function forgot(User $user): self
    {
        $this->getRepository()->forgot($user);

        return $this;
    }

    /**
     * Reset a user's password.
     *
     * @param string $resetToken The reset token
     * @param string $password   The password
     *
     * @return static
     */
    public function reset(string $resetToken, string $password): self
    {
        $this->getRepository()->reset($resetToken, $password);

        return $this;
    }

    /**
     * Lock a user.
     *
     * @param LockableUser $user The user
     *
     * @return static
     */
    public function lock(LockableUser $user): self
    {
        $this->getRepository()->lock($user);

        return $this;
    }

    /**
     * Unlock a user.
     *
     * @param LockableUser $user The user
     *
     * @return static
     */
    public function unlock(LockableUser $user): self
    {
        $this->getRepository()->unlock($user);

        return $this;
    }

    /**
     * Confirm the current user's password.
     *
     * @param string $password The password
     *
     * @throws InvalidPasswordConfirmationException
     *
     * @return static
     */
    public function confirmPassword(string $password): self
    {
        $this->getRepository()->confirmPassword($password);

        return $this;
    }

    /**
     * Determine if a re-authentication needs to occur.
     *
     * @return bool
     */
    public function isReAuthenticationRequired(): bool
    {
        return $this->getRepository()->isReAuthenticationRequired();
    }

    /**
     * Try authenticating.
     *
     * @return void
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
        } catch (Exception $exception) {
        }
    }

    /**
     * Get an adapter by name.
     *
     * @param string $name The adapter
     *
     * @return Adapter
     */
    protected function __getAdapter(string $name): Adapter
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Adapter::class,
            [
                $this->config,
            ]
        );
    }

    /**
     * Get a repository by user entity name.
     *
     * @param string      $name    The name
     * @param string      $user    The user
     * @param string|null $adapter [optional] The adapter
     *
     * @return Repository
     */
    protected function __getRepository(string $name, string $user, string $adapter = null): Repository
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Cls::inherits($name, TokenizedRepository::class) ? TokenizedRepository::class : Repository::class,
            [
                $this->getAdapter($adapter),
                $user,
                $this->config,
            ]
        );
    }

    /**
     * Get a gate by name.
     *
     * @param string      $name    The name
     * @param string|null $user    [optional] The user
     * @param string|null $adapter [optional] The adapter
     *
     * @return Gate
     */
    protected function __getGate(string $name, string $user = null, string $adapter = null): Gate
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Gate::class,
            [
                $this->getRepository($user, $adapter),
                $this->config,
            ]
        );
    }
}
