const normalizar = (texto) =>
    texto
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalPatente');
    if (!modal) return;

    const selectContribuyente = document.getElementById('fky_con');
    const buscador = document.getElementById('buscarRepresentante');
    const avisoSinCoincidencias = document.getElementById('sinCoincidenciasRepresentante');
    const opciones = selectContribuyente ? Array.from(selectContribuyente.options) : [];

    const filtrarContribuyentes = (termino) => {
        const terminoNormalizado = normalizar(termino.trim());
        let coincidencias = 0;

        opciones.forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                return;
            }
            const coincide = normalizar(option.textContent).includes(terminoNormalizado);
            option.hidden = !coincide;
            if (coincide) coincidencias++;
        });

        if (
            selectContribuyente.value &&
            selectContribuyente.selectedOptions[0]?.hidden
        ) {
            selectContribuyente.selectedIndex = 0;
        }

        if (avisoSinCoincidencias) {
            avisoSinCoincidencias.classList.toggle('d-none', coincidencias > 0);
        }
    };

    buscador?.addEventListener('input', (event) => {
        filtrarContribuyentes(event.target.value);
    });

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget || {};
        const form = document.getElementById('patenteForm');
        const modalTitle = modal.querySelector('.modal-title');
        const formAccion = document.getElementById('formAccion');
        const formId = document.getElementById('formIdPatente');
        const inputNumero = document.getElementById('num_pat');
        const inputRif = document.getElementById('rif_pat');
        const inputNombre = document.getElementById('nom_pat');
        const inputDireccion = document.getElementById('dir_pat');
        const inputRubro = document.getElementById('rub_pat');
        const selectEstado = document.getElementById('est_pat');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnIcon = btnSubmit.querySelector('i');
        const btnText = btnSubmit.querySelector('span');

        const activarModoRegistro = () => {
            form.reset();
            modalTitle.textContent = 'Registrar Nueva Patente';
            formAccion.value = 'registrar';
            formId.value = '';
            selectEstado.value = 'S';
            buscador.value = '';
            filtrarContribuyentes('');
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar Patente';
        };

        const activarModoEdicion = () => {
            modalTitle.textContent = 'Editar Patente';
            formAccion.value = 'modificar';
            formId.value = button.dataset.id || '';
            inputNumero.value = button.dataset.numero || '';
            inputRif.value = button.dataset.rif || '';
            inputNombre.value = button.dataset.nombre || '';
            inputDireccion.value = button.dataset.direccion || '';
            inputRubro.value = button.dataset.rubro || '';
            selectEstado.value = button.dataset.estado || 'S';
            selectContribuyente.value = button.dataset.representante || '';

            const opcionSeleccionada = selectContribuyente.selectedOptions[0];
            buscador.value = opcionSeleccionada ? opcionSeleccionada.textContent : '';
            filtrarContribuyentes(buscador.value);

            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar Cambios';
        };

        if (button.dataset && button.dataset.id) {
            activarModoEdicion();
        } else {
            activarModoRegistro();
        }
    });
});
