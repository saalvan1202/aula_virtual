import { helpers } from "@vuelidate/validators";

export const requiredAddJob = helpers.withMessage(
    "El valor seleccionado no es válido",
    (value) => {
        return (
            typeof value === "boolean" ||
            (value != 0 &&
                value !== null &&
                value !== undefined &&
                value !== "")
        );
    }
);
