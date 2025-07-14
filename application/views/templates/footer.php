</div> <!-- End of .container-main from header -->

<footer class="footer mt-auto py-3 bg-dark">
    <div class="container text-center">
        <span class="text-light">Mi Aplicación de Agentes &copy; <?php echo date('Y'); ?></span>
    </div>
</footer>

<!-- jQuery is now loaded in header.php -->

<!-- Bootstrap 5.3.3 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS is loaded specifically in the agentes/index view where it's needed -->
<!-- This keeps other pages lighter. -->

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
