document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalPatente');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget || {};
        const form = document.getElementById('patenteForm');
        const modalTitle = modal.querySelector('.modal-title');
        const formAccion = document.getElementById('formAccion');
        const formId = document.getElementById('formIdPatente');
        const inputNumero = document.getElementById('num_pat');
        const inputRif = document.getElementById('rif_pat');
        const inputNombre = document.getElementById('nom_pat');
        const selectContribuyente = document.getElementById('fky_con');
        const inputDireccion = document.getElementById('dir_pat');
        const inputRubro = document.getElementById('rub_pat');
        const selectEstado = document.getElementById('est_pat');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnIcon = btnSubmit.querySelector('i');
        const btnText = btnSubmit.querySelector('span');

        if (button.dataset && button.dataset.id) {
            modalTitle.textContent = 'Editar Patente';
            formAccion.value = 'modificar';
            formId.value = button.dataset.id || '';
            inputNumero.value = button.dataset.numero || '';
            inputRif.value = button.dataset.rif || '';
            inputNombre.value = button.dataset.nombre || '';
            selectContribuyente.value = button.dataset.representante || '';
            inputDireccion.value = button.dataset.direccion || '';
            inputRubro.value = button.dataset.rubro || '';
            selectEstado.value = button.dataset.estado || 'S';

            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar Cambios';
        } else {
            form.reset();
            modalTitle.textContent = 'Registrar Nueva Patente';
            formAccion.value = 'registrar';
            formId.value = '';
            selectEstado.value = 'S';

            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar Patente';
        }
    });
});
