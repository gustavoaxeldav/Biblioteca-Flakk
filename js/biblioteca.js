let libros = [];

/* =========================================
   FORMATO DE MONEDA
========================================= */

function moneda(numero) {
    return Number(numero).toLocaleString("es-MX", {
        style: "currency",
        currency: "MXN"
    });
}

/* =========================================
   MODO DALTONISMO
========================================= */

function modoDaltonismo() {
    document.body.classList.toggle("daltonismo");

    localStorage.setItem(
        "daltonismo",
        document.body.classList.contains("daltonismo")
    );
}

if (localStorage.getItem("daltonismo") === "true") {
    document.body.classList.add("daltonismo");
}

/* =========================================
   OBTENER LIBROS DESDE MYSQL
========================================= */

async function obtenerLibros() {
    try {
        const respuesta = await fetch(
            "../php/obtener_libros.php"
        );

        if (!respuesta.ok) {
            throw new Error(
                "Error HTTP: " + respuesta.status
            );
        }

        const resultado = await respuesta.json();

        if (!resultado.exito) {
            throw new Error(
                resultado.mensaje ||
                "No se pudieron cargar los libros."
            );
        }

        libros = resultado.libros;

        mostrarLibros();
        cargarLibrosVenta();
        mostrarInventario();

    } catch (error) {
        console.error(
            "Error al obtener libros:",
            error
        );
    }
}

/* =========================================
   CREAR TARJETA DE LIBRO
========================================= */

function crearTarjetaLibro(libro) {
    const imagen =
        libro.imagen ||
        "../img/portadas/sin-portada.jpg";

    return `
        <article class="libro">

            <img
                src="${imagen}"
                alt="Portada de ${libro.titulo}"
                onerror="this.src='../img/portadas/sin-portada.jpg'"
            >

            <h3>${libro.titulo}</h3>

            <p>
                <strong>Autor:</strong>
                ${libro.autor}
            </p>

            <p>
                <strong>Categoría:</strong>
                ${libro.categoria}
            </p>

            <p>
                <strong>Precio:</strong>
                ${moneda(libro.precio)}
            </p>

            <p>
                <strong>Stock:</strong>
                ${libro.stock}
            </p>

        </article>
    `;
}

/* =========================================
   MOSTRAR Y FILTRAR LIBROS
========================================= */

function mostrarLibros() {
    const contenedor =
        document.getElementById("contenedorLibros");

    if (!contenedor) {
        return;
    }

    const texto =
        document.getElementById("buscar")
            ?.value
            .trim()
            .toLowerCase() || "";

    const categoria =
        document.getElementById("filtroCategoria")
            ?.value || "Todos";

    const librosFiltrados = libros.filter((libro) => {
        const datos =
            `${libro.titulo} ${libro.autor}`
                .toLowerCase();

        const coincideTexto =
            datos.includes(texto);

        const coincideCategoria =
            categoria === "Todos" ||
            libro.categoria === categoria;

        return coincideTexto && coincideCategoria;
    });

    if (librosFiltrados.length === 0) {
        contenedor.innerHTML = `
            <p>No se encontraron libros.</p>
        `;

        return;
    }

    contenedor.innerHTML =
        librosFiltrados
            .map(crearTarjetaLibro)
            .join("");
}

/* =========================================
   AGREGAR LIBRO
========================================= */

async function agregarLibro() {
    const titulo =
        document.getElementById("titulo")
            ?.value
            .trim() || "";

    const autor =
        document.getElementById("autor")
            ?.value
            .trim() || "";

    const categoria =
        document.getElementById("categoria")
            ?.value || "";

    const precio =
        Number(
            document.getElementById("precio")
                ?.value
        );

    const stock =
        Number(
            document.getElementById("stock")
                ?.value
        );

    const imagen =
        document.getElementById("imagen")
            ?.value
            .trim() || "";

    if (
        titulo === "" ||
        autor === "" ||
        categoria === "" ||
        Number.isNaN(precio) ||
        precio < 0 ||
        Number.isNaN(stock) ||
        stock < 0
    ) {
        alert(
            "Completa correctamente todos los campos."
        );

        return;
    }

    try {
        const respuesta = await fetch(
            "../php/agregar_libro.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    titulo,
                    autor,
                    categoria,
                    precio,
                    stock,
                    imagen
                })
            }
        );

        if (!respuesta.ok) {
            throw new Error(
                "Error HTTP: " + respuesta.status
            );
        }

        const resultado = await respuesta.json();

        alert(resultado.mensaje);

        if (resultado.exito) {
            document.getElementById("titulo").value = "";
            document.getElementById("autor").value = "";
            document.getElementById("precio").value = "";
            document.getElementById("stock").value = "";
            document.getElementById("imagen").value = "";

            await obtenerLibros();
        }

    } catch (error) {
        console.error(
            "Error al agregar libro:",
            error
        );

        alert(
            "No se pudo conectar con el servidor."
        );
    }
}

/* =========================================
   CARGAR LIBROS EN EL SELECT DE VENTAS
========================================= */

