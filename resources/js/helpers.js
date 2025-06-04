import Vue, { getCurrentInstance } from "vue";
import _ from "lodash";
export const getVueInstance = () => {
    return getCurrentInstance().proxy;
};
export const searchSemana = (semanas, id) => {
    const response = semanas.find((item) => item.id === id);
    if (!response) {
        return "";
    }
    return response.nombre;
};
export const routeTo = (url) => {
    return baseUrl + "/" + url;
};
export const currentUrl = () => {
    const { pathname, search } =
        typeof window !== "undefined" ? window.location : {};
    return (
        pathname
            .replace(baseUrl.replace(/^\w*:\/\/[^/]+/, ""), "")
            .replace(/^\/+/, "") + search
    );
};
export const groupBy = function (xs, key) {
    return xs.reduce(function (rv, x) {
        (rv[x[key]] = rv[x[key]] || []).push(x);
        return rv;
    }, {});
};
export const convertDate = (_string, _caracterSplit, _caracterJoin) => {
    let cadena = _string.toString();
    if (cadena.indexOf(_caracterSplit)) {
        let arrayDatos = cadena.split(_caracterSplit);
        arrayDatos.reverse();
        cadena = arrayDatos.join(_caracterJoin);
    }

    return cadena;
};
export const firstId = (array, column) => {
    let first = _.head(array);
    if (first == undefined) {
        return 0;
    }
    if (column == undefined) {
        column = "id";
    }
    return first[column];
};
export const currentDate = function () {
    const now = new Date();
    const day = ("0" + now.getDate()).slice(-2);
    const month = ("0" + (now.getMonth() + 1)).slice(-2);
    return now.getFullYear() + "-" + month + "-" + day;
};
export const printIframe = (url, onLoad) => {
    let printFrame = document.getElementById("printIframe");
    if (printFrame == null) {
        printFrame = document.createElement("iframe");
        printFrame.setAttribute(
            "style",
            "visibility: hidden; height: 0; width: 0; position: absolute; border: 0;display:none"
        );
        printFrame.setAttribute("id", "printIframe");
        document.getElementsByTagName("body")[0].appendChild(printFrame);
        printFrame.onload = () => {
            if (onLoad !== undefined) {
                onLoad();
            }
            try {
                printFrame.focus();
                setTimeout(function () {
                    var html = printFrame.contentWindow.document.body.innerHTML;
                    if (html === "ESCPOS") {
                        printFrame.remove();
                    } else {
                        printFrame.contentWindow.print();
                    }
                }, 110);
            } catch (error) {
                console.log("ocurrio un error", error);
            } finally {
                const handler = () => {
                    setTimeout(() => {
                        /*if(callback!==undefined){
                            callback();
                        }*/
                        window.removeEventListener("mouseover", handler);
                    }, 150);
                };
                window.addEventListener("mouseover", handler);
            }
        };
    }
    printFrame.setAttribute("src", url);
};
export const eventBus = new Vue();
