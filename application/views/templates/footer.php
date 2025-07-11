</div> <!-- End of .container-main from header -->

<footer class="footer mt-auto py-3 bg-dark">
    <div class="container text-center">
        <span class="text-light">Mi Aplicación de Agentes &copy; <?php echo date('Y'); ?></span>
    </div>
</footer>

<!-- jQuery (ensure it's loaded before Bootstrap JS and other scripts) -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script> <!-- Updated to a newer version of jQuery often used with newer DataTables -->

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Placeholder for page-specific scripts -->
<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?php echo site_url('assets/js/'.$script); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- General site-wide scripts can go here -->
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Bootstrap tooltips if used
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Auto-dismiss alerts after some time (optional)
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000); // 5 seconds
    });
</script>

</body>
</html>
