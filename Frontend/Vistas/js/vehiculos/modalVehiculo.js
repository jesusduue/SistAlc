const normalizarVehiculos = (texto) =>
    (texto || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

document.addEventListener('DOMContentLoaded', () => {
    const modalVehiculo = document.getElementById('modalVehiculoFiscal');
    if (modalVehiculo) {
        const form = document.getElementById('vehiculoFiscalForm');
        const selectContribuyente = document.getElementById('fky_con_vehiculo');
        const buscadorContribuyente = document.getElementById('buscarContribuyenteVehiculo');
        const avisoSinCoincidencias = document.getElementById('sinCoincidenciasContribuyenteVehiculo');
        const opcionesContribuyente = selectContribuyente ? Array.from(selectContribuyente.options) : [];

        const filtrarContribuyentes = (termino) => {
            const terminoNormalizado = normalizarVehiculos(termino.trim());
            let coincidencias = 0;

            opcionesContribuyente.forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }
                const coincide = normalizarVehiculos(option.textContent).includes(terminoNormalizado);
                option.hidden = !coincide;
                if (coincide) coincidencias++;
            });

            if (selectContribuyente.value && selectContribuyente.selectedOptions[0]?.hidden) {
                selectContribuyente.selectedIndex = 0;
            }

            avisoSinCoincidencias?.classList.toggle('d-none', coincidencias > 0);
        };

        buscadorContribuyente?.addEventListener('input', (event) => {
            filtrarContribuyentes(event.target.value);
        });

        modalVehiculo.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget || {};
            const title = modalVehiculo.querySelector('.modal-title');
            const accion = document.getElementById('formAccionVehiculo');
            const idInput = document.getElementById('formIdVehiculo');
            const inputPlaca = document.getElementById('pla_veh');
            const selectContribuyenteVehiculo = document.getElementById('fky_con_vehiculo');
            const inputMarca = document.getElementById('mar_veh');
            const inputModelo = document.getElementById('mod_veh');
            const inputAno = document.getElementById('ano_veh');
            const inputColor = document.getElementById('col_veh');
            const inputTipo = document.getElementById('tip_veh');
            const inputCapacidad = document.getElementById('cap_car_veh');
            const inputSerial = document.getElementById('num_ser_veh');
            const selectCondicion = document.getElementById('est_veh');
            const btnSubmit = document.getElementById('btnSubmitVehiculo');
            const btnIcon = btnSubmit.querySelector('i');
            const btnText = btnSubmit.querySelector('span');

            if (button.dataset && button.dataset.id) {
                title.textContent = 'Editar vehiculo fiscal';
                accion.value = 'modificar';
                idInput.value = button.dataset.id || '';
                inputPlaca.value = button.dataset.placa || '';
                selectContribuyenteVehiculo.value = button.dataset.contribuyente || '';
                inputMarca.value = button.dataset.marca || '';
                inputModelo.value = button.dataset.modelo || '';
                inputAno.value = button.dataset.ano || '';
                inputColor.value = button.dataset.color || '';
                inputTipo.value = button.dataset.tipo || '';
                inputCapacidad.value = button.dataset.capacidad || '';
                inputSerial.value = button.dataset.serial || '';
                selectCondicion.value = button.dataset.condicion || 'p';
                buscadorContribuyente.value = selectContribuyenteVehiculo.selectedOptions[0]?.textContent || '';
                filtrarContribuyentes(buscadorContribuyente.value);
                btnIcon.className = 'fas fa-save';
                btnText.textContent = 'Guardar cambios';
                return;
            }

            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            selectCondicion.value = 'p';
            buscadorContribuyente.value = '';
            filtrarContribuyentes('');
            title.textContent = 'Registrar vehiculo fiscal';
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar vehiculo';
        });
    }

    const modalPatente = document.getElementById('modalPatenteVehiculo');
    if (modalPatente) {
        const form = document.getElementById('patenteVehiculoForm');
        const selectVehiculo = document.getElementById('fky_veh_fis');
        const buscadorVehiculo = document.getElementById('buscarVehiculoPatente');
        const resumenVehiculo = document.getElementById('resumenVehiculoPatente');
        const avisoSinCoincidencias = document.getElementById('sinCoincidenciasVehiculoPatente');
        const opcionesVehiculo = selectVehiculo ? Array.from(selectVehiculo.options) : [];
        const catalogoVehiculos = window.catalogosVehiculosPatentes || {};

        const actualizarResumen = () => {
            const datos = catalogoVehiculos[selectVehiculo.value];
            if (!datos) {
                resumenVehiculo.textContent = 'Selecciona un vehiculo para ver un resumen rapido del propietario y la unidad.';
                return;
            }
            resumenVehiculo.innerHTML = `
                <strong>${datos.placa || ''}</strong> · ${datos.vehiculo || ''}
                <br>${datos.contribuyente || ''} (${datos.rif || ''})
                <br>${datos.tipo || ''}${datos.ano ? ' · ' + datos.ano : ''}
            `;
        };

        const filtrarVehiculos = (termino) => {
            const terminoNormalizado = normalizarVehiculos(termino.trim());
            let coincidencias = 0;

            opcionesVehiculo.forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }
                const coincide = normalizarVehiculos(option.textContent).includes(terminoNormalizado);
                option.hidden = !coincide;
                if (coincide) coincidencias++;
            });

            if (selectVehiculo.value && selectVehiculo.selectedOptions[0]?.hidden) {
                selectVehiculo.selectedIndex = 0;
                actualizarResumen();
            }

            avisoSinCoincidencias?.classList.toggle('d-none', coincidencias > 0);
        };

        buscadorVehiculo?.addEventListener('input', (event) => {
            filtrarVehiculos(event.target.value);
        });
        selectVehiculo?.addEventListener('change', actualizarResumen);

        modalPatente.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget || {};
            const title = modalPatente.querySelector('.modal-title');
            const accion = document.getElementById('formAccionPatenteVehiculo');
            const idInput = document.getElementById('formIdPatenteVehiculo');
            const inputNumero = document.getElementById('num_pat_vf');
            const inputEmision = document.getElementById('fec_emi_pat');
            const inputVencimiento = document.getElementById('fec_ven_pat');
            const inputMonto = document.getElementById('mon_pat');
            const selectEstado = document.getElementById('est_pat_vf');
            const btnSubmit = document.getElementById('btnSubmitPatenteVehiculo');
            const btnIcon = btnSubmit.querySelector('i');
            const btnText = btnSubmit.querySelector('span');

            if (button.dataset && button.dataset.id) {
                title.textContent = 'Editar patente vehicular';
                accion.value = 'modificar';
                idInput.value = button.dataset.id || '';
                inputNumero.value = button.dataset.numero || '';
                selectVehiculo.value = button.dataset.vehiculo || '';
                inputEmision.value = button.dataset.emision || '';
                inputVencimiento.value = button.dataset.vencimiento || '';
                inputMonto.value = button.dataset.monto || '';
                selectEstado.value = button.dataset.estado || 'e';
                buscadorVehiculo.value = selectVehiculo.selectedOptions[0]?.textContent || '';
                filtrarVehiculos(buscadorVehiculo.value);
                actualizarResumen();
                btnIcon.className = 'fas fa-save';
                btnText.textContent = 'Guardar cambios';
                return;
            }

            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            selectEstado.value = 'e';
            buscadorVehiculo.value = '';
            filtrarVehiculos('');
            actualizarResumen();
            title.textContent = 'Registrar patente vehicular';
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar patente';
        });
    }
});
