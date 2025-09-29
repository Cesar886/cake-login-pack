(function(){
  // Guard simple de cliente
  var a = sessionStorage.getItem('auth');
  if(!a){ window.location.href = '/'; return; }
  window.logout = function(){
    sessionStorage.removeItem('auth');
    window.location.href = '/';
  };
})();
