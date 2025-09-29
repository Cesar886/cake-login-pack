<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Login</title>
<style>
  :root{--bg:#0f172a;--panel:#0b1220;--border:#334155;--text:#e2e8f0;--muted:#94a3b8;--brand:#2563eb;}
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:grid;place-items:center;background:var(--bg);color:var(--text);font:16px/1.5 system-ui,Segoe UI,Roboto,Ubuntu,sans-serif}
  form{background:var(--panel);padding:24px;border:1px solid var(--border);border-radius:12px;min-width:320px;max-width:360px}
  h2{margin:0 0 12px 0}
  label{display:block;margin-top:10px;font-size:14px;color:var(--muted)}
  input,button{width:100%;padding:10px;border-radius:8px;border:1px solid var(--border);background:#0a1020;color:var(--text)}
  input:focus{outline:none;border-color:#3b82f6}
  button{margin-top:12px;border:0;background:var(--brand);cursor:pointer;font-weight:600}
  .error{color:#fca5a5;margin-top:8px;min-height:20px}
  .hint{color:var(--muted);font-size:12px;margin-top:8px}
</style>
</head>
<body>
  <form id="f" autocomplete="on">
    <h2>Iniciar sesión</h2>
    <label>Email</label>
    <input name="email" type="email" required placeholder="demo@site.com" />
    <label>Contraseña</label>
    <input name="password" type="password" required placeholder="123456" />
    <label style="display:flex;gap:8px;align-items:center;margin-top:10px">
      <input id="remember" type="checkbox" /> Recordar preferencias
    </label>
    <button type="submit">Entrar</button>
    <div class="error" id="err"></div>
    <div class="hint">Usuarios de ejemplo: demo@site.com / 123456, admin@site.com / admin</div>
  </form>

<script>
// Redirigir si ya hay sesión
const auth = sessionStorage.getItem('auth');
if (auth) location.href = '/app';

const form = document.getElementById('f');
const err  = document.getElementById('err');
const remember = document.getElementById('remember');

form.addEventListener('submit', async (e)=>{
  e.preventDefault();
  err.textContent = '';
  const data = new URLSearchParams(new FormData(form));
  try{
    const res = await fetch('/login', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: data
    });
    const json = await res.json();
    if(!res.ok || !json.ok){ err.textContent = json.error || 'Error'; return; }

    // Guardar sesión en sessionStorage
    sessionStorage.setItem('auth', JSON.stringify({ token: json.token, profile: json.profile, ts: Date.now() }));

    // Preferencias persistentes
    if (remember.checked) {
      localStorage.setItem('prefs', JSON.stringify({ theme:'dark', lastEmail: json.profile.email }));
    }
    location.href = '/app';
  }catch(e){ err.textContent = 'Fallo de red'; }
});
</script>
</body>
</html>
