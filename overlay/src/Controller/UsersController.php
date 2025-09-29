<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;

class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->request->allowMethod(['get','post']);
        $this->viewBuilder()->setLayout('default');
    }

    public function loginForm()
    {
        // Render templates/Users/login.php
    }

    public function login()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $email = strtolower(trim((string)$this->request->getData('email', '')));
        $pass  = (string)$this->request->getData('password', '');

        // Cargar usuarios desde config/users.php
        $usersFile = CONFIG . 'users.php';
        if (!file_exists($usersFile)) {
            $this->response = $this->response->withStatus(500);
            echo json_encode(['ok'=>false,'error'=>'Falta config/users.php']);
            return;
        }
        $users = require $usersFile;

        if (!isset($users[$email]) || ($users[$email]['password'] ?? null) !== $pass) {
            $this->response = $this->response->withStatus(401);
            echo json_encode(['ok'=>false,'error'=>'Credenciales inválidas']);
            return;
        }

        $profile = [
            'email' => $email,
            'name'  => $users[$email]['name'] ?? 'User',
            'role'  => $users[$email]['role'] ?? 'user',
        ];
        $token = base64_encode(hash('sha256', $email . microtime(true), true));

        echo json_encode(['ok'=>true,'token'=>$token,'profile'=>$profile]);
    }
}
