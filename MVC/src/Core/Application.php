<?php

namespace App\Core;

use App\Core\Contracts\RouterInterface;
use Throwable;

readonly class Application
{
    public function __construct(
        private readonly Container       $container,
        private readonly RouterInterface $router,
        private readonly array           $config
    )
    {
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch($this->container);
            echo $response;
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    private function handleException(Throwable $e): void
    {
        // Здесь можно добавить логирование
        if ($this->config['debug'] ?? false) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
}