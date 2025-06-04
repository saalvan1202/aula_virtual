import { helpers } from "@vuelidate/validators";

export const requiredSchedule = helpers.withMessage(
    "El valor seleccionado no es válido",
    (value) =>
        value != 0 && value !== null && value !== undefined && value !== ""
);
