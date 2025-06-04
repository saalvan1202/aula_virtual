export const cleanObject = (obj) => {
    for (let key in obj) {
        if (obj[key] === "" || obj[key] === 0 || obj[key] === "0") {
            obj[key] = null;
        } else if (typeof obj[key] === "string") {
            obj[key] = obj[key].toUpperCase();
        }
    }
    return obj;
};
