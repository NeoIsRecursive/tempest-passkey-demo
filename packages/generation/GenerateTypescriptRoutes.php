<?php

declare(strict_types=1);

namespace Generation;

use Tempest\Console\ConsoleCommand;
use Tempest\Router\RouteConfig;
use Tempest\Router\Routing\Construction\DiscoveredRoute;
use Tempest\Support\Arr\ImmutableArray;
use Tempest\Support\Arr\MutableArray;

use function Tempest\Support\Filesystem\write_file;
use function Tempest\Support\Str\to_lower_case;

final class GenerateTypescriptRoutes
{
    public function __construct(
        private RouteConfig $routeConfig,
    ) {}

    #[ConsoleCommand(name: 'generate:ts-routes', description: 'Generate TypeScript routes file')]
    public function __invoke(): void
    {
        /** @var MutableArray<string,DiscoveredRoute> */
        $routes = new MutableArray();

        foreach ($this->routeConfig->dynamicRoutes as $routesForMethod) {
            foreach ($routesForMethod as $route) {
                $routes->set($route->uri, $route);
            }
        }

        foreach ($this->routeConfig->staticRoutes as $routesForMethod) {
            foreach ($routesForMethod as $route) {
                $routes->set($route->uri, $route);
            }
        }

        $data = $routes
            ->toImmutableArray()
            ->map(static fn (DiscoveredRoute $route) => new Method(
                class: $route->handler->getDeclaringClass()->getShortName(),
                action: $route->handler->getName(),
                params: self::getParametersFromRoute($route),
                method: to_lower_case($route->method->value),
                uri: $route->uri,
            ))
            ->groupBy(static fn (Method $route) => $route->class);

        $functionTemplate = <<<'TS'
        const builder = (
          url: string,
          parameters?: Record<string, string | boolean | number>,
        ) => {
          if (!parameters) return url;

          const remainingParams: Record<string, string> = {};

          for (const [key, value] of Object.entries(parameters)) {
            const placeholder = `{${key}}`;

            if (url.includes(placeholder)) {
              url = url.replace(placeholder, encodeURIComponent(String(value)));
            } else {
              remainingParams[key] = String(value);
            }
          }

          const searchParams = new URLSearchParams(remainingParams);
          const queryString = searchParams.toString();

          if (queryString) {
            url += (url.includes("?") ? "&" : "?") + queryString;
          }

          return url;
        };
        TS;

        $objectTemplate = <<<'TS'
        export const {{NAME}} = {
        {{METHODS}}
        } as const;
        TS;

        $methodTemplate = <<<'TS'
          {{ACTION}}: ({{PARAMS}}) => {
            const url = `{{URI}}`;

            return { method: '{{HTTP_METHOD}}', url: builder(url, parameters) } as const;
          },
        TS;

        $generated = $data->map(static function (array $methods, string $class) use ($objectTemplate, $methodTemplate) {
            $methodString = new ImmutableArray($methods)->map(
                static fn ($method) => strtr($methodTemplate, [
                    '{{ACTION}}' => $method->action,
                    '{{PARAMS}}' => $method->stringifiedParams,
                    '{{URI}}' => $method->uri,
                    '{{HTTP_METHOD}}' => $method->method,
                ]),
            );

            return strtr($objectTemplate, [
                '{{NAME}}' => $class,
                '{{METHODS}}' => $methodString->implode("\n")->toString(),
            ]);
        })->prepend($functionTemplate)->implode("\n\n");

        write_file(__DIR__ . '/routes.gen.ts', $generated);
    }

    /**
     * @return string[]
     */
    private static function getParametersFromRoute(DiscoveredRoute $route): array
    {
        /** @var string[] */
        $params = [];
        foreach ($route->parameters as $param) {
            assert(is_string($param), 'Route parameter must be a string');

            $paramType = $route->handler->getParameter($param)?->getType()?->getName() ?? 'string';

            $type = match ($paramType) {
                'int' => 'int',
                'float' => 'float',
                'bool' => 'bool',
                default => 'string',
            };

            $params[] = "{$param}: {$type}";
        }

        $params[] = '[key:string]: string | number | boolean';

        return $params;
    }
}
