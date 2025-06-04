import { helpers } from "@vuelidate/validators";

export const requiredScheduleTime = helpers.withMessage(
    "El valor seleccionado no es válido",
    (value) => value >= "07:00" && value <= "23:00"
);
