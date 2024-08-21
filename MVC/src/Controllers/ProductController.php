<?php

namespace App\Controllers;

use App\Core\Contracts\ValidatorInterface;
use App\Core\Http\Request;
use App\Models\ProductRepository;
use App\Core\View;

readonly class ProductController
{
    public function __construct(
        private readonly ProductRepository  $repository,
        private readonly Request            $request,
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function index(): string
    {
        $products = $this->repository->list();

        return View::render('products/index', ['products' => $products]);
    }

    public function create(): string
    {
        return View::render('products/create');
    }

    public function store(): void
    {
        $data = $this->request->only(['name', 'price']);

        if (!$this->validator->validate($data, [
            'name' => 'required|string',
            'price' => 'required|numeric',
        ])) {
            // Обработка ошибок валидации
            return;
        }

        $this->repository->create($data);
        header('Location: /');
    }

    public function delete(): void
    {
        $id = $this->request->input('id', FILTER_SANITIZE_NUMBER_INT);
        $this->repository->delete((int)$id);
        header('Location: /');
    }
}