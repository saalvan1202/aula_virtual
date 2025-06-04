export const capitalizeFirstWord = (texto) => {
    if (!texto) return;

    const textoModificado =
        texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();

    return textoModificado;
};

export const capitalizeFirstWordWithSpace = (texto) => {
    if (!texto) return "";

    const textoModificado = texto
        .split(" ") // Divide el texto en palabras por los espacios
        .map(
            (palabra) =>
                palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase()
        ) // Capitaliza la primera letra de cada palabra
        .join(" "); // Une las palabras de nuevo con espacios

    return textoModificado;
};

export const capitalizeFirstWordAndConvertToSingular = (texto) => {
    if (!texto) return;

    // Retirar la "s" al final si existe
    let textoModificado = texto.trim(); // Eliminar espacios en blanco al inicio y final

    if (textoModificado.endsWith("es")) {
        textoModificado = textoModificado.slice(0, -2); // Retirar la última letra ("s")
    } else if (textoModificado.endsWith("s")) {
        textoModificado = textoModificado.slice(0, -1); // Retirar la última letra ("s")
    }

    // Capitalizar la primera letra y convertir el resto a minúsculas
    textoModificado =
        textoModificado.charAt(0).toUpperCase() +
        textoModificado.slice(1).toLowerCase();

    return textoModificado;
};
