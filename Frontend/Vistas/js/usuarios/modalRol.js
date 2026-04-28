document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('rolModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('rolForm');
        const accion = document.getElementById('formAccionRol');
        const idInput = document.getElementById('formIdRol');
        const nombreInput = document.getElementById('nom_rol');
        const estadoInput = document.getElementById('est_registro');
        const ayudaRolBase = document.getElementById('ayudaRolBase');
        const title = modal.querySelector('.modal-title');
        const submitBtn = document.getElementById('btnSubmitRol');
        const btnIcon = submitBtn.querySelector('i');
        const btnText = submitBtn.querySelector('span');

        if (button.dataset && button.dataset.id) {
            const esRolBase = button.dataset.esBase === '1';

            title.textContent = 'Editar rol';
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            nombreInput.value = button.dataset.nombre || '';
            estadoInput.value = button.dataset.estado || 'A';
            nombreInput.readOnly = esRolBase;

            if (esRolBase) {
                ayudaRolBase.textContent = 'Este es un rol base. El nombre no puede cambiarse porque el sistema resuelve permisos a partir de ese valor.';
                ayudaRolBase.classList.remove('d-none');
            } else {
                ayudaRolBase.textContent = '';
                ayudaRolBase.classList.add('d-none');
            }

            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar cambios';
            return;
        }

        form.reset();
        title.textContent = 'Registrar rol';
        accion.value = 'registrar';
        idInput.value = '';
        estadoInput.value = 'A';
        nombreInput.readOnly = false;
        ayudaRolBase.textContent = '';
        ayudaRolBase.classList.add('d-none');
        btnIcon.className = 'fas fa-plus';
        btnText.textContent = 'Registrar rol';
    });
});
