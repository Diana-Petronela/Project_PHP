<script>
// runs before paint, so there's no flash of the wrong theme -
// kept inline so there's no extra request to wait on
(function () {
  try {
    var stored = localStorage.getItem('theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.setAttribute('data-theme', stored || (prefersDark ? 'dark' : 'light'));
  } catch (e) {
    // localStorage can throw in private mode - light theme is a fine fallback
  }
})();
</script>
