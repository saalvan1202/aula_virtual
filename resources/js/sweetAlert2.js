import Swal from "sweetalert2";
export const alertSuccess = (message) => {
    Swal.fire({
        position: "center",
        icon: "success",
        title: "Operacion Terminada",
        text: message,
        showConfirmButton: false,
        timer: 1500,
        customClass: {
            confirmButton: "btn btn-primary",
        },
        buttonsStyling: false,
    });
};
export const alertError = (message, timer, html) => {
    if (timer == undefined) {
        timer = 1500;
    }
    if (html == undefined) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: message,
            showConfirmButton: false,
            timer: timer,
            customClass: {
                confirmButton: "btn btn-primary",
            },
            buttonsStyling: false,
        });
    } else {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            html: message,
            showConfirmButton: false,
            timer: timer,
            customClass: {
                confirmButton: "btn btn-primary",
            },
            buttonsStyling: false,
        });
    }
};
export const alertWarning = (message, timer = 2500) => {
    Swal.fire({
        position: "center",
        icon: "warning",
        title: "Advertencia",
        text: message,
        showConfirmButton: false,
        timer: timer,
        customClass: {
            confirmButton: "btn btn-primary",
        },
        buttonsStyling: false,
    });
};
export const confirm = (options, callback, force) => {
    if (force == undefined) force = false;
    const defaults = {
        title: "Confirmar",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ed5565",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
    };
    const settings = $.extend({}, defaults, options);
    Swal.fire(settings).then((result) => {
        if (force) {
            callback(result);
            return;
        }
        if (result.value) {
            callback(result);
        }
    });
};

export const notifyWarning = (message) => {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        icon: "warning",
        title: message,
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });
    Toast.fire();
};
export const notifyDanger = (message) => {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        icon: "error",
        // text: 'Los datos ingresados son insuficiente, revise que todos los campos hayan sido llenados correctamente',
        title: message,
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });
    Toast.fire();
};
