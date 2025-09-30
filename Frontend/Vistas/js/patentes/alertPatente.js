document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const btnCambiarEstado = event.target.closest('.btn-cambiar-estado-patente');
        if (btnCambiarEstado) {
            event.preventDefault();
            const url = btnCambiarEstado.getAttribute('data-url');
            const numero = btnCambiarEstado.getAttribute('data-numero') || 'patente';

            Swal.fire({
                title: '¿Está seguro?',
                text: `Se cambiará el estado del registro de la patente "${numero}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });

            return;
        }

        const btnActualizarEstado = event.target.closest('.btn-actualizar-estado');
        if (btnActualizarEstado) {
            event.preventDefault();
            const url = btnActualizarEstado.getAttribute('data-url');
            const numero = btnActualizarEstado.getAttribute('data-numero') || 'patente';
            const estado = btnActualizarEstado.getAttribute('data-estado') || 'estado';

            Swal.fire({
                title: 'Confirmar cambio',
                text: `Se actualizará el estado del trámite de la patente "${numero}" a "${estado}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });

            return;
        }

        const btnEliminar = event.target.closest('.btn-eliminar-patente');
        if (btnEliminar) {
            event.preventDefault();
            const url = btnEliminar.getAttribute('data-url');
            const numero = btnEliminar.getAttribute('data-numero') || 'patente';

            Swal.fire({
                title: '¿Está seguro?',
                text: `Se eliminará la patente "${numero}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    });
});
