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

    public function adminPanel()
    {
        // Verificar que sea admin (simulado con sessionStorage en el frontend)
        $this->viewBuilder()->setLayout('default');
    }

    public function getUsersList()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $usersFile = CONFIG . 'users.php';
        if (!file_exists($usersFile)) {
            $this->response = $this->response->withStatus(500);
            echo json_encode(['ok'=>false,'error'=>'Falta config/users.php']);
            return;
        }

        $users = require $usersFile;
        $usersList = [];
        
        foreach ($users as $email => $userData) {
            $usersList[] = [
                'email' => $email,
                'name' => $userData['name'],
                'role' => $userData['role']
            ];
        }

        echo json_encode(['ok'=>true,'users'=>$usersList]);
    }

    public function createUser()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $email = strtolower(trim((string)$this->request->getData('email', '')));
        $name = trim((string)$this->request->getData('name', ''));
        $password = (string)$this->request->getData('password', '');
        $role = trim((string)$this->request->getData('role', 'user'));

        // Validaciones básicas
        if (empty($email) || empty($name) || empty($password)) {
            $this->response = $this->response->withStatus(400);
            echo json_encode(['ok'=>false,'error'=>'Todos los campos son requeridos']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response = $this->response->withStatus(400);
            echo json_encode(['ok'=>false,'error'=>'Email inválido']);
            return;
        }

        $usersFile = CONFIG . 'users.php';
        if (!file_exists($usersFile)) {
            $this->response = $this->response->withStatus(500);
            echo json_encode(['ok'=>false,'error'=>'Falta config/users.php']);
            return;
        }

        $users = require $usersFile;

        // Verificar si el usuario ya existe
        if (isset($users[$email])) {
            $this->response = $this->response->withStatus(400);
            echo json_encode(['ok'=>false,'error'=>'El usuario ya existe']);
            return;
        }

        // Agregar nuevo usuario
        $users[$email] = [
            'password' => $password,
            'name' => $name,
            'role' => $role
        ];

        // Guardar en archivo
        $this->saveUsersToFile($usersFile, $users);

        echo json_encode(['ok'=>true,'message'=>'Usuario creado exitosamente']);
    }

    public function deleteUser()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $email = strtolower(trim((string)$this->request->getData('email', '')));

        if (empty($email)) {
            $this->response = $this->response->withStatus(400);
            echo json_encode(['ok'=>false,'error'=>'Email requerido']);
            return;
        }

        // No permitir eliminar admin
        if ($email === 'admin@site.com') {
            $this->response = $this->response->withStatus(400);
            echo json_encode(['ok'=>false,'error'=>'No se puede eliminar el usuario admin']);
            return;
        }

        $usersFile = CONFIG . 'users.php';
        if (!file_exists($usersFile)) {
            $this->response = $this->response->withStatus(500);
            echo json_encode(['ok'=>false,'error'=>'Falta config/users.php']);
            return;
        }

        $users = require $usersFile;

        if (!isset($users[$email])) {
            $this->response = $this->response->withStatus(404);
            echo json_encode(['ok'=>false,'error'=>'Usuario no encontrado']);
            return;
        }

        // Eliminar usuario
        unset($users[$email]);

        // Guardar en archivo
        $this->saveUsersToFile($usersFile, $users);

        echo json_encode(['ok'=>true,'message'=>'Usuario eliminado exitosamente']);
    }

    private function saveUsersToFile($filePath, $users)
    {
        $content = "<?php\n// Usuarios \"hardcodeados\" para demo rápida.\n// Añade o edita entradas en este array.\nreturn [\n";
        
        foreach ($users as $email => $userData) {
            $password = addslashes($userData['password']);
            $name = addslashes($userData['name']);
            $role = addslashes($userData['role']);
            $content .= "  '{$email}' => ['password' => '{$password}', 'name' => '{$name}', 'role' => '{$role}'],\n";
        }
        
        $content .= "];\n";
        
        file_put_contents($filePath, $content);
    }
}
