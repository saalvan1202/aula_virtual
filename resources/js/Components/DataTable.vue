<template>
    <table class="table zero-configuration" :id="idTable">
        <thead>
            <tr>
                <th v-for="(colum, index) in columns" :key="index">
                    {{ colum.title }}
                </th>
            </tr>
        </thead>
    </table>
</template>
<script>
export default {
    name: "DataTable",
    props: {
        columns: Array,
        path: String,
        height: {
            type: Number,
            default: 300,
        },
        params: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            dataTable: null,
        };
    },
    mounted() {
        this.initializeDataTable(this.path);

        let columns = [];
        const $self = this;
        this.columns.forEach((el, index) => {
            if (index == 0) {
                el.data = "id";
                el.orderable = true;
            }
            columns.push({
                data: el.data,
                name: el.name == undefined ? el.data : el.name,
                orderable: el.orderable == undefined ? true : el.orderable,
                searchable: el.searchable == undefined ? true : el.searchable,
                render: el.render == undefined ? null : el.render,
                width: el.width == undefined ? null : el.width,
            });
        });

        $.extend($.fn.dataTable.defaults, {
            // dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                infoEmpty:
                    "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                infoThousands: ",",
                loadingRecords: "Cargando...",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
                aria: {
                    sortAscending:
                        ": Activar para ordenar la columna de manera ascendente",
                    sortDescending:
                        ": Activar para ordenar la columna de manera descendente",
                },
                buttons: {
                    copy: "Copiar",
                    colvis: "Visibilidad",
                    collection: "Colección",
                    colvisRestore: "Restaurar visibilidad",
                    copyKeys:
                        "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                    copySuccess: {
                        1: "Copiada 1 fila al portapapeles",
                        _: "Copiadas %d fila al portapapeles",
                    },
                    copyTitle: "Copiar al portapapeles",
                    csv: "CSV",
                    excel: "Excel",
                    pageLength: {
                        "-1": "Mostrar todas las filas",
                        1: "Mostrar 1 fila",
                        _: "Mostrar %d filas",
                    },
                    pdf: "PDF",
                    print: "Imprimir",
                },
                autoFill: {
                    cancel: "Cancelar",
                    fill: "Rellene todas las celdas con <i>%d<\/i>",
                    fillHorizontal: "Rellenar celdas horizontalmente",
                    fillVertical: "Rellenar celdas verticalmentemente",
                },
                decimal: ",",
                searchBuilder: {
                    add: "Añadir condición",
                    button: {
                        0: "Constructor de búsqueda",
                        _: "Constructor de búsqueda (%d)",
                    },
                    clearAll: "Borrar todo",
                    condition: "Condición",
                    conditions: {
                        date: {
                            after: "Despues",
                            before: "Antes",
                            between: "Entre",
                            empty: "Vacío",
                            equals: "Igual a",
                            notBetween: "No entre",
                            notEmpty: "No Vacio",
                            not: "Diferente de",
                        },
                        number: {
                            between: "Entre",
                            empty: "Vacio",
                            equals: "Igual a",
                            gt: "Mayor a",
                            gte: "Mayor o igual a",
                            lt: "Menor que",
                            lte: "Menor o igual que",
                            notBetween: "No entre",
                            notEmpty: "No vacío",
                            not: "Diferente de",
                        },
                        string: {
                            contains: "Contiene",
                            empty: "Vacío",
                            endsWith: "Termina en",
                            equals: "Igual a",
                            notEmpty: "No Vacio",
                            startsWith: "Empieza con",
                            not: "Diferente de",
                        },
                        array: {
                            not: "Diferente de",
                            equals: "Igual",
                            empty: "Vacío",
                            contains: "Contiene",
                            notEmpty: "No Vacío",
                            without: "Sin",
                        },
                    },
                    data: "Data",
                    deleteTitle: "Eliminar regla de filtrado",
                    leftTitle: "Criterios anulados",
                    logicAnd: "Y",
                    logicOr: "O",
                    rightTitle: "Criterios de sangría",
                    title: {
                        0: "Constructor de búsqueda",
                        _: "Constructor de búsqueda (%d)",
                    },
                    value: "Valor",
                },
                searchPanes: {
                    clearMessage: "Borrar todo",
                    collapse: {
                        0: "Paneles de búsqueda",
                        _: "Paneles de búsqueda (%d)",
                    },
                    count: "{total}",
                    countFiltered: "{shown} ({total})",
                    emptyPanes: "Sin paneles de búsqueda",
                    loadMessage: "Cargando paneles de búsqueda",
                    title: "Filtros Activos - %d",
                },
                select: {
                    1: "%d fila seleccionada",
                    _: "%d filas seleccionadas",
                    cells: {
                        1: "1 celda seleccionada",
                        _: "$d celdas seleccionadas",
                    },
                    columns: {
                        1: "1 columna seleccionada",
                        _: "%d columnas seleccionadas",
                    },
                },
                thousands: ".",
                datetime: {
                    previous: "Anterior",
                    next: "Proximo",
                    hours: "Horas",
                    minutes: "Minutos",
                    seconds: "Segundos",
                    unknown: "-",
                    amPm: ["am", "pm"],
                },
                editor: {
                    close: "Cerrar",
                    create: {
                        button: "Nuevo",
                        title: "Crear Nuevo Registro",
                        submit: "Crear",
                    },
                    edit: {
                        button: "Editar",
                        title: "Editar Registro",
                        submit: "Actualizar",
                    },
                    remove: {
                        button: "Eliminar",
                        title: "Eliminar Registro",
                        submit: "Eliminar",
                        confirm: {
                            _: "¿Está seguro que desea eliminar %d filas?",
                            1: "¿Está seguro que desea eliminar 1 fila?",
                        },
                    },
                    error: {
                        system: 'Ha ocurrido un error en el sistema (<a target="\\" rel="\\ nofollow" href="\\">Más información&lt;\\\/a&gt;).<\/a>',
                    },
                    multi: {
                        title: "Múltiples Valores",
                        info: "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                        restore: "Deshacer Cambios",
                        noMulti:
                            "Este registro puede ser editado individualmente, pero no como parte de un grupo.",
                    },
                },
                info: "Mostrando de _START_ a _END_ de _TOTAL_ entradas",
            },
        });
        this.$nextTick(() => {
            this.dataTable = $(`#${this.idTable}`).DataTable({
                autoWidth: true,
                scrollY: this.height,
                processing: true,
                serverSide: true,
                ajax: {
                    url: this.path,
                    type: "get",
                    data: function (d) {
                        for (const [key, value] of Object.entries(
                            $self.params
                        )) {
                            d[key] = value;
                        }
                    },
                },
                columns: columns,
                columnDefs: [
                    {
                        searchable: false,
                        orderable: false,
                        targets: 0,
                    },
                ],
                order: [[0, "desc"]],
                buttons: {
                    buttons: [
                        {
                            extend: "copyHtml5",
                            className: "btn btn-light",
                            exportOptions: {
                                columns: [0, ":visible"],
                            },
                        },
                        {
                            extend: "excelHtml5",
                            className: "btn btn-light",
                            exportOptions: {
                                columns: ":visible",
                            },
                        },
                        {
                            extend: "pdfHtml5",
                            className: "btn btn-light",
                            exportOptions: {
                                columns: [0, 1, 2, 5],
                            },
                        },
                        {
                            extend: "colvis",
                            text: '<i class="icon-three-bars"></i>',
                            className: "btn bg-blue btn-icon dropdown-toggle",
                        },
                    ],
                },
            });
            /*this.dataTable.on("order.dt search.dt", function () {
                $self.dataTable
                    .column(0, { search: "applied", order: "applied" })
                    .nodes()
                    .each(function (cell, i) {
                        cell.innerHTML = i + 1;
                        $self.dataTable.cell(cell).invalidate("dom");
                    });
            }).draw();*/
            $(`#${this.idTable}`)
                .on("error.dt", function (e, settings, techNote, message) {
                    console.log("error ajax: ", message);
                })
                .DataTable();
        });

        $.fn.dataTable.ext.errMode = "none";
        $(document).on("click", ".sidebar-control", function () {
            $self.dataTable.columns.adjust().draw();
        });
        $(this.$el).on("click", "tr", function () {
            $self.selected = $self.dataTable.row(this).data();
            /* if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }*/
            $self.dataTable.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        });
        $(document).on("click", "[data-action]", function (e) {
            e.preventDefault();
            e.stopPropagation();
            let target = $(e.currentTarget);
            let action = target.attr("data-action");
            if (!action) {
                return;
            }
            let tr = target;
            if (target.prop("tagName") !== "TR") {
                tr = target.closest("tr");
            }
            if (tr) {
                const row = $self.dataTable.row(tr);
                const data = row.data();
                $self.$emit(action, data, row, tr, target);
            }
        });
    },
    methods: {
        reload() {
            this.dataTable.ajax.reload();
            this.reset();
        },
        getSelectedRow() {
            return this.selected;
        },
        reset() {
            this.selected = null;
        },
        draw() {
            this.dataTable.draw();
        },
        url(url) {
            this.dataTable.ajax.url(url).load();
        },

        initializeDataTable(url) {
            let columns = this.columns.map((el, index) => ({
                data: index === 0 ? "id" : el.data,
                name: el.name || el.data,
                orderable: el.orderable !== false,
                searchable: el.searchable !== false,
                render: el.render || null,
                width: el.width || null,
            }));

            this.dataTable = $(`#${this.idTable}`).DataTable({
                // ... (configuración existente)
                ajax: {
                    url: url,
                    type: "get",
                    data: (d) => {
                        Object.entries(this.params).forEach(([key, value]) => {
                            d[key] = value;
                        });
                    },
                },
                columns: columns,
            });
        },
        updateTableUrl(newUrl) {
            if (this.dataTable) {
                this.dataTable.ajax.url(newUrl).load();
            }
        },
    },
    computed: {
        idTable() {
            return (
                "data-table-" +
                Math.floor(Math.random() * (999 - 100 + 1)) +
                100
            );
        },
    },
    watch: {
        path(newPath) {
            this.updateTableUrl(newPath);
        },
        params: {
            deep: true,
            handler(newParams) {
                if (this.dataTable) {
                    this.dataTable.ajax.reload(); // Recarga si los params cambian
                }
            },
        },
    },
    beforeDestroy() {
        if (this.dataTable) {
            this.dataTable.destroy(true); // Limpia eventos y datos
        }
    },
};
</script>
