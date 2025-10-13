document.addEventListener('DOMContentLoaded', () => {
    const catalogos = window.catalogosFacturacion || { reportes: {} };
    const reportesIniciales = catalogos.reportes || {};

    const fechaInput = document.getElementById('fechaReporteDiario');
    const btnRefrescar = document.getElementById('btnRefrescarReporteDiario');
    const tbodyMovimientos = document.querySelector('#tablaIngresosDia tbody');
    const tbodyRubros = document.querySelector('#tablaRubrosDia tbody');
    const badgePagado = document.getElementById('totalPagadoDia');
    const badgeAnulado = document.getElementById('totalAnuladoDia');
    const badgeNeto = document.getElementById('totalNetoDia');
    const badgeRubrosPagado = document.getElementById('totalRubrosPagado');
    const badgeRubrosAnulado = document.getElementById('totalRubrosAnulado');
    const badgeRubrosNeto = document.getElementById('totalRubrosNeto');

    const tablaRubrosMensuales = document.querySelector('#tablaRubrosMensuales tbody');
    const listaIngresosMensuales = document.getElementById('listaIngresosMensuales');
    const selectAnioMensual = document.getElementById('anioReporteMensual');
    const selectMesMensual = document.getElementById('mesReporteMensual');

    const nombresMes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    const formatearMonto = (valor) => {
        const numero = Number(valor) || 0;
        return numero.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const renderMovimientos = (data) => {
        if (!tbodyMovimientos) return;
        tbodyMovimientos.innerHTML = '';
        const movimientos = data.movimientos || [];
        if (!movimientos.length) {
            const fila = document.createElement('tr');
            const celda = document.createElement('td');
            celda.colSpan = 4;
            celda.textContent = 'Sin movimientos registrados en la fecha seleccionada.';
            fila.appendChild(celda);
            tbodyMovimientos.appendChild(fila);
        } else {
            movimientos.forEach((mov) => {
                const fila = document.createElement('tr');
                const esAnulado = mov.tipo_movimiento === 'A';
                const hora = mov.fec_fac ? mov.fec_fac.slice(11, 16) : '--';
                const monto = Number(mov.tot_fac) || 0;
                fila.innerHTML = `
                    <td>${hora}</td>
                    <td>${mov.nom_con || ''} ${mov.rif_con ? `(${mov.rif_con})` : ''}</td>
                    <td>${mov.num_fac || ''}</td>
                    <td class="text-end ${esAnulado ? 'text-danger' : 'text-success'}">${esAnulado ? '-' : ''}Bs. ${formatearMonto(monto)}</td>
                `;
                tbodyMovimientos.appendChild(fila);
            });
        }
        if (badgePagado) {
            badgePagado.textContent = `+Bs. ${formatearMonto(data.totales?.pagado || 0)}`;
        }
        if (badgeAnulado) {
            const totalAnulado = data.totales?.anulado || 0;
            badgeAnulado.textContent = totalAnulado ? `-Bs. ${formatearMonto(totalAnulado)}` : '-Bs. 0,00';
        }
        if (badgeNeto) {
            const neto = data.totales?.neto || 0;
            const prefijo = neto >= 0 ? '+' : '-';
            badgeNeto.textContent = `${prefijo}Bs. ${formatearMonto(Math.abs(neto))}`;
        }
    };

    const renderRubrosDiarios = (data) => {
        if (!tbodyRubros) return;
        tbodyRubros.innerHTML = '';
        const rubros = data.rubros || [];
        if (!rubros.length) {
            const fila = document.createElement('tr');
            const celda = document.createElement('td');
            celda.colSpan = 4;
            celda.textContent = 'Sin rubros registrados en la fecha seleccionada.';
            fila.appendChild(celda);
            tbodyRubros.appendChild(fila);
        } else {
            rubros.forEach((rubro) => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${rubro.nombre || ''}</td>
                    <td class="text-end text-success">Bs. ${formatearMonto(rubro.pagado)}</td>
                    <td class="text-end text-danger">Bs. ${formatearMonto(rubro.anulado)}</td>
                    <td class="text-end">${rubro.neto >= 0 ? '' : '-'}Bs. ${formatearMonto(Math.abs(rubro.neto))}</td>
                `;
                tbodyRubros.appendChild(fila);
            });
        }
        if (badgeRubrosPagado) {
            badgeRubrosPagado.textContent = `+Bs. ${formatearMonto(data.totales?.pagado || 0)}`;
        }
        if (badgeRubrosAnulado) {
            const total = data.totales?.anulado || 0;
            badgeRubrosAnulado.textContent = total ? `-Bs. ${formatearMonto(total)}` : '-Bs. 0,00';
        }
        if (badgeRubrosNeto) {
            const neto = data.totales?.neto || 0;
            const prefijo = neto >= 0 ? '+' : '-';
            badgeRubrosNeto.textContent = `${prefijo}Bs. ${formatearMonto(Math.abs(neto))}`;
        }
    };

    const renderRubrosMensuales = (data) => {
        if (!tablaRubrosMensuales) return;
        tablaRubrosMensuales.innerHTML = '';
        const rubros = data.rubros || [];
        if (!rubros.length) {
            const fila = document.createElement('tr');
            const celda = document.createElement('td');
            celda.colSpan = 2;
            celda.textContent = 'Sin rubros registrados en el mes seleccionado.';
            fila.appendChild(celda);
            tablaRubrosMensuales.appendChild(fila);
        } else {
            rubros.forEach((rubro) => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${rubro.nombre || ''}</td>
                    <td class="text-end">Bs. ${formatearMonto(rubro.total)}</td>
                `;
                tablaRubrosMensuales.appendChild(fila);
            });
        }
        const total = data.total || 0;
        const filaTotal = document.createElement('tr');
        filaTotal.innerHTML = `
            <td class="fw-semibold">Total</td>
            <td class="text-end fw-semibold">Bs. ${formatearMonto(total)}</td>
        `;
        tablaRubrosMensuales.appendChild(filaTotal);
    };

    const renderIngresosMensuales = (serie, anioSeleccionado) => {
        if (!listaIngresosMensuales) return;
        listaIngresosMensuales.innerHTML = '';
        const datos = Array.isArray(serie) ? serie : [];
        datos.forEach((valor, indice) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const mesNombre = nombresMes[indice] || `Mes ${indice + 1}`;
            li.innerHTML = `<span>${mesNombre} ${anioSeleccionado}</span><span class="fw-semibold">Bs. ${formatearMonto(valor)}</span>`;
            listaIngresosMensuales.appendChild(li);
        });
    };

    const cargarReporteDiario = async (fecha) => {
        try {
            const params = new URLSearchParams({ accion: 'ingresos_diarios', fecha });
            const respIngresos = await fetch(`../../../Backend/controlador/reportesFinancierosController.php?${params.toString()}`);
            const dataIngresos = await respIngresos.json();
            if (dataIngresos.ok) renderMovimientos(dataIngresos.data);

            const paramsRubros = new URLSearchParams({ accion: 'rubros_diarios', fecha });
            const respRubros = await fetch(`../../../Backend/controlador/reportesFinancierosController.php?${paramsRubros.toString()}`);
            const dataRubros = await respRubros.json();
            if (dataRubros.ok) renderRubrosDiarios(dataRubros.data);
        } catch (error) {
            console.error('No se pudieron cargar los reportes diarios', error);
        }
    };

    const cargarIngresosMensuales = async (anio) => {
        try {
            const params = new URLSearchParams({ accion: 'ingresos_mensuales', anio });
            const response = await fetch(`../../../Backend/controlador/reportesFinancierosController.php?${params.toString()}`);
            const resultado = await response.json();
            if (resultado.ok) {
                renderIngresosMensuales(resultado.data, anio);
            }
        } catch (error) {
            console.error('Error cargando ingresos mensuales', error);
        }
    };

    const cargarRubrosMensuales = async (anio, mes) => {
        try {
            const params = new URLSearchParams({ accion: 'rubros_mensuales', anio, mes });
            const response = await fetch(`../../../Backend/controlador/reportesFinancierosController.php?${params.toString()}`);
            const resultado = await response.json();
            if (resultado.ok) {
                renderRubrosMensuales(resultado.data);
            }
        } catch (error) {
            console.error('Error cargando rubros mensuales', error);
        }
    };

    if (reportesIniciales.diario) {
        renderMovimientos(reportesIniciales.diario);
    }
    if (reportesIniciales.rubrosDiario) {
        renderRubrosDiarios(reportesIniciales.rubrosDiario);
    }
    if (reportesIniciales.rubrosMensuales) {
        renderRubrosMensuales(reportesIniciales.rubrosMensuales);
    }
    if (reportesIniciales.indicadores && reportesIniciales.indicadores.ingresosMensuales) {
        renderIngresosMensuales(reportesIniciales.indicadores.ingresosMensuales, new Date().getFullYear());
    }

    btnRefrescar?.addEventListener('click', (event) => {
        event.preventDefault();
        const fecha = fechaInput?.value || '';
        if (!fecha) return;
        cargarReporteDiario(fecha);
    });

    selectAnioMensual?.addEventListener('change', () => {
        const anio = parseInt(selectAnioMensual.value, 10) || new Date().getFullYear();
        renderIngresosMensuales([], anio); // Limpia mientras carga
        cargarIngresosMensuales(anio);
        const mes = parseInt(selectMesMensual?.value || '1', 10);
        cargarRubrosMensuales(anio, mes);
    });

    selectMesMensual?.addEventListener('change', () => {
        const anio = parseInt(selectAnioMensual?.value || new Date().getFullYear(), 10);
        const mes = parseInt(selectMesMensual.value || '1', 10);
        cargarRubrosMensuales(anio, mes);
    });
});
