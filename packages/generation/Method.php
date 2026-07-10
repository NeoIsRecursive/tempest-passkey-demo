<?php

declare(strict_types=1);

namespace Generation;

final class Method
{
    public function __construct(
        public readonly string $class,
        public readonly string $action,
        /** @var array<string> */
        public readonly array $params,
        public readonly string $method,
        public readonly string $uri,
    ) {}

    public string $stringifiedParams {
        get {
            $hasParams = count($this->params) > 1;

            return sprintf(
                'parameters%s {%s}',
                $hasParams
                    ? ':'
                    : '?:',
                implode('; ', $this->params),
            );
        }
    }
}
