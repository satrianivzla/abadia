<?php if (isset($afiliacion) && !empty($afiliacion)): ?>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Detalles de su Contrato de Afiliación</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Información del Contrato</h4>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>N° de Contrato</b> <span class="float-right"><?php echo html_escape($afiliacion->contract_number); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Fecha de Afiliación</b> <span class="float-right"><?php echo date('d/m/Y', strtotime($afiliacion->fecha)); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Estado del Contrato</b> <span class="float-right"><span class="badge bg-success"><?php echo html_escape($afiliacion->status); ?></span></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4>Información del Plan</h4>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Plan</b> <span class="float-right"><?php echo html_escape($afiliacion->plan_type); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Monto Total</b> <span class="float-right">$<?php echo number_format($afiliacion->plan_amount, 2); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Cuotas</b> <span class="float-right"><?php echo html_escape($afiliacion->cuotas); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr>

            <h4>Grupo Familiar Afiliado</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombres y Apellidos</th>
                        <th>Cédula</th>
                        <th>Parentesco</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Titular -->
                    <tr>
                        <td><?php echo html_escape($afiliacion->titular_nombres . ' ' . $afiliacion->titular_apellidos); ?></td>
                        <td><?php echo html_escape($afiliacion->titular_cedula); ?></td>
                        <td><span class="badge bg-primary">Titular</span></td>
                    </tr>
                    <!-- Familiares -->
                    <?php if (!empty($afiliacion->familiares)): ?>
                        <?php foreach($afiliacion->familiares as $familiar): ?>
                        <tr>
                            <td><?php echo html_escape($familiar->nombres . ' ' . $familiar->apellidos); ?></td>
                            <td><?php echo html_escape($familiar->cedula); ?></td>
                            <td><?php echo html_escape(ucfirst($familiar->parentesco)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h4 class="alert-heading">Bienvenido</h4>
        <p>Aún no se ha encontrado un plan de afiliación activo donde usted sea el titular.</p>
        <hr>
        <p class="mb-0">Si cree que esto es un error, por favor contacte a su asesor de ventas o a soporte.</p>
    </div>
<?php endif; ?>