function cargarLibrosVenta() {

    const select =
        document.getElementById("libroVenta");

    if (!select) {
        return;
    }

    const disponibles = libros.filter(
        libro => Number(libro.stock) > 0
    );

    if (disponibles.length === 0) {

        select.innerHTML = `
            <option value="">
                No hay libros disponibles
            </option>
        `;

        return;
    }

    select.innerHTML =
        `<option value="">
            Selecciona un libro
        </option>` +

        disponibles.map(libro => `
            <option value="${libro.id}">
                ${libro.titulo} (Stock: ${libro.stock})
            </option>
        `).join("");
}

/* =========================================
   REGISTRAR VENTA
========================================= */

async function registrarVenta() {

    const idLibro =
        Number(
            document.getElementById("libroVenta").value
        );

    const cantidad =
        Number(
            document.getElementById("cantidad").value
        );

    const cliente =
        document.getElementById("cliente")
            .value
            .trim();

    if (
        idLibro <= 0 ||
        Number.isNaN(cantidad) ||
        cantidad <= 0
    ) {

        alert(
            "Selecciona un libro y una cantidad válida."
        );

        return;
    }

    try {

        const respuesta = await fetch(
            "../php/registrar_venta.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/json"
                },
                body: JSON.stringify({
                    id_libro: idLibro,
                    cantidad,
                    cliente
                })
            }
        );

        if (!respuesta.ok) {
            throw new Error(
                "Error HTTP " +
                respuesta.status
            );
        }

        const resultado =
            await respuesta.json();

        alert(resultado.mensaje);

        if (resultado.exito) {

            document.getElementById(
                "cantidad"
            ).value = 1;

            document.getElementById(
                "cliente"
            ).value = "";

            await obtenerLibros();
            await obtenerVentas();

        }

    } catch (error) {

        console.error(error);

        alert(
            "No se pudo registrar la venta."
        );

    }

}

/* =========================================
   OBTENER HISTORIAL DE VENTAS
========================================= */

async function obtenerVentas() {

    const tabla =
        document.getElementById("tablaVentas");

    if (!tabla) {
        return;
    }

    try {

        const respuesta = await fetch(
            "../php/obtener_ventas.php"
        );

        if (!respuesta.ok) {
            throw new Error(
                "Error HTTP " +
                respuesta.status
            );
        }

        const resultado =
            await respuesta.json();

        if (!resultado.exito) {
            throw new Error(
                resultado.mensaje
            );
        }

        mostrarVentas(resultado.ventas);

    } catch (error) {

        console.error(error);

        tabla.innerHTML = `
            <tr>
                <td colspan="4">
                    Error al cargar ventas.
                </td>
            </tr>
        `;

    }

}

/* =========================================
   MOSTRAR HISTORIAL
========================================= */

function mostrarVentas(ventas) {

    const tabla =
        document.getElementById("tablaVentas");

    if (!tabla) {
        return;
    }

    if (ventas.length === 0) {

        tabla.innerHTML = `
            <tr>
                <td colspan="4">
                    No hay ventas registradas.
                </td>
            </tr>
        `;

        return;
    }

    tabla.innerHTML = ventas.map(
        venta => {

            const cliente =
                venta.cliente === ""
                    ? "Público general"
                    : venta.cliente;

            const fecha =
                new Date(
                    venta.fecha.replace(
                        " ",
                        "T"
                    )
                ).toLocaleString("es-MX");

            return `
                <tr>
                    <td>${venta.libro}</td>
                    <td>${venta.cantidad}</td>
                    <td>${cliente}</td>
                    <td>${fecha}</td>
                </tr>
            `;

        }

    ).join("");

}

/* =========================================
   INVENTARIO
========================================= */

function mostrarInventario() {

    const tabla =
        document.getElementById("tablaInventario");

    if (!tabla) {
        return;
    }

    tabla.innerHTML = libros.map(libro => `
        <tr>
            <td>${libro.titulo}</td>
            <td>${libro.autor}</td>
            <td>${libro.categoria}</td>
            <td>${libro.stock}</td>
        </tr>
    `).join("");

    const totalLibros =
        document.getElementById("totalLibros");

    const existencias =
        document.getElementById("existencias");

    if (totalLibros) {
        totalLibros.textContent =
            libros.length;
    }

    if (existencias) {

        existencias.textContent =
            libros.reduce(
                (total, libro) =>
                    total + Number(libro.stock),
                0
            );

    }

}

/* =========================================
   BUSCADOR
========================================= */

const buscar =
    document.getElementById("buscar");

buscar?.addEventListener(
    "input",
    mostrarLibros
);

/* =========================================
   FILTRO POR CATEGORÍA
========================================= */

const filtroCategoria =
    document.getElementById(
        "filtroCategoria"
    );

filtroCategoria?.addEventListener(
    "change",
    mostrarLibros
);

/* =========================================
   FECHA
========================================= */

const fecha =
    document.getElementById("fecha");

if (fecha) {

    fecha.textContent =
        new Date().toLocaleDateString(
            "es-MX",
            {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            }
        );

}

/* =========================================
   CARGA INICIAL
========================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        obtenerLibros();
        obtenerVentas();

    }
);