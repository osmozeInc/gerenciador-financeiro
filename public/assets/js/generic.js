
/*  MANIPULAÇÃO DE DOM */

export function toggleClassById(idElemento, classCss) {
    document.getElementById(idElemento).classList.toggle(classCss);
}

export function toggleClassByQuery(classElement, classCss) {
    document.querySelector(classElement).classList.toggle(classCss);
}

