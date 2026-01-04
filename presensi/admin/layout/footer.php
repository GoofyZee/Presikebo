<footer class="footer bg-light py-3 mt-auto border-top">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start text-muted small">
        <p class="mb-0">
          &copy; <?= date('Y') ?> 
          <a href="https://unpam.ac.id" class="text-decoration-none text-muted fw-semibold" target="_blank" rel="noopener noreferrer">Kerja Praktek Unpam</a>
        </p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <ul class="list-inline mb-0 small">
          <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Support</a></li>
          <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Help</a></li>
          <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Privacy</a></li>
          <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Terms</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>

<!-- Script JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (typeof feather !== 'undefined') {
      feather.replace();
    }

    // Highlight active nav
    const currentPath = location.pathname.split('/').pop();
    const links = document.querySelectorAll('.nav-link, .dropdown-item');
    links.forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;
      const hrefPath = href.split('/').pop();
      if (hrefPath === currentPath) {
        link.classList.add('active');
        const parentDropdown = link.closest('.dropdown-menu');
        if (parentDropdown) {
          const dropdownToggle = parentDropdown.previousElementSibling;
          if (dropdownToggle) dropdownToggle.classList.add('active');
        }
      }
    });
  });
</script>

</body>
</html>
