<?php

namespace Tests\Integration;

use \PHPUnit\Framework\TestCase;
use \Psr\Http\Message\ResponseInterface;
use \Symfony\Component\Dotenv\Dotenv;
use \Slim\App;
use \Slim\Http\Environment;
use \Slim\Http\Request;
use \Slim\Http\Response;

class BaseTestCase extends TestCase
{
    private static $pdo = null;

    public static function setUpBeforeClass()
    {
        // Load up .env vars into $_ENV/$_SERVER super globals
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env.testing');

        // Create database connection
        $dsn  = 'mysql:host=' . $_ENV['DB_HOST'] . ';';
        $user = $_ENV['DB_ROOT_USER'];
        $pass = $_ENV['DB_ROOT_PASS'];
        self::$pdo = new \PDO($dsn, $user, $pass);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        self::$pdo->exec(
            "CREATE DATABASE IF NOT EXISTS `".$_ENV['DB_NAME']."`"
        );
        self::$pdo->exec(
            "GRANT ALL PRIVILEGES ON ".$_ENV['DB_NAME'].".* TO '".$_ENV['DB_USER']."'@'%'"
        );

        // Execute Phinx migrations and seeding
        exec('vendor/bin/phinx migrate -e testing');
        exec('vendor/bin/phinx seed:run -e testing');
    }

    public static function tearDownAfterClass()
    {
        // Execute Phinx rollback
        exec('vendor/bin/phinx rollback -e testing');

        // Kill pdo connection
        self::$pdo = null;
    }

    /**
     * Used to test SLIM API end points
     * @param string $requestMethod - Http request method string (GET, POST, PUT...)
     * @param string $requestUri - The uri to test
     * @param array|null $requestData - Request data to send
     * @return ResponseInterface - Response from the Slim App
     * @throws \Throwable
     */
    public function runApp(
        string $requestMethod,
        string $requestUri,
        array $requestData = null
    ): ResponseInterface
    {
        // Create mock environment for use in making requests
        $env = Environment::mock([
            'REQUEST_METHOD' => $requestMethod,
            'REQUEST_URI'    => $requestUri,
        ]);

        // Create request using mocked environment
        $req = Request::createFromEnvironment($env);

        // Append any data passed in for the request
        if (isset($requestData)) {
            $req = $req->withParsedBody($requestData);
        }

        // Bootstrap our SLIM app
        $settings  = require __DIR__ . '/../../src/App/Settings.php';
        $app       = new App($settings);
        $container = $app->getContainer();
        require __DIR__ . '/../../src/App/Dependencies.php';
        require __DIR__ . '/../../src/App/Services.php';
        require __DIR__ . '/../../src/App/Routes.php';

        return $app->process($req, new Response());
    }

}