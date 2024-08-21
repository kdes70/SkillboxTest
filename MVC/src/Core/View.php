<?php

namespace App\Core;

use App\Core\Contracts\ViewInterface;
use Exception;

class View implements ViewInterface
{
    private string $templatePath;
    private string $layoutPath;
    private array $data = [];

    public function __construct(string $templatePath, string $layoutPath = null)
    {
        $this->templatePath = $templatePath;
        $this->layoutPath = $layoutPath ?: 'views/layout.php'; // путь к основному шаблону по умолчанию
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }


    public static function render(string $template, array $data = [], string $layout = null): string
    {
        $viewPath = 'views/' . str_replace('.', '/', $template) . '.php';
        $layoutPath = $layout ?: 'views/layout.php';

        $instance = new self($viewPath, $layoutPath);
        foreach ($data as $key => $value) {
            $instance->set($key, $value);
        }

        return $instance->renderTemplate();
    }

    /**
     * @throws Exception
     */
    public function renderTemplate(): string
    {
        ob_start(); // Начинаем буферизацию вывода
        if (file_exists($this->templatePath)) {
            extract($this->data); // Преобразует массив в переменные
            include $this->templatePath;
        } else {
            throw new Exception("Шаблон не найден: " . $this->templatePath);
        }
        $content = ob_get_clean(); // Получаем содержимое буфера и очищаем его

        if (file_exists($this->layoutPath)) {
            ob_start();
            include $this->layoutPath;
            return ob_get_clean(); // Возвращаем сгенерированный контент
        }

        throw new Exception("Layout не найден: " . $this->layoutPath);
    }
}