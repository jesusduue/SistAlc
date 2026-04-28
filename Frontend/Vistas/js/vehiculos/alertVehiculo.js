document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const btnCambiarEstadoVehiculo = event.target.closest('.btn-cambiar-estado-vehiculo');
        if (btnCambiarEstadoVehiculo) {
            event.preventDefault();
            const url = btnCambiarEstadoVehiculo.getAttribute('data-url');
            const placa = btnCambiarEstadoVehiculo.getAttribute('data-placa') || 'vehiculo';

            Swal.fire({
                title: 'Confirmar cambio',
                text: `Se actualizara el estado del vehiculo "${placa}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
            return;
        }

        const btnEliminarVehiculo = event.target.closest('.btn-eliminar-vehiculo');
        if (btnEliminarVehiculo) {
            event.preventDefault();
            const url = btnEliminarVehiculo.getAttribute('data-url');
            const placa = btnEliminarVehiculo.getAttribute('data-placa') || 'vehiculo';

            Swal.fire({
                title: 'Eliminar vehiculo',
                text: `Se eliminara el vehiculo "${placa}". Esta accion no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
            return;
        }

        const btnCambiarEstadoPatente = event.target.closest('.btn-cambiar-estado-patente-vehiculo');
        if (btnCambiarEstadoPatente) {
            event.preventDefault();
            const url = btnCambiarEstadoPatente.getAttribute('data-url');
            const numero = btnCambiarEstadoPatente.getAttribute('data-numero') || 'patente';

            Swal.fire({
                title: 'Confirmar cambio',
                text: `Se actualizara el estado del registro de la patente "${numero}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
            return;
        }

        const btnActualizarEstadoPatente = event.target.closest('.btn-actualizar-estado-patente-vehiculo');
        if (btnActualizarEstadoPatente) {
            event.preventDefault();
            const url = btnActualizarEstadoPatente.getAttribute('data-url');
            const numero = btnActualizarEstadoPatente.getAttribute('data-numero') || 'patente';
            const estado = btnActualizarEstadoPatente.getAttribute('data-estado') || 'estado';

            Swal.fire({
                title: 'Actualizar estado',
                text: `La patente "${numero}" pasara al estado "${estado}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
            return;
        }

        const btnEliminarPatente = event.target.closest('.btn-eliminar-patente-vehiculo');
        if (btnEliminarPatente) {
            event.preventDefault();
            const url = btnEliminarPatente.getAttribute('data-url');
            const numero = btnEliminarPatente.getAttribute('data-numero') || 'patente';

            Swal.fire({
                title: 'Eliminar patente vehicular',
                text: `Se eliminara la patente "${numero}". Esta accion no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }
    });
});
