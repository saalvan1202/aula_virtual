export function timeToMinutes(time) {
    const [hours, minutes] = time.split(":").map(Number);
    return hours * 60 + minutes;
}

export function minutesToTime(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${String(hours).padStart(2, "0")}:${String(mins).padStart(2, "0")}`;
}

export function subtractTimes(time1, time2) {
    const minutes1 = timeToMinutes(time1);
    const minutes2 = timeToMinutes(time2);
    const difference = minutes1 - minutes2;
    return minutesToTime(difference);
}

export function subtractDates(date1, date2) {
    const d1 = new Date(date1); // Convertir a objeto Date
    const d2 = new Date(date2); // Convertir a objeto Date

    // Comparar las fechas directamente
    if (d1 > d2) {
        return 1; // Fecha de inicio es posterior
    } else if (d1 < d2) {
        return -1; // Fecha de inicio es anterior
    }
    return 0; // Son iguales
}
