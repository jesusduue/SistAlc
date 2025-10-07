document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalLicencia');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget || {};
        const form = document.getElementById('licenciaForm');
        const modalTitle = modal.querySelector('.modal-title');
        const formAccion = document.getElementById('formAccionLicencia');
        const formId = document.getElementById('formIdLicencia');
        const inputNumero = document.getElementById('num_lic');
        const selectPatente = document.getElementById('fky_pat');
        const inputExpedicion = document.getElementById('fec_exp_lic');
        const inputVigencia = document.getElementById('vig_lic');
        const selectEstado = document.getElementById('est_lic');
        const btnSubmit = document.getElementById('btnSubmitLicencia');
        const btnIcon = btnSubmit.querySelector('i');
        const btnText = btnSubmit.querySelector('span');

        if (button.dataset && button.dataset.id) {
            modalTitle.textContent = 'Editar Licencia';
            formAccion.value = 'modificar';
            formId.value = button.dataset.id || '';
            inputNumero.value = button.dataset.numero || '';
            selectPatente.value = button.dataset.patente || '';
            inputExpedicion.value = button.dataset.expedicion || '';
            inputVigencia.value = button.dataset.vigencia || '';
            selectEstado.value = button.dataset.estado || 'S';
            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar Cambios';
        } else {
            form.reset();
            modalTitle.textContent = 'Registrar Nueva Licencia';
            formAccion.value = 'registrar';
            formId.value = '';
            selectEstado.value = 'S';
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar Licencia';
        }
    });
});
