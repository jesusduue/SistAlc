document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalLicencia');
    if (!modal) return;

    const form = document.getElementById('licenciaForm');
    const modalTitle = modal.querySelector('.modal-title');
    const formAccion = document.getElementById('formAccionLicencia');
    const formId = document.getElementById('formIdLicencia');
    const inputNumero = document.getElementById('num_lic');
    const selectPatente = document.getElementById('fky_pat');
    const inputExpedicion = document.getElementById('fec_exp_lic');
    const inputVigencia = document.getElementById('vig_lic');
    const selectEstado = document.getElementById('est_lic');
    const hiddenFacturaId = document.getElementById('fky_fac');
    const inputFacturaNumero = document.getElementById('num_fac_ref');
    const btnValidarFactura = document.getElementById('btnValidarFactura');
    const infoFactura = document.getElementById('infoFacturaSeleccionada');
    const btnSubmit = document.getElementById('btnSubmitLicencia');
    const btnIcon = btnSubmit.querySelector('i');
    const btnText = btnSubmit.querySelector('span');

    const catalogos = window.catalogosLicencias || { patentes: {} };

    const limpiarFactura = (mensaje = 'Debe validar la factura para poder guardar.') => {
        hiddenFacturaId.value = '';
        infoFactura.textContent = mensaje;
        infoFactura.classList.remove('text-success', 'text-danger');
        infoFactura.classList.add('text-muted');
    };

    const mostrarFactura = (datos) => {
        hiddenFacturaId.value = datos.id_fac;
        infoFactura.textContent = `Factura #${datos.num_fac} · ${datos.nom_con} (${datos.rif_con}) · Total Bs. ${Number(datos.tot_fac).toFixed(2)}`;
        infoFactura.classList.remove('text-muted', 'text-danger');
        infoFactura.classList.add('text-success');
    };

    const validarFactura = async () => {
        const numero = (inputFacturaNumero.value || '').trim();
        const patente = selectPatente.value;
        const idLic = formId.value || '';
        if (!numero) {
            limpiarFactura('Ingrese el numero de factura y presione validar.');
            return;
        }
        if (!patente) {
            limpiarFactura('Seleccione la patente antes de validar la factura.');
            return;
        }
        infoFactura.textContent = 'Validando factura...';
        infoFactura.classList.remove('text-muted', 'text-success');
        infoFactura.classList.add('text-warning');
        try {
            const params = new URLSearchParams();
            params.append('accion', 'validar_factura');
            params.append('num_fac_ref', numero);
            params.append('fky_pat', patente);
            if (idLic) params.append('id_lic', idLic);

            const response = await fetch('../../../Backend/controlador/licenciaController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            });
            const data = await response.json();
            if (!data.ok) {
                limpiarFactura(data.mensaje || 'La factura indicada no es valida.');
                infoFactura.classList.remove('text-muted');
                infoFactura.classList.add('text-danger');
                return;
            }
            mostrarFactura(data.factura);
        } catch (error) {
            limpiarFactura('No se pudo validar la factura. Intente nuevamente.');
            infoFactura.classList.remove('text-muted');
            infoFactura.classList.add('text-danger');
            console.error('Error validando factura:', error);
        }
    };

    const actualizarResumenPatente = () => {
        const patenteId = selectPatente.value;
        const datos = catalogos.patentes ? catalogos.patentes[patenteId] : null;
        if (!datos) {
            selectPatente.setAttribute('data-helper', '');
            return;
        }
        const resumen = `${datos.numero || ''} · ${datos.nombre || ''} · ${datos.contribuyente || ''} (${datos.rif || ''})`;
        selectPatente.setAttribute('data-helper', resumen);
    };

    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        if (!button.dataset) return;

        if (button.dataset.id) {
            modalTitle.textContent = 'Editar Licencia';
            formAccion.value = 'modificar';
            formId.value = button.dataset.id || '';
            inputNumero.value = button.dataset.numero || '';
            selectPatente.value = button.dataset.patente || '';
            inputExpedicion.value = button.dataset.expedicion || '';
            inputVigencia.value = button.dataset.vigencia || '';
            selectEstado.value = button.dataset.estado || 'S';
            const numeroFactura = (button.dataset.factura && button.dataset.factura !== 'null') ? button.dataset.factura : '';
            const facturaId = (button.dataset.facturaId && button.dataset.facturaId !== 'null') ? button.dataset.facturaId : '';
            inputFacturaNumero.value = numeroFactura;
            hiddenFacturaId.value = facturaId;
            if (numeroFactura) {
                const mensaje = `Factura #${numeroFactura}${button.dataset.contribuyente ? ' · ' + button.dataset.contribuyente : ''}`;
                infoFactura.textContent = mensaje;
                infoFactura.classList.remove('text-muted', 'text-danger');
                infoFactura.classList.add('text-success');
            } else {
                limpiarFactura();
            }
            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar Cambios';
        } else {
            form.reset();
            formAccion.value = 'registrar';
            formId.value = '';
            selectEstado.value = 'S';
            limpiarFactura();
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar Licencia';
        }
        actualizarResumenPatente();
    });

    selectPatente?.addEventListener('change', () => {
        actualizarResumenPatente();
        limpiarFactura();
    });

    btnValidarFactura?.addEventListener('click', validarFactura);
    inputFacturaNumero?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            validarFactura();
        }
    });

    form.addEventListener('submit', (event) => {
        if (!hiddenFacturaId.value) {
            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Factura requerida',
                text: 'Debe validar y seleccionar una factura antes de guardar la licencia.'
            });
        }
    });
});
