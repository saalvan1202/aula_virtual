import { helpers } from "@vuelidate/validators";

export const qualificationsValidator = (maxValue = 20) =>
    helpers.withMessage(
        (value) => {
            const stringValue = String(value).trim();

            // Si contiene una coma
            if (stringValue.includes(",")) {
                return "El valor no debe contener comas.";
            }

            // Intentar convertir a número
            const numberValue = parseFloat(value);
            if (isNaN(numberValue)) {
                return `El valor debe estar entre 0.01 y ${maxValue.toFixed(
                    2
                )} y sin contener (,).`;
            }

            // Validar rango permitido
            if (numberValue < 0.1 || numberValue > maxValue) {
                return `El valor debe estar entre 0.01 y ${maxValue.toFixed(
                    2
                )}`;
            }

            if (numberValue === 0) {
                return "El valor no puede ser 0.";
            }

            return ""; // Sin errores
        },
        (value) => {
            const stringValue = String(value).trim();
            const numberValue = parseFloat(value);

            if (stringValue.includes(",")) return false;
            if (isNaN(numberValue)) return false;
            if (numberValue < 0.0 || numberValue > maxValue) return false;

            return numberValue !== 0; // Retorna false si el valor es 0

            // return true;
        }
    );
