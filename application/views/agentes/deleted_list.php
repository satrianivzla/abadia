<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?php echo html_escape($title); ?></h1>
    <a href="<?php echo site_url('agentes'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a Agentes Activos
    </a>
</div>

<div class="table-responsive">
    <table id="deletedAgentesTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cédula</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Fecha de Eliminación</th>
                <th>Eliminado Por</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded by DataTables -->
        </tbody>
    </table>
</div>

<!-- DataTables Initialization Script -->
<script type="text/javascript">
$(document).ready(function() {
    $('#deletedAgentesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('agentes/get_deleted_agentes_list'); ?>",
            "type": "POST"
        },
        "columns": [
            { "data": "id" },
            { "data": "cedula" },
            { "data": "nombres" },
            { "data": "apellidos" },
            { "data": "deleted_at" },
            { "data": "deleted_by" },
            { "data": "actions", "orderable": false, "searchable": false }
        ],
        "order": [[ 4, "desc" ]], // Default order by deletion date
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        },
        "responsive": true
    });
});
</script>
