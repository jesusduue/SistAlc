document.addEventListener('DOMContentLoaded', () => {
    const catalogos = window.catalogosFacturacion || { tipos: [], cedulas: [], solvenciasMunicipales: [], licencias: [], solvenciasComerciales: [], patentes: [], reportes: {} };
    const form = document.getElementById('facturaForm');
    const accionInput = document.getElementById('formAccionFactura');
    const idInput = document.getElementById('formIdFactura');
    const totInput = document.getElementById('tot_fac');
    const detallesInput = document.getElementById('detallesFactura');
    const tablaBody = document.querySelector('#tablaDetalle tbody');
    const btnAgregarDetalle = document.getElementById('btnAgregarDetalle');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    const btnGuardar = document.getElementById('btnGuardarFactura');
    const selectContribuyente = document.getElementById('fky_con');
    const descripcionInput = document.getElementById('des_fac');
    const buscadorPatentes = document.getElementById('buscadorPatentes');
    const tablaPatentes = document.getElementById('tablaPatentesFacturacion');

    const entityOptions = [
        { value: 'none', label: 'Sin entidad' },
        { value: 'cedula', label: 'Cedula catastral' },
        { value: 'solv_mun', label: 'Solvencia municipal' },
        { value: 'licencia', label: 'Licencia comercial' },
        { value: 'solv_com', label: 'Solvencia comercial' },
    ];

    const mapCatalog = {
        cedula: catalogos.cedulas,
        solv_mun: catalogos.solvenciasMunicipales,
        licencia: catalogos.licencias,
        solv_com: catalogos.solvenciasComerciales,
    };

    const limpiarTabla = () => {
        tablaBody.innerHTML = '';
        recalcularTotal();
    };

    const crearSelect = (options, value = '') => {
        const select = document.createElement('select');
        select.className = 'form-select form-select-sm';
        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            if (String(opt.value) === String(value)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
        return select;
    };

    const crearFila = (detalle = {}) => {
        const tr = document.createElement('tr');

        // Tipo de impuesto
        const tdTipo = document.createElement('td');
        const tipoSelect = crearSelect(catalogos.tipos.map(t => ({ value: t.id_tip, label: `${t.nom_tip}` })), detalle.fky_tip_imp);
        tipoSelect.name = 'detalle_tipo';
        tdTipo.appendChild(tipoSelect);

        // Monto
        const tdMonto = document.createElement('td');
        const montoInput = document.createElement('input');
        montoInput.type = 'number';
        montoInput.min = '0';
        montoInput.step = '0.01';
        montoInput.className = 'form-control form-control-sm';
        montoInput.value = typeof detalle.mon_imp !== 'undefined' ? detalle.mon_imp : '';
        montoInput.name = 'detalle_monto';
        montoInput.addEventListener('input', recalcularTotal);
        tdMonto.appendChild(montoInput);

        // Tipo de entidad
        const tdEntidad = document.createElement('td');
        const entidadSelect = crearSelect(entityOptions, obtenerTipoEntidad(detalle));
        entidadSelect.name = 'detalle_entidad_tipo';
        tdEntidad.appendChild(entidadSelect);

        // Referencia
        const tdReferencia = document.createElement('td');
        const referenciaSelect = crearSelect([{ value: '', label: 'Seleccione...' }]);
        referenciaSelect.name = 'detalle_entidad_id';
        tdReferencia.appendChild(referenciaSelect);

        const asignarOpcionesReferencia = (tipoActual, valorActual = '') => {
            const datos = mapCatalog[tipoActual] || [];
            referenciaSelect.innerHTML = '';
            const optDefault = document.createElement('option');
            optDefault.value = '';
            optDefault.textContent = 'Seleccione...';
            referenciaSelect.appendChild(optDefault);
            datos.forEach(item => {
                const opt = document.createElement('option');
                let etiqueta = '';
                switch (tipoActual) {
                    case 'cedula':
                        etiqueta = `${item.num_ced} - ${item.sol_ced}`;
                        break;
                    case 'solv_mun':
                        etiqueta = `${item.num_sol} - ${item.sol_sol}`;
                        break;
                    case 'licencia':
                        etiqueta = `${item.num_lic} - ${item.est_lic}`;
                        break;
                    case 'solv_com':
                        etiqueta = `${item.num_sol_com} - ${item.est_sol_com}`;
                        break;
                    default:
                        etiqueta = '';
                }
                opt.value = item.id_ced ?? item.id_sol ?? item.id_lic ?? item.id_sol_com ?? '';
                opt.textContent = etiqueta;
                if (String(opt.value) === String(valorActual)) {
                    opt.selected = true;
                }
                referenciaSelect.appendChild(opt);
            });
            referenciaSelect.disabled = tipoActual === 'none';
        };

        entidadSelect.addEventListener('change', () => {
            asignarOpcionesReferencia(entidadSelect.value);
        });

        const entidadTipoInicial = obtenerTipoEntidad(detalle);
        const entidadIdInicial = obtenerIdEntidad(detalle);
        asignarOpcionesReferencia(entidadTipoInicial, entidadIdInicial);

        // Acciones
        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-sm btn-outline-danger';
        btnEliminar.innerHTML = '<i class="fas fa-times"></i>';
        btnEliminar.addEventListener('click', () => {
            tr.remove();
            recalcularTotal();
        });
        tdAccion.appendChild(btnEliminar);

        tr.append(tdTipo, tdMonto, tdEntidad, tdReferencia, tdAccion);
        return tr;
    };

    const obtenerTipoEntidad = (detalle) => {
        if (detalle.fky_ced) return 'cedula';
        if (detalle.fky_sol_mun) return 'solv_mun';
        if (detalle.fky_lic) return 'licencia';
        if (detalle.fky_sol_com) return 'solv_com';
        return 'none';
    };

    const obtenerIdEntidad = (detalle) => {
        return detalle.fky_ced || detalle.fky_sol_mun || detalle.fky_lic || detalle.fky_sol_com || '';
    };

    const recalcularTotal = () => {
        const montos = Array.from(tablaBody.querySelectorAll('input[name="detalle_monto"]'))
            .map(inp => parseFloat(inp.value || '0'));
        const total = montos.reduce((acc, val) => acc + (isNaN(val) ? 0 : val), 0);
        totInput.value = total.toFixed(2);
    };

    const construirDetalle = () => {
        const filas = Array.from(tablaBody.querySelectorAll('tr'));
        return filas.map(fila => {
            const tipo = fila.querySelector('select[name="detalle_tipo"]').value;
            const monto = parseFloat(fila.querySelector('input[name="detalle_monto"]').value || '0');
            const entidadTipo = fila.querySelector('select[name="detalle_entidad_tipo"]').value;
            const entidadId = fila.querySelector('select[name="detalle_entidad_id"]').value || null;

            const detalle = {
                fky_tip_imp: tipo,
                mon_imp: monto,
                fky_ced: null,
                fky_sol_mun: null,
                fky_lic: null,
                fky_sol_com: null,
            };

            if (entidadTipo === 'cedula') detalle.fky_ced = entidadId;
            if (entidadTipo === 'solv_mun') detalle.fky_sol_mun = entidadId;
            if (entidadTipo === 'licencia') detalle.fky_lic = entidadId;
            if (entidadTipo === 'solv_com') detalle.fky_sol_com = entidadId;

            return detalle;
        });
    };

    btnAgregarDetalle?.addEventListener('click', () => {
        tablaBody.appendChild(crearFila());
    });

    const asignarPatente = (info) => {
        if (!info || !selectContribuyente) return;
        if (info.fky_con) {
            selectContribuyente.value = info.fky_con;
        }
        if (descripcionInput && !descripcionInput.value) {
            const nombre = info.nom_pat ? `${info.num_pat || ''} - ${info.nom_pat}` : (info.num_pat || '');
            descripcionInput.value = nombre ? `Factura generada para la patente ${nombre}` : descripcionInput.value;
        }
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Patente asignada',
                text: 'Se asigno el contribuyente de la patente seleccionada al formulario.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    };

    tablaPatentes?.addEventListener('click', (event) => {
        const btn = event.target.closest('.seleccionar-patente');
        if (!btn) return;
        const fila = btn.closest('tr');
        if (!fila) return;
        const data = fila.getAttribute('data-patente');
        if (!data) return;
        try {
            const info = JSON.parse(data);
            asignarPatente(info);
        } catch (error) {
            console.error('No se pudo interpretar la informacion de la patente seleccionada', error);
        }
    });

    buscadorPatentes?.addEventListener('input', () => {
        const termino = buscadorPatentes.value.trim().toLowerCase();
        if (!tablaPatentes) return;
        tablaPatentes.querySelectorAll('tbody tr').forEach((fila) => {
            const coincide = fila.textContent.toLowerCase().includes(termino);
            fila.style.display = coincide ? '' : 'none';
        });
    });

    btnCancelarEdicion?.addEventListener('click', () => {
        form.reset();
        accionInput.value = 'registrar';
        idInput.value = '';
        detallesInput.value = '';
        btnGuardar.querySelector('span').textContent = 'Registrar Factura';
        limpiarTabla();
        btnCancelarEdicion.hidden = true;
        buscadorPatentes && (buscadorPatentes.value = '');
        if (tablaPatentes) {
            tablaPatentes.querySelectorAll('tbody tr').forEach(fila => fila.style.display = '');
        }
    });

    form?.addEventListener('submit', (event) => {
        const detalles = construirDetalle();
        if (!detalles.length) {
            event.preventDefault();
            alert('Debe agregar al menos un detalle de impuesto.');
            return;
        }
        for (const item of detalles) {
            if (!item.fky_tip_imp) {
                event.preventDefault();
                alert('Cada detalle debe tener un tipo de impuesto.');
                return;
            }
        }
        detallesInput.value = JSON.stringify(detalles);
    });

    document.querySelectorAll('.btn-detalles-factura').forEach(btn => {
        btn.addEventListener('click', () => {
            const data = btn.dataset.detalles ? JSON.parse(btn.dataset.detalles) : [];
            const cuerpo = document.getElementById('detalleFacturaCuerpo');
            cuerpo.innerHTML = '';
            if (!data.length) {
                const fila = document.createElement('tr');
                const celda = document.createElement('td');
                celda.colSpan = 3;
                celda.textContent = 'Sin detalles registrados';
                fila.appendChild(celda);
                cuerpo.appendChild(fila);
            } else {
                data.forEach(det => {
                    const fila = document.createElement('tr');
                    const tipo = catalogos.tipos.find(t => String(t.id_tip) === String(det.fky_tip_imp));
                    const entidadLabel = () => {
                        if (det.fky_ced) {
                            const item = catalogos.cedulas.find(c => String(c.id_ced) === String(det.fky_ced));
                            return item ? `Cedula ${item.num_ced}` : 'Cedula';
                        }
                        if (det.fky_sol_mun) {
                            const item = catalogos.solvenciasMunicipales.find(c => String(c.id_sol) === String(det.fky_sol_mun));
                            return item ? `Solv. municipal ${item.num_sol}` : 'Solv. municipal';
                        }
                        if (det.fky_lic) {
                            const item = catalogos.licencias.find(c => String(c.id_lic) === String(det.fky_lic));
                            return item ? `Licencia ${item.num_lic}` : 'Licencia';
                        }
                        if (det.fky_sol_com) {
                            const item = catalogos.solvenciasComerciales.find(c => String(c.id_sol_com) === String(det.fky_sol_com));
                            return item ? `Solv. comercial ${item.num_sol_com}` : 'Solv. comercial';
                        }
                        return 'Sin entidad';
                    };
                    fila.innerHTML = `
                        <td>${tipo ? tipo.nom_tip : 'Tipo no definido'}</td>
                        <td>${Number(det.mon_imp).toFixed(2)}</td>
                        <td>${entidadLabel()}</td>
                    `;
                    cuerpo.appendChild(fila);
                });
            }
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleFactura'));
            modal.show();
        });
    });

    document.querySelectorAll('.btn-editar-factura').forEach(btn => {
        btn.addEventListener('click', () => {
            form.reset();
            limpiarTabla();
            accionInput.value = 'modificar';
            idInput.value = btn.dataset.id || '';
            document.getElementById('num_fac').value = btn.dataset.numero || '';
            document.getElementById('fec_fac').value = btn.dataset.fecha || '';
            document.getElementById('fky_con').value = btn.dataset.contribuyente || '';
            document.getElementById('des_fac').value = btn.dataset.descripcion || '';
            document.getElementById('est_pago').value = btn.dataset.pago || 'E';
            btnGuardar.querySelector('span').textContent = 'Guardar Cambios';
            btnCancelarEdicion.hidden = false;

            const detalles = btn.dataset.detalles ? JSON.parse(btn.dataset.detalles) : [];
            if (detalles.length) {
                detalles.forEach(det => tablaBody.appendChild(crearFila(det)));
            } else {
                tablaBody.appendChild(crearFila());
            }
            recalcularTotal();
            window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' });
        });
    });

    // Inicializa con una fila por defecto
    if (!tablaBody.children.length) {
        tablaBody.appendChild(crearFila());
    }
    recalcularTotal();
});
