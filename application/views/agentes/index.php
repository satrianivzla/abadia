<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo html_escape($title); ?></h1>
    <div>
        <?php $leadership_groups = ['admin', 'Gerente General', 'Gerente de Zona', 'Supervisor']; ?>
        <?php if ($this->ion_auth->in_group($leadership_groups)): ?>
            <a href="<?php echo site_url('agentes/deleted_list'); ?>" class="btn btn-warning">
                <i class="fas fa-trash-alt"></i> Papelera
            </a>
        <?php endif; ?>
        <a href="<?php echo site_url('agentes/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Agente
        </a>
    </div>
</div>

<div class="table-responsive">
    <table id="agentesTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Cédula</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded by DataTables -->
        </tbody>
    </table>
</div>

<!-- Note: jQuery is loaded in header.php -->
<!-- DataTables 2.0.8 & SweetAlert2 11 -->
<script type="text/javascript" src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Placeholder for page-specific scripts -->
<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?php echo base_url('assets/js/'.$script); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Page specific script for DataTables initialization -->
<script type="text/javascript">
$(document).ready(function () {
    var tabla = $('#agentesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "<?php echo site_url('agentes/agentes_list'); ?>", // Updated URL
            type: "POST"
        },
        // The 'data' property for each column should match a key in the JSON response from the server.
        // The library's setOutput() method with associative keys will create this.
        columns: [
            { data: "id" },
            { data: "foto_perfil" },
            { data: "cedula" },
            { data: "nombres" },
            { data: "apellidos" },
            { data: "actions" } // Column for CRUD buttons
        ],
        columnDefs: [
            {
                targets: 1, // Corresponds to the 'foto_perfil' column
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // 'data' here is the value of 'foto_perfil' from the server
                    // The server-side should handle path logic, but we can have a fallback
                    const foto_url = data ? "<?php echo base_url(); ?>" + (data.startsWith('./') ? data.substring(2) : data) : "<?php echo base_url('uploads/default_avatar.png'); ?>";
					const usuario = row.nombres + ' ' + row.apellidos;
                    return `<img src="${foto_url}" class="rounded-circle" width="40" height="40" alt="${usuario}" title="${usuario}">`;
                }
            },
            {
                targets: 5, // Corresponds to the 'actions' column
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // 'data' here is the pre-formatted HTML for actions from the server
                    // Or we can build it here if the server just sends the ID.
                    // The server-side is already building this HTML, so 'data' will contain it.
                    return data;
                }
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        }
    });

    console.log("El DataTable fue cargado correctamente");

    // Evento para botón Eliminar (delegated event)
    $('#agentesTable').on('click', '.eliminar', function () {
        const id            = $(this).data('id');
        const nombre        = $(this).data('nombre');
        const foto          = $(this).data('foto'); // Corrected typo here
		const nombre_agente = nombre; // 'nombre' from data-nombre attribute already contains full name
        const deleteUrl     = "<?php echo site_url('agentes/delete/'); ?>" + id;

        Swal.fire({
            title: `¿Eliminar a ${nombre_agente}?`,
            html: `<img src="${foto}" class="rounded-circle mb-3" width="80" alt="${nombre_agente}" title="${nombre_agente}"><br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Use POST for the delete request as it's better practice for destructive actions
                $.post(deleteUrl, { id: id, '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>' }, function (response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Eliminado',
                            text: `El agente ${nombre_agente} fue eliminado exitosamente.`,
                            icon: 'success',
                            timer: 2500,
                            showConfirmButton: false
                        });
                        tabla.ajax.reload(null, false); // Reload table without resetting pagination
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'No se pudo completar la eliminación.',
                            icon: 'error'
                        });
                    }
                }, 'json').fail(function () {
                    Swal.fire({
                        title: 'Error de Comunicación',
                        text: 'No se pudo contactar al servidor.',
                        icon: 'error'
                    });
                });
            }
        });
    });
});
</script>
