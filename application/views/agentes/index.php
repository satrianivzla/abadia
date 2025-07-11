<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo html_escape($title); ?></h1>
    <a href="<?php echo site_url('agentes/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Agente
    </a>
</div>

<div class="table-responsive">
    <table id="agentesTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Cédula</th>
                <th>RIF</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Cargo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded by DataTables -->
        </tbody>
    </table>
</div>

<!-- Page specific script for DataTables initialization -->
<script type="text/javascript">
$(document).ready(function() {
    $('#agentesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('agentes/get_agentes_list_ss'); ?>",
            "type": "POST"
        },
        "columns": [
            { "data": "id" },
            {
                "data": "foto_perfil",
                "render": function(data, type, row) {
                    var img_src = "<?php echo base_url('uploads/default_avatar.png'); ?>"; // Default avatar
                    if (data && data.trim() !== '') {
                        // Assuming 'data' contains a relative path like './uploads/agentes_fotos/file.jpg'
                        // or an absolute URL if stored differently.
                        // For './' relative paths from CI root:
                        let relative_path = data.startsWith('./') ? data.substring(2) : data;
                        img_src = "<?php echo base_url(); ?>" + relative_path;
                    }
                    return '<img src="' + img_src + '" alt="Foto Perfil" class="img-thumbnail" style="width:50px; height:50px; object-fit:cover;">';
                },
                "orderable": false // Typically, image columns are not sortable
            },
            { "data": "nombres" },
            { "data": "apellidos" },
            { "data": "cedula" },
            { "data": "rif" },
            { "data": "correo_electronico" },
            { "data": "telefono_celular" },
            { "data": "cargo_nombre" },
            { "data": "actions", "orderable": false, "searchable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" // Spanish language file for DataTables
        },
        "responsive": true,
        "deferRender": true, // For speed with large datasets
        "pageLength": 10, // Default number of rows per page
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]] // Page length options
    });
});
</script>
