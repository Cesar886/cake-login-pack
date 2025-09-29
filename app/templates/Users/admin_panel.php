<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Panel de Administración</title>
<style>
  :root{--bg:#0f172a;--panel:#0b1220;--border:#334155;--text:#e2e8f0;--muted:#94a3b8;--brand:#2563eb;--danger:#dc2626;--success:#16a34a;}
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;background:var(--bg);color:var(--text);font:16px/1.5 system-ui,Segoe UI,Roboto,Ubuntu,sans-serif;padding:20px}
  .container{max-width:1200px;margin:0 auto}
  .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding:20px;background:var(--panel);border:1px solid var(--border);border-radius:12px}
  .user-info{color:var(--muted)}
  .logout-btn{background:var(--danger);color:white;border:0;padding:8px 16px;border-radius:6px;cursor:pointer}
  .section{background:var(--panel);padding:24px;border:1px solid var(--border);border-radius:12px;margin-bottom:20px}
  .section h2{margin:0 0 20px 0;color:var(--text)}
  .form-grid{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end}
  .form-group{display:flex;flex-direction:column}
  label{font-size:14px;color:var(--muted);margin-bottom:4px}
  input,select,button{padding:10px;border-radius:8px;border:1px solid var(--border);background:#0a1020;color:var(--text)}
  input:focus,select:focus{outline:none;border-color:#3b82f6}
  .btn{cursor:pointer;font-weight:600;border:0}
  .btn-primary{background:var(--brand);color:white}
  .btn-danger{background:var(--danger);color:white}
  .btn-success{background:var(--success);color:white}
  .users-list{margin-top:20px}
  .user-card{display:flex;justify-content:space-between;align-items:center;padding:16px;border:1px solid var(--border);border-radius:8px;margin-bottom:12px}
  .user-card:last-child{margin-bottom:0}
  .user-info-card{flex:1}
  .user-name{font-weight:600;margin-bottom:4px}
  .user-email{color:var(--muted);font-size:14px}
  .user-role{display:inline-block;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600;margin-top:4px}
  .role-admin{background:#dc262633;color:#fca5a5}
  .role-user{background:#16a34a33;color:#86efac}
  .message{padding:12px;border-radius:8px;margin-bottom:16px}
  .message.success{background:#16a34a33;color:#86efac;border:1px solid #16a34a}
  .message.error{background:#dc262633;color:#fca5a5;border:1px solid #dc2626}
  .hidden{display:none}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h1>Panel de Administración</h1>
        <div class="user-info">Bienvenido, <span id="adminName">Admin</span></div>
      </div>
      <button class="logout-btn" onclick="logout()">Cerrar Sesión</button>
    </div>

    <div id="message" class="message hidden"></div>

    <div class="section">
      <h2>Crear Nuevo Usuario</h2>
      <form id="createUserForm" class="form-grid">
        <div class="form-group">
          <label>Email</label>
          <input name="email" type="email" required placeholder="usuario@ejemplo.com" />
        </div>
        <div class="form-group">
          <label>Nombre</label>
          <input name="name" type="text" required placeholder="Nombre completo" />
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input name="password" type="password" required placeholder="Contraseña" />
        </div>
        <div class="form-group">
          <label>Rol</label>
          <select name="role">
            <option value="user">Usuario</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-success">Crear Usuario</button>
      </form>
    </div>

    <div class="section">
      <h2>Usuarios Existentes</h2>
      <div id="usersList" class="users-list">
        <!-- Los usuarios se cargarán dinámicamente -->
      </div>
    </div>
  </div>

<script>
// Verificar autenticación admin
const auth = sessionStorage.getItem('auth');
if (!auth) {
  location.href = '/';
}

const authData = JSON.parse(auth);
if (authData.profile.role !== 'admin') {
  alert('Acceso denegado. Solo administradores pueden acceder.');
  location.href = '/app';
}

document.getElementById('adminName').textContent = authData.profile.name;

// Cargar usuarios al inicio
loadUsers();

// Formulario crear usuario
document.getElementById('createUserForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  
  try {
    const response = await fetch('/admin/create-user', {
      method: 'POST',
      body: new URLSearchParams(formData)
    });
    
    const result = await response.json();
    
    if (result.ok) {
      showMessage(result.message, 'success');
      e.target.reset();
      loadUsers();
    } else {
      showMessage(result.error, 'error');
    }
  } catch (error) {
    showMessage('Error de conexión', 'error');
  }
});

async function loadUsers() {
  try {
    const response = await fetch('/admin/users-list');
    const result = await response.json();
    
    if (result.ok) {
      displayUsers(result.users);
    } else {
      showMessage('Error cargando usuarios: ' + result.error, 'error');
      // Mostrar usuarios por defecto en caso de error
      const defaultUsers = [
        { email: 'demo@site.com', name: 'Demo', role: 'user' },
        { email: 'admin@site.com', name: 'Admin', role: 'admin' }
      ];
      displayUsers(defaultUsers);
    }
  } catch (error) {
    console.error('Error cargando usuarios:', error);
    showMessage('Error de conexión al cargar usuarios', 'error');
    // Mostrar usuarios por defecto
    const defaultUsers = [
      { email: 'demo@site.com', name: 'Demo', role: 'user' },
      { email: 'admin@site.com', name: 'Admin', role: 'admin' }
    ];
    displayUsers(defaultUsers);
  }
}

function displayUsers(users) {
  const usersList = document.getElementById('usersList');
  usersList.innerHTML = '';
  
  users.forEach(user => {
    const userCard = document.createElement('div');
    userCard.className = 'user-card';
    
    userCard.innerHTML = `
      <div class="user-info-card">
        <div class="user-name">${user.name}</div>
        <div class="user-email">${user.email}</div>
        <span class="user-role role-${user.role}">${user.role.toUpperCase()}</span>
      </div>
      ${user.email !== 'admin@site.com' ? 
        `<button class="btn btn-danger" onclick="deleteUser('${user.email}')">Eliminar</button>` : 
        '<span style="color: var(--muted); font-size: 12px;">Protegido</span>'
      }
    `;
    
    usersList.appendChild(userCard);
  });
}

async function deleteUser(email) {
  if (!confirm(`¿Estás seguro de eliminar el usuario ${email}?`)) {
    return;
  }
  
  try {
    const response = await fetch('/admin/delete-user', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `email=${encodeURIComponent(email)}`
    });
    
    const result = await response.json();
    
    if (result.ok) {
      showMessage(result.message, 'success');
      loadUsers();
    } else {
      showMessage(result.error, 'error');
    }
  } catch (error) {
    showMessage('Error de conexión', 'error');
  }
}

function showMessage(text, type) {
  const messageEl = document.getElementById('message');
  messageEl.textContent = text;
  messageEl.className = `message ${type}`;
  messageEl.classList.remove('hidden');
  
  setTimeout(() => {
    messageEl.classList.add('hidden');
  }, 5000);
}

function logout() {
  sessionStorage.removeItem('auth');
  localStorage.removeItem('prefs');
  location.href = '/';
}
</script>
</body>
</html>