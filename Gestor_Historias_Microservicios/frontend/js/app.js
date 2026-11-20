
// DIRECCION DEL API GATEWAY
const API = "http://localhost/Mircroservicios/Gestor_Historias_Microservicios/api-gateway/";

// INICIAR CARGA DE DATOS
document.addEventListener("DOMContentLoaded", function () {
    cargarSprints();
    cargarHistorias();
    cargarInformes();
});

// =====================================
// CONSULTAR SPRINTS
// =====================================

async function cargarSprints() {

    const contenedor = document.getElementById("listaSprints");

    try {

        const respuesta = await fetch(API + "?servicio=sprints");

        if (!respuesta.ok) {
            throw new Error("Error al consultar sprints");
        }

        const sprints = await respuesta.json();

        document.getElementById("totalSprints").textContent = sprints.length;

        if (sprints.length === 0) {
            contenedor.innerHTML = "<p>No hay sprints registrados.</p>";
            return;
        }

        contenedor.innerHTML = sprints.map(sprint => `
            <article class="registro">
                <h3>${sprint.nombre}</h3>
                <p>Inicio: ${sprint.fecha_inicio}</p>
                <p>Finalizacion: ${sprint.fecha_fin}</p>
            </article>
        `).join("");

    } catch (error) {

        console.error("Error en sprints:", error);

        contenedor.textContent = "No se pudieron cargar los sprints.";
    }
}

// =====================================
// CONSULTAR HISTORIAS
// =====================================

async function cargarHistorias() {

    const contenedor = document.getElementById("listaHistorias");

    try {

        const respuesta = await fetch(API + "?servicio=historias");

        if (!respuesta.ok) {
            throw new Error("Error al consultar historias");
        }

        const historias = await respuesta.json();

        document.getElementById("totalHistorias").textContent = historias.length;

        const puntos = historias.reduce(
            (total, historia) => total + Number(historia.puntos),
            0
        );

        document.getElementById("totalPuntos").textContent = puntos;

        if (historias.length === 0) {
            contenedor.innerHTML = "<p>No hay historias registradas.</p>";
            return;
        }

        contenedor.innerHTML = historias.map(historia => `
            <article class="registro">
                <h3>${historia.titulo}</h3>
                <p>${historia.descripcion}</p>
                <p>Responsable: ${historia.responsable}</p>
                <p>Estado: ${historia.estado}</p>
                <p>Puntos: ${historia.puntos}</p>
                <p>Sprint: ${historia.sprint_id}</p>
            </article>
        `).join("");

    } catch (error) {

        console.error("Error en historias:", error);

        contenedor.textContent = "No se pudieron cargar las historias.";
    }
}

// =====================================
// CONSULTAR INFORMES
// =====================================

async function cargarInformes() {

    const contenedor = document.getElementById("listaInformes");

    try {

        const respuesta = await fetch(API + "?servicio=informes");

        if (!respuesta.ok) {
            throw new Error("Error al consultar informes");
        }

        const informes = await respuesta.json();

        if (informes.length === 0) {
            contenedor.innerHTML = "<p>No hay informes disponibles.</p>";
            return;
        }

        contenedor.innerHTML = informes.map(informe => `
            <article class="registro">
                <h3>Estado: ${informe.estado}</h3>
                <p>Cantidad de historias: ${informe.cantidad}</p>
                <p>Puntos totales: ${informe.puntos_totales ?? 0}</p>
            </article>
        `).join("");

    } catch (error) {

        console.error("Error en informes:", error);

        contenedor.textContent = "No se pudieron cargar los informes.";
    }
}
