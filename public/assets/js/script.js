import { definirTema, definirVisibilidadeDeValores } from "/assets/js/modais.js";

let tema = localStorage.getItem('tema');

if (!tema) {
    definirTema('light');
} else {
    definirTema(tema);
}
