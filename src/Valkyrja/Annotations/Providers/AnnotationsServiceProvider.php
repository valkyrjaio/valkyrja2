<?php

namespace Valkyrja\Annotations\Providers;

use Valkyrja\Annotations\Annotations;
use Valkyrja\Annotations\AnnotationsParser;
use Valkyrja\Console\Annotations\CommandAnnotations;
use Valkyrja\Container\Annotations\ContainerAnnotations;
use Valkyrja\Container\Enums\CoreComponent;
use Valkyrja\Container\Service;
use Valkyrja\Events\Annotations\ListenerAnnotations;
use Valkyrja\Routing\Annotations\RouteAnnotations;
use Valkyrja\Support\ServiceProvider;

/**
 * Class AnnotationsServiceProvider.
 *
 * @author Melech Mizrachi
 */
class AnnotationsServiceProvider extends ServiceProvider
{
    /**
     * What services are provided.
     *
     * @var array
     */
    public static $provides = [
        CoreComponent::ANNOTATIONS_PARSER,
        CoreComponent::ANNOTATIONS,
        CoreComponent::CONTAINER_ANNOTATIONS,
        CoreComponent::LISTENER_ANNOTATIONS,
        CoreComponent::COMMAND_ANNOTATIONS,
        CoreComponent::ROUTE_ANNOTATIONS,
    ];

    /**
     * Publish the service provider.
     *
     * @return void
     */
    public function publish(): void
    {
        $this->bindAnnotationsParser();
        $this->bindAnnotations();
        $this->bindContainerAnnotations();
        $this->bindListenerAnnotations();
        $this->bindCommandAnnotations();
        $this->bindRouteAnnotations();
    }

    /**
     * Bind the annotations parser.
     *
     * @return void
     */
    protected function bindAnnotationsParser(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::ANNOTATIONS_PARSER)
                ->setClass(AnnotationsParser::class)
                ->setDependencies([CoreComponent::CONFIG])
                ->setSingleton(true)
        );
    }

    /**
     * Bind the annotations.
     *
     * @return void
     */
    protected function bindAnnotations(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::ANNOTATIONS)
                ->setClass(Annotations::class)
                ->setDependencies([CoreComponent::ANNOTATIONS_PARSER])
                ->setSingleton(true)
        );
    }

    /**
     * Bind the container annotations.
     *
     * @return void
     */
    protected function bindContainerAnnotations(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::CONTAINER_ANNOTATIONS)
                ->setClass(ContainerAnnotations::class)
                ->setDependencies([CoreComponent::ANNOTATIONS_PARSER])
                ->setSingleton(true)
        );
    }

    /**
     * Bind the listener annotations.
     *
     * @return void
     */
    protected function bindListenerAnnotations(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::LISTENER_ANNOTATIONS)
                ->setClass(ListenerAnnotations::class)
                ->setDependencies([CoreComponent::ANNOTATIONS_PARSER])
                ->setSingleton(true)
        );
    }

    /**
     * Bind the command annotations.
     *
     * @return void
     */
    protected function bindCommandAnnotations(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::COMMAND_ANNOTATIONS)
                ->setClass(CommandAnnotations::class)
                ->setDependencies([CoreComponent::ANNOTATIONS_PARSER])
                ->setSingleton(true)
        );
    }

    /**
     * Bind the route annotations.
     *
     * @return void
     */
    protected function bindRouteAnnotations(): void
    {
        $this->app->container()->bind(
            (new Service())
                ->setId(CoreComponent::ROUTE_ANNOTATIONS)
                ->setClass(RouteAnnotations::class)
                ->setDependencies([CoreComponent::ANNOTATIONS_PARSER])
                ->setSingleton(true)
        );
    }
}
