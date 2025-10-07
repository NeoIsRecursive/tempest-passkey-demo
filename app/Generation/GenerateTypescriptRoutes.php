<?php

namespace App\Generation;

use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Router\RouteConfig;
use Tempest\Router\Routing\Construction\DiscoveredRoute;
use Tempest\Support\Arr\MutableArray;

use function Tempest\Support\arr;
use function Tempest\Support\Filesystem\write_file;
use function Tempest\Support\Str\to_lower_case;

final class GenerateTypescriptRoutes
{
    public function __construct(
        private Console $console,
        private RouteConfig $routeConfig,
    ) {}

    #[ConsoleCommand(name: 'gen:ts-routes', description: 'Generate TypeScript routes file')]
    public function __invoke(): void
    {
        /** @var MutableArray<string,\Tempest\Router\Routing\Construction\DiscoveredRoute> */
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
            ->map(function (DiscoveredRoute $route) {
                $params = self::getParametersFromRoute($route);

                $hasParams = count($params) > 1;

                return [
                    'HTTP_METHOD' => to_lower_case($route->method->value),
                    'PARAMS' => sprintf(
                        'parameters%s {%s}',
                        $hasParams
                            ? ':'
                            : '?:',
                        implode('; ', $params),
                    ),
                    'URI' => $route->uri,
                    'ACTION' => $route->handler->getName(),
                    'class' => $route->handler->getDeclaringClass()->getShortName(),
                ];
            })
            ->groupBy(fn (array $route) => $route['class']);

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

        $generated = $data->map(function (array $methods, string $class) use ($objectTemplate, $methodTemplate) {
            $methodString = arr($methods)->map(
                fn ($method) => strtr($methodTemplate, [
                    '{{ACTION}}' => $method['ACTION'],
                    '{{PARAMS}}' => $method['PARAMS'],
                    '{{URI}}' => $method['URI'],
                    '{{HTTP_METHOD}}' => $method['HTTP_METHOD'],
                ]),
            );

            return strtr($objectTemplate, [
                '{{NAME}}' => $class,
                '{{METHODS}}' => $methodString->implode("\n"),
            ]);
        })->prepend($functionTemplate)->implode("\n\n");

        write_file(__DIR__ . '/routes.gen.ts', $generated);
    }

    private static function getParametersFromRoute(DiscoveredRoute $route)
    {
        $allowedTypes = ['string', 'int', 'float', 'bool'];

        $params = [];
        foreach ($route->parameters as $param) {
            $type = $route->handler->getParameter($param)?->getType()->getName() ?? 'string';

            if (! in_array($type, $allowedTypes, true)) {
                $type = 'string';
            }

            if ($type === 'int' || $type === 'float') {
                $type = 'number';
            } elseif ($type === 'bool') {
                $type = 'boolean';
            }

            $params[] = "{$param}: {$type}";
        }

        $params[] = '[key:string]: string | number | boolean';

        return $params;
    }
}
