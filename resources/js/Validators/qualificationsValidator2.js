import { helpers } from "@vuelidate/validators";

export const qualificationsValidator2 = (maxValue = 20) =>
    helpers.withMessage(
        (value) => {
            const stringValue = String(value).trim();

            // Si el campo está vacío
            if (!stringValue) {
                return "Este campo es requerido.";
            }

            // Si contiene una coma
            if (stringValue.includes(",")) {
                return "El valor no debe contener comas.";
            }

            // Convertir a número
            const numberValue = parseFloat(value);

            // Validar que el valor no sea 0
            if (numberValue === 0) {
                return "El valor no puede ser 0.";
            }

            return ""; // Sin errores
        },
        (value) => {
            const stringValue = String(value).trim();

            // Retorna false si el campo está vacío, contiene una coma o es 0
            if (!stringValue || stringValue.includes(",")) return false;

            const numberValue = parseFloat(value);
            return numberValue !== 0; // Retorna false si el valor es 0
        }
    );
