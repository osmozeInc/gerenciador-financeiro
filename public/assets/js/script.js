import { definirTema, definirVisibilidadeDeValores } from "./modais.js";

let tema = localStorage.getItem('tema');

if (!tema) {
    definirTema('light');
} else {
    definirTema(tema);
}

