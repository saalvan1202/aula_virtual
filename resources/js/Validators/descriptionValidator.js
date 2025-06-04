import { helpers, required } from "@vuelidate/validators";

// Función para validar que el valor sea un string
const isString = (value) => typeof value === "string";

// Función reutilizable para validar "descripcion"
export const descriptionValidator = () => ({
    required: helpers.withMessage("La descripción es requerida", required),
    isString: helpers.withMessage("La descripción debe ser un texto", isString),
});
