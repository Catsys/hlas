<?php

namespace App\Controller;

use App\Service\DB;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;


class UserController
{
    private PDO $db;

    public function __construct() {
        $this->db = DB::getConnection();
    }

    /**
     * Получение пользователя по идентификатору
     */
    public function getById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'] ?? null;

        // Проверим на валидность. Невалидный uuid валит sql запрос
        if (!Uuid::isValid($id)) {
            return $response->withStatus(400);
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        }
        catch (\Throwable $e) {
            return $response->withStatus(500);
        }

        if (!$user) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            'id'          => $user['id'],
            'first_name'  => $user['first_name'],
            'second_name' => $user['last_name'],
            'birthdate'   => $user['birth_date'],
            'biography'   => $user['biography'],
            'city'        => $user['city'],
        ], JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Регистрация пользователя
     */
    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = json_decode($request->getBody()->getContents(), true);

        $birthdate = \DateTimeImmutable::createFromFormat('Y-m-d', $params['birthdate']);

        // валидация входных данных
        if (
            (!$birthdate || $birthdate->format('Y-m-d') !== $params['birthdate'])
            || (empty($params['first_name']) || !is_string($params['first_name']))
            || (empty($params['second_name']) || !is_string($params['second_name']))
            || (empty($params['city']) || !is_string($params['city']))
            || (empty($params['gender']) || !is_string($params['gender']) || !in_array($params['gender'], ['male', 'female']))
            || (empty($params['password']))
        ) {
            return $response->withStatus(400);
        }

        try {
            // Генерация UUID и хеширование пароля
            $id = Guid::uuid4()->toString();
            $passwordHash = password_hash($params['password'], PASSWORD_BCRYPT);

            // Подготовка SQL-запроса создания пользователя
            $sql = <<<SQL
            INSERT INTO users (id, first_name, last_name, birth_date, biography, city, interests, pass_hash, gender) 
                VALUES (:id, :first_name, :last_name, :birthdate, :biography, :city, :interests, :pass_hash, :gender)
            SQL;

            $stmt = $this->db->prepare($sql);

            // Выполнение запроса
            $stmt->execute([
                ':id'          => $id,
                ':first_name'  => $params['first_name'],
                ':last_name' => $params['second_name'],
                ':birthdate'   => $birthdate->format('Y-m-d'),
                ':biography'   => $params['biography'] ?? '',
                ':interests'   => $params['interests'] ?? '',
                ':gender'      => $params['gender'],
                ':city'        => $params['city'],
                ':pass_hash'    => $passwordHash,
            ]);
        }
        catch (\Throwable $e) {
            return $response->withStatus(500);
        }

        $response->getBody()->write(json_encode(['user_id' => $id]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Логин
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = json_decode($request->getBody()->getContents(), true);
        $id = $params['id'] ?? null;

        if (!Uuid::isValid($id)) {
            // Выбрасываем исключение, если UUID некорректен
            return $response->withStatus(400);
        }
        // Поиск юзера
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // нет юзера
        if (!$user) {
            return $response->withStatus(404);
        }

        // Проверка пароля
        if (!password_verify($params['password'], $user['pass_hash'])) {
            return $response->withStatus(400);
        }

        // генерация токена (заглущка)
        $token = Guid::uuid4()->toString();

        $response->getBody()->write(json_encode([
            'token' => $token
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}