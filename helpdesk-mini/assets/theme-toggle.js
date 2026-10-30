// flips light/dark mode on every [data-theme-toggle] button and
// remembers the choice for next time
document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
  button.addEventListener('click', function () {
    var root = document.documentElement;
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';

    root.setAttribute('data-theme', next);

    try {
      localStorage.setItem('theme', next);
    } catch (e) {
      // save failed (private browsing?) - toggle still works, just won't persist
    }
  });
});
