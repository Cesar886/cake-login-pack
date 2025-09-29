<?php
// templates/Pages/home.php
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>App</title>
<link rel="preload" href="/js/auth-guard.js" as="script" />
<style>
  body{font-family:system-ui,Segoe UI,Roboto,Ubuntu,sans-serif;margin:0;background:#0b1220;color:#e2e8f0}
  header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:#0f172a;border-bottom:1px solid #334155}
  main{max-width:920px;margin:24px auto;padding:0 16px}
  button{padding:8px 12px;border:0;border-radius:8px;background:#ef4444;color:#fff;cursor:pointer}
  .card{background:#0f172a;border:1px solid #334155;border-radius:12px;padding:16px}
  .muted{color:#94a3b8}
</style>
<script src="/js/auth-guard.js"></script>
</head>
<body>
  <header>
    <strong>Demo App</strong>
    <div>
      <span id="uname" class="muted"></span>
      <button onclick="logout()">Salir</button>
    </div>
  </header>
  <main>
    <div class="card">
      <h2>Zona privada</h2>
      <p>Solo accesible con sesión en <code>sessionStorage</code>.</p>
      <pre id="dump" class="muted"></pre>
    </div>
  </main>
<script>
  const auth = JSON.parse(sessionStorage.getItem('auth') || '{}');
  document.getElementById('uname').textContent = auth?.profile?.name ? ('Hola, ' + auth.profile.name) : '';
  document.getElementById('dump').textContent = JSON.stringify(auth, null, 2);
</script>
</body>
</html>
