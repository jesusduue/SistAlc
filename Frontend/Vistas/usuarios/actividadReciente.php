    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Actividad Reciente</span>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted">Mostrando <span id="rangeInfo">1-5</span></span>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="paginador"></ul>
                </nav>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody id="tbodyActividad">
                    <!-- Filas generadas por JS -->
                </tbody>
            </table>
        </div>
    </div>