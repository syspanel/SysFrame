# SysFrame

SysFrame is a minimal PHP micro-framework designed for rapid development.  
It integrates **PHP-DI**, supports **PSR-7 Request/Response**, **middleware**, **route groups**, **named routes**, and **dependency injection**.  
It is lightweight, fast, and easy to extend.

Developer: Marco Costa contato@syspanel.com.br 2025

---

## Features

- PSR-7 Request and Response support
- PHP-DI integration for dependency injection
- Routing with parameters (numeric, alphanumeric)
- Route groups and prefixes
- Named routes
- Global and route-specific middleware
- Supports GET, POST, and other HTTP methods
- Easy to extend and customize

---

## Requirements

- PHP >= 8.1
- Composer
- Web server (Apache, Nginx, PHP built-in server)

---

## Installation

Install via Composer:

\`\`\`bash
composer require sysframe/sysframe
\`\`\`

Include Composer autoload:

\`\`\`php
require 'vendor/autoload.php';
\`\`\`

---

## Basic Usage

See \`examples/\` folder for Controllers, Middlewares, and Services.

---

## Route Groups

\`\`\`php
\$app->group('/api', function(\$router) {
    \$router->get('/users', [UserController::class, 'list']);
    \$router->get('/user/{id:\d+}', [UserController::class, 'show']);
}, [AuthMiddleware::class]);
\`\`\`

---

## Named Routes

\`\`\`php
\$app->get('/user/{id:\d+}', [UserController::class, 'show'])->name('user.show');
\`\`\`

---

## Middleware

- **Global Middleware:** runs for every request
- **Route Middleware:** runs only for specific routes

\`\`\`php
// Global middleware
\$app->addMiddleware(SomeMiddleware::class);

// Route-specific
\$app->get('/admin', [AdminController::class, 'index'], [AuthMiddleware::class]);
\`\`\`

---

## Dependency Injection

SysFrame uses PHP-DI. You can inject services directly into controllers:

\`\`\`php
class UserController
{
    private UserService \$service;

    public function __construct(UserService \$service)
    {
        \$this->service = \$service;
    }

    public function show(\$request, \$response, array \$params)
    {
        \$user = \$this->service->find(\$params['id']);
        \$response->getBody()->write(json_encode(\$user));
        return \$response;
    }
}
\`\`\`

---

## PSR-7 Support

SysFrame works with **Nyholm PSR-7** or any PSR-7 compatible library.  
You can use `ServerRequestInterface` and `ResponseInterface` in all handlers and middleware.

---

## Folder Structure

\`\`\`
sysframe/
 ├── src/                  # Core framework
 ├── examples/             # Controllers, Services, Middlewares
 ├── public/               # index.php for demo
 ├── composer.json
 ├── README.md
 └── .gitignore
\`\`\`

---

## Contributing

1. Fork the repository
2. Create a new branch (\`git checkout -b feature/my-feature\`)
3. Commit your changes (\`git commit -am 'Add feature'\`)
4. Push to the branch (\`git push origin feature/my-feature\`)
5. Create a Pull Request

---

## License

MIT License

Copyright (c) 2025 Marco Costa

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
