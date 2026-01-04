<footer class="footer bg-white py-3 mt-auto border-top shadow-sm">
  <div class="container text-center text-muted small">
    <p class="mb-0">
      &copy; <?= date('Y') ?> Aplikasi Presensi - <strong>Kecamatan Bojongsari</strong><br>
      <span class="d-none d-sm-inline">SIPREKEBO</span>
    </p>
  </div>
</footer>

<!-- JS App -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Feather Icons Init -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  });
</script>

</body>
</html>
