    /* script para activar el modal de registro o edicion */
document.addEventListener('DOMContentLoaded', function () {
    const contribuyenteModal = document.getElementById('modalContribuyente');

    contribuyenteModal.addEventListener('show.bs.modal', function (event) {
        //boton que activa el modal
        const button = event.relatedTarget;
        
        //se extrae la informacion de los atributos data-
        const id = button.getAttribute('data-id');

        //elementos del formulario
        const modalTitle = contribuyenteModal.querySelector('.modal-title');
        const form = document.getElementById('contribuyenteForm');
        const formAccion = document.getElementById('formAccion');
        const formIdContribuyente = document.getElementById('formIdContribuyente');
        const inputId = document.getElementById('id_con');
        const inputNombre = document.getElementById('nom_con');
        const inputRif = document.getElementById('rif_con');
        const inputTelefono = document.getElementById('tel_con');
        const inputCorreo = document.getElementById('ema_con');
        const inputDireccion = document.getElementById('dir_con');
        const btnSubmitText = document.querySelector('#btnSubmit span');
        const btnSubmitIcon = document.querySelector('#btnSubmit i');


        /* valida que recibe un ID para activa el modal en modo edicion */
        if (id) {
            // MODO EDICIÓN
            modalTitle.textContent = 'Editar Contribuyente';
            formAccion.value = 'modificar';
            formIdContribuyente.value = id;

            
            inputNombre.value = button.getAttribute('data-nombre');
            inputRif.value = button.getAttribute('data-rif');
            inputTelefono.value = button.getAttribute('data-telefono');
            inputCorreo.value = button.getAttribute('data-correo');
            inputDireccion.value = button.getAttribute('data-direccion');

            //se actualiza el boton de envio
            btnSubmitText.textContent = 'Guardar Cambios';
            btnSubmitIcon.className = 'fas fa-save';

            //console.log para ver los datos actuales
            console.log("Editando Contribuyente:", {
                codigo: button.getAttribute('data-id'),
                nombre: button.getAttribute('data-nombre'),
                rif: button.getAttribute('data-rif'),
                telefono: button.getAttribute('data-telefono'),
                correo: button.getAttribute('data-correo'),
                direccion: button.getAttribute('data-direccion'),
                //verificar que el value sea modificar
                accion: formAccion.value

            }); 

        } else {
            // MODO REGISTRO
            modalTitle.textContent = 'Registrar Nuevo Contribuyente';
            form.reset();
            formAccion.value = 'registrar';
            formIdContribuyente.value = '';
            btnSubmitText.textContent = 'Registrar Contribuyente';
            btnSubmitIcon.className = 'fas fa-plus';
        }
    });
});