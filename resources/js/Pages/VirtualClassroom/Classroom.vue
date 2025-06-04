<template>
    <LayoutContent>
        <ReturnButton title="Volver" path_remove="virtual-classroom" />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <h3>
                        <strong>
                            {{ capitalizeWord(curso.descripcion) }}</strong
                        >
                    </h3>
                </template>
                <div class="title-secciones_data">
                    <h4 style="width: 20%; display: flex; gap: 5px">
                        <feather-icon
                            icon="BookOpenIcon"
                            style="width: 8%"
                        ></feather-icon>
                        Actividades
                    </h4>
                    <div class="aniadir">
                        <p class="m-0 p-0" @click.prevent="createSeccion">
                            Añadir sección
                        </p>
                    </div>
                </div>
                <div
                    class="contenidos d-flex flex-column justify-content-center align-items-center row"
                >
                    <template v-if="secciones_data.length === 0">
                        <div class="py-2">
                            <div
                                class="empty d-flex flex-column justify-content-center align-items-center mt-1"
                            >
                                <div>
                                    <feather-icon icon="InboxIcon" size="50" />
                                </div>
                                <p class="m-0 p-0">
                                    No hay ninguna sección disponible
                                </p>
                            </div>
                        </div>
                    </template>
                    <div
                        v-else
                        v-for="clase in secciones_data"
                        :key="clase.id"
                        class="acordeon-container col-md-12"
                        :id="'clase-' + clase.id"
                    >
                        <div class="acordeon">
                            <div
                                class="acordeon-header d-flex justify-content-between align-items-center"
                            >
                                <div
                                    class="acordeon-header__title d-flex justify-content-start"
                                    @click="toggleClase(clase.id)"
                                    style="cursor: pointer"
                                >
                                    <span>
                                        <BBadge
                                            ref="badgeR"
                                            tabindex="0"
                                            class="badge-arrow"
                                            variant="light-success"
                                            style="
                                                display: grid;
                                                place-items: center;
                                                border-radius: 50%;
                                                height: 40px;
                                                width: 40px;
                                            "
                                        >
                                            <feather-icon
                                                style="
                                                    width: 20px;
                                                    height: 20px;
                                                "
                                                stroke-width="3px"
                                                :icon="
                                                    isVisible(clase.id)
                                                        ? 'ChevronDownIcon'
                                                        : 'ChevronRightIcon'
                                                "
                                            ></feather-icon>
                                        </BBadge>
                                    </span>
                                    <h4 class="m-0 p-0">
                                        <strong>
                                            {{
                                                capitalizeWord(clase.nombre)
                                            }}</strong
                                        >
                                    </h4>
                                </div>
                                <div class="acordeon-header__actions">
                                    <DropdownMenu
                                        @toggle="toggleDropdown(clase.id)"
                                        :active-dropdown="
                                            activeDropdown === clase.id
                                        "
                                    >
                                        <p @click="editarSeccion(clase)">
                                            Editar sección
                                        </p>
                                        <p @click="borrarSeccion(clase.id)">
                                            Borrar sección
                                        </p>
                                    </DropdownMenu>
                                </div>
                            </div>
                            <transition
                                name="slide"
                                @before-enter="beforeEnter"
                                @enter="enter"
                                @leave="leave"
                            >
                                <div
                                    v-show="isVisible(clase.id, clase.nombre)"
                                    class="acordeon-body"
                                >
                                    <div
                                        v-for="recurso in clase.seccion_recurso"
                                        :key="recurso.id"
                                        :id="'recurso-' + recurso.id"
                                    >
                                        <Activity
                                            :secciones_recursos="
                                                clase.seccion_recurso
                                            "
                                            :typeActivity="
                                                recurso.tipo_recurso.tipo_nombre.toUpperCase()
                                            "
                                            :color_activity="
                                                recurso.tipo_recurso.color
                                            "
                                            :subTitle="recurso.nombre"
                                            :descripcion="recurso.descripcion"
                                            :id_tipo_recurso="
                                                recurso.id_tipo_recurso
                                            "
                                            :tarea="recurso.recurso_tarea"
                                            :foro="recurso.recurso_foro"
                                            :examen="recurso.recurso_examen"
                                            :tipo="recurso.tipo"
                                            :icon="recurso.tipo_recurso.icono"
                                            :url="recurso.url"
                                            :id="recurso.id"
                                            :mostrar="recurso.mostrar == 'A'"
                                            :uuid_curso_matricula="curso.uuid"
                                            @edit="editarActividad(recurso)"
                                        />
                                    </div>

                                    <AddActivity
                                        title="Añadir una actividad o un recurso"
                                        @add="createActividades(clase.id)"
                                    />
                                    <ImportActivity
                                        @id_seccion="setIdSeccion(clase.id)"
                                        title="Importar una actividad o un recurso"
                                        @add="openModalImport"
                                    />
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </Card>
            <Modal ref="modalSecciones" :loading="formSeccion.processing">
                <template #title>Nueva sección</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Nombre"
                                :requerido="true"
                                :modelValue="formSeccion.nombre"
                                :autoValidate="true"
                                :autoHasError="errors?.nombre"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'nombre',
                                            formSeccion
                                        )
                                "
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Descripcion"
                                :requerido="true"
                                :modelValue="formSeccion.descripcion"
                                :autoValidate="true"
                                :autoHasError="errors?.descripcion"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'descripcion',
                                            formSeccion
                                        )
                                "
                            />
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeSeccion"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeSeccion"
                        :disabled="formSeccion.processing"
                    >
                        <feather-icon
                            :icon="!seccionEditMode ? 'PlusIcon' : 'EditIcon'"
                        />
                        <span>{{ !seccionEditMode ? "Crear" : "Editar" }}</span>
                    </button>
                </template>
            </Modal>
            <Modal ref="modalActividades" :loading="formUrl.processing">
                <template #title
                    >{{ !actividadEditMode ? "Nuevo(a)" : "Editar" }}
                    {{
                        page == 0
                            ? "actividad"
                            : page == 2
                            ? "enlace"
                            : page == 3
                            ? "tarea"
                            : page == 4
                            ? "foro"
                            : page == 6 && "examen"
                    }}</template
                >
                <template v-if="page == 0">
                    <div class="activity-container">
                        <div
                            v-for="recurso in tipos_recursos"
                            :key="recurso.id"
                            class="activity-item"
                            @click="goPage(recurso.id)"
                        >
                            <feather-icon :icon="recurso.icono" size="14" />
                            <p class="p-0 m-0">{{ recurso.nombre }}</p>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div>
                        <div class="tmplate" v-if="!actividadEditMode">
                            <feather-icon
                                icon="ArrowLeftIcon"
                                size="26"
                                @click="resetPage"
                                style="cursor: pointer"
                            />
                        </div>
                        <template v-if="page == 2">
                            <form class="form form-vertical mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <InputSchedule
                                            label="Nombre"
                                            :requerido="true"
                                            :modelValue="
                                                formUrl.nombre_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.nombre_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'nombre_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <InputSchedule
                                            label="Descripcion"
                                            :requerido="false"
                                            :modelValue="
                                                formUrl.descripcion_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.descripcion_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'descripcion_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <InputSchedule
                                            label="URL"
                                            :requerido="true"
                                            :modelValue="formUrl.url"
                                            :autoValidate="true"
                                            :autoHasError="errors?.url"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'url',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template v-if="page == 3">
                            <form class="form form-vertical mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Nombre"
                                            :modelValue="
                                                formUrl.nombre_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.nombre_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'nombre_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Descripcion"
                                            :modelValue="
                                                formUrl.descripcion_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.descripcion_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'descripcion_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <SelectSchedule
                                            label="Tipo"
                                            :modelValue="formUrl.tipo"
                                            :hasError="errors?.tipo"
                                            :customizeOptions="true"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'tipo',
                                                        formUrl
                                                    )
                                            "
                                        >
                                            <template #default>
                                                <option>URL</option>
                                                <option>ARCHIVO</option>
                                            </template>
                                        </SelectSchedule>
                                    </div>
                                    <div
                                        v-if="formUrl.tipo === 'URL'"
                                        class="col-md-12 col-lg-6"
                                    >
                                        <InputSchedule
                                            label="URL"
                                            :modelValue="formUrl.url"
                                            :autoValidate="true"
                                            :autoHasError="errors?.url"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'url',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                    <div
                                        v-if="formUrl.tipo === 'ARCHIVO'"
                                        class="col-md-12 col-lg-6"
                                    >
                                        <!--juan-->
                                        <!-- <input label="ARCHIVO" type="file" /> -->
                                        <div class="form-group">
                                            <label>ARCHIVO</label>
                                            <div class="input-group">
                                                <b-form-file
                                                    placeholder="Elige un archivo..."
                                                    :browse-text="'Subir'"
                                                    v-model="formUrl.archivo"
                                                >
                                                </b-form-file>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <p>Detalle de la tarea</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha inicio"
                                            type="date"
                                            :modelValue="formTarea.fecha_inicio"
                                            :autoValidate="true"
                                            :autoHasError="errors?.fecha_inicio"
                                            :min="actualDate"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_inicio',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha fin"
                                            type="date"
                                            :min="actualDate"
                                            :modelValue="formTarea.fecha_fin"
                                            :autoValidate="true"
                                            :autoHasError="errors?.fecha_fin"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_fin',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora inicio"
                                            type="time"
                                            :modelValue="formTarea.hora_inicio"
                                            :autoValidate="true"
                                            :autoHasError="errors?.hora_inicio"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_inicio',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora fin"
                                            type="time"
                                            :modelValue="formTarea.hora_fin"
                                            :autoValidate="true"
                                            :autoHasError="errors?.hora_fin"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_fin',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <SelectSchedule
                                            label="Tipo de archivos"
                                            :modelValue="
                                                formTarea.tipo_archivos
                                            "
                                            :options="tipos_archivos"
                                            :hasError="errors?.tipo_archivos"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'tipo_archivos',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Numero de intentos"
                                            :modelValue="
                                                formTarea.numero_intento
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.numero_intento
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'numero_intento',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Puntaje"
                                            :modelValue="formTarea.puntaje"
                                            :autoValidate="true"
                                            :autoHasError="errors?.puntaje"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'puntaje',
                                                        formTarea
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template v-if="page == 4">
                            <form class="form form-vertical mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Nombre"
                                            :modelValue="
                                                formUrl.nombre_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.nombre_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'nombre_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Descripcion"
                                            :modelValue="
                                                formUrl.descripcion_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.descripcion_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'descripcion_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <p>Detalle de la tarea</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha inicio"
                                            type="date"
                                            :modelValue="
                                                formForo.fecha_inicio_foro
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.fecha_inicio_foro
                                            "
                                            :min="actualDate"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_inicio_foro',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha fin"
                                            type="date"
                                            :min="actualDate"
                                            :modelValue="
                                                formForo.fecha_fin_foro
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.fecha_fin_foro
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_fin_foro',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora inicio"
                                            type="time"
                                            :modelValue="
                                                formForo.hora_inicio_foro
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.hora_inicio_foro
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_inicio_foro',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora fin"
                                            type="time"
                                            :modelValue="formForo.hora_fin_foro"
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.hora_fin_foro
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_fin_foro',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <!-- <InputSchedule
                                            label="Semana"
                                            :modelValue="formForo.semana"
                                            :autoValidate="true"
                                            :autoHasError="errors?.semana"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'semana',
                                                        formForo
                                                    )
                                            "
                                        /> -->
                                        <SelectSchedule
                                            label="Semana"
                                            :modelValue="formForo.semana"
                                            :hasError="errors?.semana"
                                            :customizeOptions="true"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'semana',
                                                        formForo
                                                    )
                                            "
                                        >
                                            <template #default>
                                                <option
                                                    v-for="item in semanas"
                                                    :value="item.id"
                                                >
                                                    {{ item.nombre }}
                                                </option>
                                            </template>
                                        </SelectSchedule>
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <SelectSchedule
                                            label="Modo de calificación"
                                            :modelValue="
                                                formForo.modo_calificacion
                                            "
                                            :options="[
                                                {
                                                    id: 'MAXIMA',
                                                    descripcion: 'MAXIMA',
                                                },
                                                {
                                                    id: 'PROMEDIO',
                                                    descripcion: 'PROMEDIO',
                                                },
                                            ]"
                                            :hasError="
                                                errors?.modo_calificacion
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'modo_calificacion',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Puntaje"
                                            :modelValue="formForo.puntaje"
                                            :autoValidate="true"
                                            :autoHasError="errors?.puntaje"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'puntaje',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Maximo de comentarios"
                                            :modelValue="
                                                formForo.maximo_comentarios
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.maximo_comentarios
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'maximo_comentarios',
                                                        formForo
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template v-if="page == 6">
                            <form class="form form-vertical mt-1">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Nombre"
                                            :modelValue="
                                                formUrl.nombre_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.nombre_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'nombre_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Descripcion"
                                            :modelValue="
                                                formUrl.descripcion_actividad
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.descripcion_actividad
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'descripcion_actividad',
                                                        formUrl
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <p>Detalle del examen</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha inicio"
                                            type="date"
                                            :modelValue="
                                                formExamen.fecha_inicio_examen
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.fecha_inicio_examen
                                            "
                                            :min="actualDate"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_inicio_examen',
                                                        formExamen
                                                    )
                                            "
                                            :onChange="updateDateEndExamen"
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora inicio"
                                            type="time"
                                            :modelValue="
                                                formExamen.hora_inicio_examen
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.hora_inicio_examen
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_inicio_examen',
                                                        formExamen
                                                    )
                                            "
                                            :onChange="updateDateEndExamen"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <p>Duración</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora(s)"
                                            type="number"
                                            min="0"
                                            :modelValue="
                                                formExamen.duracion_hora
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.duracion_hora
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'duracion_hora',
                                                        formExamen
                                                    )
                                            "
                                            :onChange="updateDateEndExamen"
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Minuto(s)"
                                            type="number"
                                            min="0"
                                            :modelValue="
                                                formExamen.duracion_minuto
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.duracion_minuto
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'duracion_minuto',
                                                        formExamen
                                                    )
                                            "
                                            :onChange="updateDateEndExamen"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Fecha fin"
                                            type="date"
                                            :min="actualDate"
                                            :modelValue="
                                                formExamen.fecha_fin_examen
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.fecha_fin_examen
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'fecha_fin_examen',
                                                        formExamen
                                                    )
                                            "
                                            :disabled="true"
                                        />
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Hora fin"
                                            type="time"
                                            :modelValue="
                                                formExamen.hora_fin_examen
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.hora_fin_examen
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'hora_fin_examen',
                                                        formExamen
                                                    )
                                            "
                                            :disabled="true"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <SelectSchedule
                                            label="Semana"
                                            :modelValue="formExamen.semana"
                                            :hasError="errors?.semana"
                                            :customizeOptions="true"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'semana',
                                                        formExamen
                                                    )
                                            "
                                        >
                                            <template #default>
                                                <option
                                                    v-for="item in semanas"
                                                    :value="item.id"
                                                >
                                                    {{ item.nombre }}
                                                </option>
                                            </template>
                                        </SelectSchedule>
                                    </div>
                                    <!-- <div class="col-md-12 col-lg-6">
                                        <InputSchedule
                                            label="Numero de intento"
                                            :type="'number'"
                                            :modelValue="
                                                formExamen.numero_intento
                                            "
                                            :autoValidate="true"
                                            :autoHasError="
                                                errors?.numero_intento
                                            "
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'numero_intento',
                                                        formExamen
                                                    )
                                            "
                                        />
                                    </div> -->
                                    <!-- <div class="col-md-12 col-lg-6">
                                        <SelectSchedule
                                            label="Barajar"
                                            :modelValue="formExamen.barajar"
                                            :options="[
                                                {
                                                    id: 'S',
                                                    descripcion: 'SI',
                                                },
                                                {
                                                    id: 'N',
                                                    descripcion: 'NO',
                                                },
                                            ]"
                                            :hasError="errors?.barajar"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'barajar',
                                                        formExamen
                                                    )
                                            "
                                        />
                                    </div> -->
                                    <!-- <div class="col-md-12 col-lg-6">
                                        <SelectSchedule
                                            label="Tiempo por pregunta"
                                            :modelValue="
                                                formExamen.tiempo_pregunta
                                            "
                                            :options="[
                                                {
                                                    id: 'S',
                                                    descripcion: 'SI',
                                                },
                                                {
                                                    id: 'N',
                                                    descripcion: 'NO',
                                                },
                                            ]"
                                            :hasError="errors?.tiempo_pregunta"
                                            @update:modelValue="
                                                (value) =>
                                                    updateField(
                                                        value,
                                                        'tiempo_pregunta',
                                                        formExamen
                                                    )
                                            "
                                        />
                                    </div> -->
                                </div>
                            </form>
                        </template>
                    </div>
                </template>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeActividades"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        v-if="page != 0"
                        class="btn btn-success"
                        @click.prevent="storeActividades"
                        :disabled="formUrl.processing"
                    >
                        <feather-icon
                            :icon="!actividadEditMode ? 'PlusIcon' : 'EditIcon'"
                        />
                        <span>{{
                            !actividadEditMode ? "Guardar" : "Editar"
                        }}</span>
                    </button>
                </template>
            </Modal>
            <Modal ref="modalImport" modal="modal-lg">
                <template #title>Importar Actividad</template>
                <div class="row mb-2">
                    <div class="col-md-3 col-lg-3">
                        <label>Periodo de clases</label>
                        <select
                            class="form-control"
                            @change="
                                getSeccionesCursoDocente(), changePeriodo()
                            "
                            v-model="id_periodo_clases"
                        >
                            <option v-for="item in periodo" :value="item.id">
                                {{ item.descripcion }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label>Secciones</label>
                        <select
                            class="form-control"
                            :disabled="seccionesImport == 0"
                            v-model="id_seccion"
                            @change="changeSeccion"
                        >
                            <option
                                v-for="item in seccionesImport"
                                :value="item.id"
                            >
                                {{ item.nombre }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label>Tipo de recurso</label>
                        <select
                            class="form-control"
                            v-model="id_tipo_recurso"
                            @change="getActividadesImport"
                            :disabled="id_seccion == '' ? true : false"
                        >
                            <option
                                value="todos"
                                v-for="item in tipos_recursos"
                                :value="item.nombre"
                            >
                                {{ item.nombre }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="import-modal" v-if="actividadesModal.length != 0">
                    <div>
                        <p><strong>Actividades:</strong></p>
                        <div class="import-actividad">
                            <div
                                class="not-length"
                                v-if="loadingActividadesImp"
                            >
                                <BSpinner
                                    variant="success"
                                    type="grow"
                                ></BSpinner>
                            </div>
                            <div
                                class="not-length"
                                v-if="actividades.length == 0"
                            >
                                <feather-icon icon="InboxIcon" />
                                <span class="align-small">Sin recursos</span>
                            </div>
                            <section
                                style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: start;
                                    width: 100%;
                                    gap: 15px;
                                    margin-top: 10px;

                                    border-bottom: 2px solid #e2e2e2;
                                "
                                v-for="(item, index) in actividades"
                                :key="item.id"
                            >
                                <section
                                    style="
                                        display: flex;
                                        gap: 20px;
                                        justify-content: space-between;
                                        margin-left: 20px;
                                        margin-right: 20px;
                                        width: 100%;
                                    "
                                >
                                    <p
                                        class="media-heading"
                                        style="
                                            display: flex;
                                            flex-direction: column;
                                            justify-content: center;
                                            margin-bottom: 0;
                                        "
                                    >
                                        <span
                                            class="font-weight-bolder"
                                            title="notification.descripcio"
                                        >
                                            <feather-icon :icon="item.icono" />
                                            {{ item.nombre }}
                                        </span>

                                        <span
                                            class="font-weight-normal"
                                            style="font-size: 11px"
                                        >
                                            {{
                                                convertDate(
                                                    item.fecha_inicio,
                                                    "-",
                                                    "/"
                                                )
                                            }}
                                            -
                                            {{
                                                convertDate(
                                                    item.fecha_fin,
                                                    "-",
                                                    "/"
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="font-weight-normal"
                                            style="font-size: 11px"
                                        >
                                            {{ item.hora_inicio }} -
                                            {{ item.hora_fin }}
                                        </span>
                                    </p>
                                    <section
                                        style="
                                            display: flex;
                                            align-items: center;

                                            gap: 10px;
                                        "
                                    >
                                        <button
                                            title="Importar actividad"
                                            class="btn btn-success btn-icon"
                                            @click="addImportActividades(index)"
                                        >
                                            <feather-icon
                                                icon="ArrowRightIcon"
                                            ></feather-icon>
                                        </button>
                                    </section>
                                </section>
                            </section>
                        </div>
                    </div>
                    <div>
                        <section class="import-modal_button-todo">
                            <button
                                title="Agregar Todo"
                                class="btn btn-primary"
                                @click="addImportActividadesTodo"
                            >
                                <feather-icon
                                    icon="ArrowRightIcon"
                                ></feather-icon>
                            </button>
                            <button
                                title="Deshacer Todo"
                                class="btn btn-primary"
                                @click="cancelAddActividadesTodo"
                            >
                                <feather-icon
                                    icon="ArrowLeftIcon"
                                ></feather-icon>
                            </button>
                        </section>
                    </div>
                    <div>
                        <p><strong>Actividades importadas:</strong></p>
                        <div class="import-actividad">
                            <div
                                class="not-length"
                                v-if="actividadesImportadas.length == 0"
                            >
                                <feather-icon icon="InboxIcon" />
                                <span class="align-small">Sin recursos</span>
                            </div>
                            <section
                                style="
                                    display: flex;
                                    align-items: center;
                                    width: 100%;
                                    gap: 15px;
                                    margin-top: 10px;
                                    border-bottom: 2px solid #e2e2e2;
                                "
                                v-for="(item, index) in actividadesImportadas"
                                :key="item.id"
                            >
                                <section
                                    style="
                                        display: flex;
                                        gap: 20px;
                                        justify-content: space-between;
                                        margin-left: 20px;
                                        margin-right: 20px;
                                        width: 100%;
                                    "
                                >
                                    <p
                                        class="media-heading"
                                        style="
                                            display: flex;
                                            flex-direction: column;
                                            justify-content: center;
                                            margin-bottom: 0;
                                        "
                                    >
                                        <span
                                            class="font-weight-bolder"
                                            title="notification.descripcio"
                                        >
                                            <feather-icon :icon="item.icono" />

                                            {{ item.nombre }}
                                        </span>

                                        <span
                                            class="font-weight-normal"
                                            style="font-size: 11px"
                                        >
                                            {{
                                                convertDate(
                                                    item.fecha_inicio,
                                                    "-",
                                                    "/"
                                                )
                                            }}
                                            -
                                            {{
                                                convertDate(
                                                    item.fecha_fin,
                                                    "-",
                                                    "/"
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="font-weight-normal"
                                            style="font-size: 11px"
                                        >
                                            {{ item.hora_inicio }} -
                                            {{ item.hora_fin }}
                                        </span>
                                    </p>
                                    <section
                                        style="
                                            display: flex;
                                            align-items: center;

                                            gap: 10px;
                                        "
                                    >
                                        <button
                                            title="Importar actividad"
                                            class="btn btn-success btn-icon"
                                            @click="cancelAddActividades(index)"
                                        >
                                            <feather-icon
                                                icon="ArrowLeftIcon"
                                            ></feather-icon>
                                        </button>
                                    </section>
                                </section>
                            </section>
                        </div>
                    </div>
                </div>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeModalImport"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click="storeMigrateActividades(index)"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Guardar</span>
                    </button>
                </template>
            </Modal>
        </div>
    </LayoutContent>
</template>

<script>
import { useForm } from "@inertiajs/vue2";
import { FeatherIcon } from "vue-feather-icons";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import Modal from "../../Components/Modal.vue";
import AddActivity from "./components/AddActivity.vue";
import Activity from "./components/Activity.vue";
import DropdownMenu from "./components/DropdownMenu.vue";
import InputSchedule from "../Schedule/components/InputSchedule.vue";
import SelectSchedule from "../Schedule/components/SelectSchedule.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import { capitalizeFirstWord } from "../../utils/textProcess";
import { cleanObject } from "../../utils/objectProcess";
import {
    alertError,
    alertSuccess,
    alertWarning,
    confirm,
} from "../../sweetAlert2";
import { subtractDates, subtractTimes } from "../../utils/timeProcess";
import { addMinutes, format, parse } from "date-fns";
import ImportActivity from "./components/ImportActivity.vue";
import { convertDate } from "../../helpers";
import { BBadge, BSpinner } from "bootstrap-vue";
import { objectToFormData } from "../../formData";

export default {
    components: {
        LayoutContent,
        Card,
        AddActivity,
        Activity,
        DropdownMenu,
        Modal,
        InputSchedule,
        SelectSchedule,
        ImportActivity,
        BSpinner,
        BBadge,
        ReturnButton,
    },
    name: "Classroom",
    props: {
        urlRoute: String,
        curso: Object,
        secciones: Array,
        tipos_recursos: Array,
        tipos_archivos: Array,
        periodo: Array,
        secciones: Array,
        tipos_recursos: Array,
        uuid: String,
        semanas: Array,
    },
    data() {
        return {
            loadingActividadesImp: false,
            id_periodo_clases: "",
            id_seccion: "",
            id_tipo_recurso: "",
            actividades: [],
            id_curso_docente_seccion_import: "0",
            actividadesImportadas: [],
            seccionesImport: "",
            formSeccion: useForm({
                nombre: "",
                descripcion: "",
            }),
            formUrl: useForm({
                id_tipo_recurso: "",
                nombre_actividad: "",
                descripcion_actividad: "",
                tipo: "",
                url: "",
                archivo: "",
            }),
            formTarea: useForm({
                fecha_inicio: "",
                fecha_fin: "",
                hora_inicio: "",
                hora_fin: "",
                tipo_archivos: "",
                numero_intento: 1,
                puntaje: 20,
            }),
            formForo: useForm({
                fecha_inicio_foro: "",
                fecha_fin_foro: "",
                hora_inicio_foro: "",
                hora_fin_foro: "",
                semana: "",
                modo_calificacion: "0",
                puntaje: 20,
                maximo_comentarios: 1,
            }),
            formExamen: useForm({
                fecha_inicio_examen: "",
                fecha_fin_examen: "",
                hora_inicio_examen: "",
                hora_fin_examen: "",
                duracion_hora: 0,
                duracion_minuto: 0,
                semana: "",
                // numero_intento: 1,
                // barajar: "S",
                // tiempo_pregunta: "N",
            }),
            actividadesModal: [],
            visibleSeccion: [], // IDs de las secciones visibles
            activeDropdown: null, // ID del dropdown activo
            secciones_data: [],
            id_curso_docente_seccion: 0,
            page: 0,
            actualDate: "",
            seccionEditMode: false,
            seccionEditId: 0,
            actividadEditMode: false,
            actividadEditId: 0,
            loading: false,
        };
    },
    mounted() {
        console.log(this.secciones);
        this.secciones_data = this.secciones || [];
        this.setCurrentDate();
    },
    methods: {
        convertDate,
        addImportActividadesTodo() {
            this.actividadesImportadas = [
                ...this.actividades,
                ...this.actividadesImportadas,
            ];
            this.actividadesImportadas = this.actividadesImportadas.sort(
                (a, b) => a.id - b.id
            );
            this.actividades = [];
        },
        cancelAddActividadesTodo() {
            this.actividades = [
                ...this.actividades,
                ...this.actividadesImportadas,
            ];
            this.actividades = this.actividades.sort((a, b) => a.id - b.id);
            this.actividadesImportadas = [];
        },
        addImportActividades(index) {
            console.log(index);
            this.actividadesImportadas.push(this.actividades[index]);
            this.actividades.splice(index, 1);
            this.actividadesImportadas = this.actividadesImportadas.sort(
                (a, b) => a.id - b.id
            );
        },
        cancelAddActividades(index) {
            this.actividades.push(this.actividadesImportadas[index]);
            this.actividadesImportadas.splice(index, 1);
            this.actividades = this.actividades.sort((a, b) => a.id - b.id);
        },
        changePeriodo() {
            this.id_tipo_recurso = "";
            this.id_seccion = "";
        },
        changeSeccion() {
            this.id_tipo_recurso = "";
        },
        storeMigrateActividades() {
            this.loadingActividadesImp = true;
            const data = {
                tipo_recurso: this.id_tipo_recurso,
                seccion_curso_docente: this.id_curso_docente_seccion_import,
                uuid: this.uuid,
                recursos: this.actividadesImportadas,
            };
            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/virtual-classroom/import-recursos/store`
                ),
                data: data,
                headers: {
                    "X-Inertia-Error-Bag": "recursos",
                },
            })
                .then(() => {
                    alertSuccess("Datos Actualizados Correctamente");
                    this.loadingActividadesImp = false;
                    this.$inertia.reload({ only: ["secciones"] });
                })
                .finally(() => {
                    this.closeModalImport();
                });
        },
        getActividadesImport() {
            let tipo_recurso = this.id_tipo_recurso;
            if (this.id_tipo_recurso == "Link/Url") {
                tipo_recurso = "Link";
            }
            //ID_TIPO_RECURSO no envía el id si no el tipo ejemplo:"Tareas,Foros,etc"
            this.$http
                .get(
                    this.routeTo(
                        `gestion-cursos/virtual-classroom/import-recursos/${this.id_seccion}/${tipo_recurso}`
                    )
                )
                .then((res) => {
                    if (res.data.length == 0) {
                        alertWarning(
                            "No se encontraron actividades en esta sección"
                        );
                    }
                    this.actividades = res.data;
                    this.actividadesModal = [
                        ...res.data,
                        ...this.actividadesModal,
                    ];
                    this.loading = false;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        getSeccionesCursoDocente() {
            this.loading = true;
            this.$http
                .get(
                    this.routeTo(
                        `gestion-cursos/virtual-classroom/${this.curso.id_curso}/${this.id_periodo_clases}`
                    )
                )
                .then((res) => {
                    if (res.data.length == 0) {
                        alertWarning(
                            "No se encontraron secciones en este periodo"
                        );
                    }
                    this.seccionesImport = res.data;
                    this.loading = false;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        openModalImport() {
            this.$refs.modalImport.show();
            this.id_periodo_clases = "";
            this.id_seccion = "";
            this.seccionesImport = [];
            this.id_tipo_recurso = "";
            this.actividades = [];
            this.actividadesImportadas = [];
        },
        closeModalImport() {
            this.$refs.modalImport.hide();
        },
        toggleClase(id) {
            // Alternar la visibilidad de la clase
            if (this.visibleSeccion.includes(id)) {
                this.visibleSeccion = this.visibleSeccion.filter(
                    (claseId) => claseId !== id
                );
            } else {
                this.visibleSeccion.push(id);
            }
            this.activeDropdown = null; // Cerrar el dropdown activo al abrir/cerrar
        },
        setIdSeccion(id) {
            this.id_curso_docente_seccion_import = id;
        },
        isVisible(id) {
            return this.visibleSeccion.includes(id); // Verificar si la clase está visible
        },
        beforeEnter(el) {
            el.style.height = "0";
        },
        enter(el) {
            el.style.height = el.scrollHeight + "px";
        },
        leave(el) {
            el.style.height = el.scrollHeight + "px";
            requestAnimationFrame(() => {
                el.style.height = "0";
            });
        },
        toggleDropdown(id) {
            // Alternar el estado del dropdown
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
        },
        borrarSeccion(id) {
            confirm(
                {
                    text: "¿Desea eliminar la sección?",
                },
                () => {
                    this.$http({
                        method: "DELETE",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/${id}`
                        ),
                    }).then(() => {
                        alertSuccess("Seccion eliminada correctamente");
                        this.$inertia.reload({ only: ["secciones"] });
                    });
                }
            );

            console.log(`Borrando ${id}`);
        },
        editarSeccion(seccion) {
            this.seccionEditId = seccion.id;

            this.formSeccion.nombre = seccion.nombre;
            this.formSeccion.descripcion = seccion.descripcion;

            this.$refs.modalSecciones.show();

            this.seccionEditMode = true;
        },
        editarActividad(recurso) {
            console.log("recurso", recurso);

            this.createActividades(recurso.id_curso_docente_seccion);

            this.actividadEditId = recurso.id;
            this.actividadEditMode = true;
            this.page = recurso.id_tipo_recurso;

            if (this.page == 2) {
                this.formUrl.id_tipo_recurso = recurso.id_tipo_recurso;
                this.formUrl.nombre_actividad = recurso.nombre;
                this.formUrl.descripcion_actividad = recurso.descripcion;
                this.formUrl.tipo = recurso.tipo;
                this.formUrl.url = recurso.url;
            } else if (this.page == 3) {
                this.formUrl.id_tipo_recurso = recurso.id_tipo_recurso;
                this.formUrl.nombre_actividad = recurso.nombre;
                this.formUrl.descripcion_actividad = recurso.descripcion;
                this.formUrl.tipo = recurso.tipo;
                this.formUrl.url = recurso.url;

                // Parsear fecha_inicio y fecha_fin
                const fecha_inicio = parse(
                    recurso.recurso_tarea.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formTarea.fecha_inicio = format(
                    fecha_inicio,
                    "yyyy-MM-dd"
                );

                const fecha_fin = parse(
                    recurso.recurso_tarea.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formTarea.fecha_fin = format(fecha_fin, "yyyy-MM-dd");

                // Parsear hora_inicio y hora_fin
                const hora_inicio = parse(
                    recurso.recurso_tarea.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formTarea.hora_inicio = format(hora_inicio, "HH:mm");

                const hora_fin = parse(
                    recurso.recurso_tarea.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formTarea.hora_fin = format(hora_fin, "HH:mm");

                this.formTarea.tipo_archivos = recurso.recurso_tarea
                    .tipo_archivos
                    ? recurso.recurso_tarea.tipo_archivos
                    : "";

                this.formTarea.numero_intento =
                    recurso.recurso_tarea.numero_intento;

                this.formTarea.puntaje = recurso.recurso_tarea.puntaje;
            } else if (this.page == 4) {
                this.formUrl.id_tipo_recurso = recurso.id_tipo_recurso;
                this.formUrl.nombre_actividad = recurso.nombre;
                this.formUrl.descripcion_actividad = recurso.descripcion;

                // Parsear fecha_inicio y fecha_fin
                const fecha_inicio_foro = parse(
                    recurso.recurso_foro.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formForo.fecha_inicio_foro = format(
                    fecha_inicio_foro,
                    "yyyy-MM-dd"
                );

                const fecha_fin_foro = parse(
                    recurso.recurso_foro.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formForo.fecha_fin_foro = format(
                    fecha_fin_foro,
                    "yyyy-MM-dd"
                );

                // Parsear hora_inicio y hora_fin
                const hora_inicio_foro = parse(
                    recurso.recurso_foro.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formForo.hora_inicio_foro = format(
                    hora_inicio_foro,
                    "HH:mm"
                );

                const hora_fin_foro = parse(
                    recurso.recurso_foro.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formForo.hora_fin_foro = format(hora_fin_foro, "HH:mm");

                this.formForo.semana = recurso.recurso_foro.semana;
                this.formForo.modo_calificacion =
                    recurso.recurso_foro.modo_calificacion;
                this.formForo.puntaje = recurso.recurso_foro.puntaje;
                this.formForo.maximo_comentarios =
                    recurso.recurso_foro.maximo_comentarios;
            } else if (this.page == 6) {
                this.formUrl.id_tipo_recurso = recurso.id_tipo_recurso;
                this.formUrl.nombre_actividad = recurso.nombre;
                this.formUrl.descripcion_actividad = recurso.descripcion;

                // Parsear fecha_inicio y fecha_fin
                const fecha_inicio_examen = parse(
                    recurso.recurso_examen.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formExamen.fecha_inicio_examen = format(
                    fecha_inicio_examen,
                    "yyyy-MM-dd"
                );

                const fecha_fin_examen = parse(
                    recurso.recurso_examen.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formExamen.fecha_fin_examen = format(
                    fecha_fin_examen,
                    "yyyy-MM-dd"
                );

                // Parsear hora_inicio y hora_fin
                const hora_inicio_examen = parse(
                    recurso.recurso_examen.fecha_inicio,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formExamen.hora_inicio_examen = format(
                    hora_inicio_examen,
                    "HH:mm"
                );

                const hora_fin_examen = parse(
                    recurso.recurso_examen.fecha_fin,
                    "yyyy-MM-dd HH:mm:ss",
                    new Date()
                );
                this.formExamen.hora_fin_examen = format(
                    hora_fin_examen,
                    "HH:mm"
                );

                const duracion = recurso.recurso_examen.duracion;

                this.formExamen.duracion_hora = Math.floor(duracion / 60); // Horas completas
                this.formExamen.duracion_minuto = duracion % 60; // Minutos restantes

                this.formExamen.semana = recurso.recurso_examen.semana;

                // this.formExamen.numero_intento =
                //     recurso.recurso_examen.numero_intento;

                // this.formExamen.barajar = recurso.recurso_examen.barajar;

                // this.formExamen.tiempo_pregunta =
                //     recurso.recurso_examen.tiempo_pregunta;
            }
        },
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        goPage(id) {
            this.page = id;
            this.formUrl.id_tipo_recurso = id;
        },
        resetPage() {
            this.page = 0;
        },
        setCurrentDate() {
            // Obtén la fecha actual en tu zona horaria
            const currentDate = new Date();

            // Ajusta la fecha para que coincida con tu zona horaria
            const modifyDate = new Date(
                currentDate.getTime() - currentDate.getTimezoneOffset() * 60000
            );

            // Formatea la fecha en el formato YYYY-MM-DD
            const formattedDate = modifyDate.toISOString().split("T")[0];

            this.actualDate = formattedDate;
        },

        // VERIFY

        verifyDiferenceDate(date1, date2) {
            const result = subtractDates(date1, date2);

            if (result < 0) {
                // Si la fecha de fin es anterior a la de inicio
                return {
                    invalid: true,
                    message:
                        "La fecha de fin no puede ser anterior a la fecha de inicio",
                };
            } else {
                return {
                    invalid: false,
                    message: "",
                };
            }
        },

        verifyDiferenceTime(time1, time2, date1, date2) {
            if (date1 != date2)
                return {
                    invalid: false,
                    message: "",
                };

            const result = subtractTimes(time1, time2);

            if (result < "00:00") {
                return {
                    invalid: true,
                    message:
                        "La hora de fin no puede ser menor a la hora de inicio",
                };
            } else {
                return {
                    invalid: false,
                    message: "",
                };
            }
        },

        // UPDATE

        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        updateDateEndExamen() {
            const data = this.formExamen.data();
            const new_data = cleanObject(data);

            const fecha_inicio = new_data.fecha_inicio_examen; // Fecha de inicio
            const hora_inicio = new_data.hora_inicio_examen; // Hora de inicio
            const duracion_hora = this.formExamen.duracion_hora; // Duración en horas
            const duracion_minuto = this.formExamen.duracion_minuto; // Duración en minutos

            if (
                fecha_inicio &&
                hora_inicio &&
                (duracion_hora || duracion_minuto)
            ) {
                // 1. Combinar fecha_inicio y hora_inicio
                const fechaHoraInicio = parse(
                    `${fecha_inicio} ${hora_inicio}`,
                    "yyyy-MM-dd HH:mm",
                    new Date()
                );

                // 2. Calcular los minutos totales
                const minutos = duracion_hora * 60 + parseInt(duracion_minuto);

                console.log(minutos);

                // 3. Sumar los minutos a la fecha y hora de inicio
                const fechaHoraFin = addMinutes(fechaHoraInicio, minutos);

                // 4. Asignar fecha_fin y hora_fin
                this.formExamen.fecha_fin_examen = format(
                    fechaHoraFin,
                    "yyyy-MM-dd"
                );
                this.formExamen.hora_fin_examen = format(fechaHoraFin, "HH:mm");
            }
        },

        // MODALS

        createSeccion() {
            this.formSeccion.reset();
            this.seccionEditMode = false;
            this.seccionEditId = 0;
            this.$refs.modalSecciones.show();
        },

        closeSeccion() {
            this.$refs.modalSecciones.hide();
        },

        storeSeccion() {
            const data = this.formSeccion.data();
            const id_curso_docente = this.curso.id;
            const newData = {
                ...cleanObject(data),
                id_curso_docente,
            };

            console.log("data", newData);
            console.log(
                this.routeTo(`${this.urlRoute}/virtual-classroom/store`)
            );

            this.formSeccion.processing = true;

            if (!this.seccionEditMode) {
                this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/store`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "secciones",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.closeSeccion();
                        this.secciones_data.push({
                            ...res.data,
                            seccion_recurso: [],
                        });
                    })
                    .catch((err) => alertError(err.message))
                    .finally(() => {
                        this.formSeccion.processing = false;
                    });
            } else {
                this.$http({
                    method: "PUT",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/update/${this.seccionEditId}`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "secciones",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos actualizados Correctamente");
                        this.closeSeccion();
                        this.$inertia.reload({ only: ["secciones"] });
                        this.seccionEditId = 0;
                    })
                    .catch((err) => alertError(err.message))
                    .finally(() => {
                        this.formSeccion.processing = false;
                    });
            }
        },

        createActividades(id) {
            this.page = 0;
            this.formUrl.reset();
            this.formTarea.reset();
            this.formForo.reset();
            this.formExamen.reset();
            this.actividadEditMode = false;
            this.actividadEditId = 0;
            this.$refs.modalActividades.show();
            this.id_curso_docente_seccion = id;
        },

        closeActividades() {
            this.$refs.modalActividades.hide();
        },

        storeActividades() {
            if (this.page == 2) {
                const id_curso_docente_seccion = this.id_curso_docente_seccion;
                const data = this.formUrl.data();
                const { url, ...dataToClean } = data;
                const dataClean = cleanObject(dataToClean);

                const newData = {
                    id_curso_docente_seccion,
                    url,
                    ...dataClean,
                };

                this.formUrl.processing = true;

                if (this.actividadEditMode) {
                    this.$http({
                        method: "PUT",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos/update/${this.actividadEditId}`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursos",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Guardados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.page = 0;
                            this.actividadEditMode = false;

                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .finally(() => {
                            this.formUrl.processing = false;
                        });
                } else {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos/store`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursos",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Actualizados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.page = 0;

                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .finally(() => {
                            this.formUrl.processing = false;
                            this.toggleClase(id_curso_docente_seccion);
                        });
                }
            } else if (this.page == 3) {
                const id_curso_docente_seccion = this.id_curso_docente_seccion;
                const dataActividad = this.formUrl.data();
                const { url, archivo, ...dataToClean } = dataActividad;
                const dataClean = cleanObject(dataToClean);

                const verifyDiferenceDate = this.verifyDiferenceDate(
                    this.formTarea.fecha_fin,
                    this.formTarea.fecha_inicio
                );

                if (verifyDiferenceDate.invalid) {
                    alertWarning(verifyDiferenceDate.message);
                    return;
                }

                const verifyDiferenceTime = this.verifyDiferenceTime(
                    this.formTarea.hora_fin,
                    this.formTarea.hora_inicio,
                    this.formTarea.fecha_fin,
                    this.formTarea.fecha_inicio
                );

                if (verifyDiferenceTime.invalid) {
                    alertWarning(verifyDiferenceTime.message);
                    return;
                }

                const dataTarea = this.formTarea.data();
                const {
                    tipo_archivos,
                    puntaje,
                    numero_intento,
                    ...dataTareaToClean
                } = dataTarea;
                const dataTareaClean = cleanObject(dataTareaToClean);

                const newData = {
                    id_curso_docente_seccion,
                    url,
                    archivo,
                    tipo_archivos,
                    puntaje: Number(puntaje),
                    numero_intento: Number(numero_intento),
                    ...dataClean,
                    ...dataTareaClean,
                };

                console.log("new_data", newData);

                this.formUrl.processing = true;
                this.formTarea.processing = true;
                if (this.actividadEditMode) {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-tareas/update/${this.actividadEditId}`
                        ),
                        data: objectToFormData(newData),
                    })
                        .then(() => {
                            alertSuccess("Datos Actualizados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formTarea.reset();
                            this.page = 0;

                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.formTarea.processing = false;
                            this.formUrl.processing = false;
                        });
                } else {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-tareas/store`
                        ),
                        data: objectToFormData(newData),
                        headers: {
                            "X-Inertia-Error-Bag": "recursosTareas",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Guardados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formTarea.reset();
                            this.page = 0;

                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.toggleClase(id_curso_docente_seccion);
                            this.formTarea.processing = false;
                            this.formUrl.processing = false;
                        });
                }
            } else if (this.page == 4) {
                const id_curso_docente_seccion = this.id_curso_docente_seccion;
                const dataActividad = this.formUrl.data();
                const { url, tipo, ...dataToClean } = dataActividad;
                const dataClean = cleanObject(dataToClean);

                const verifyDiferenceDate = this.verifyDiferenceDate(
                    this.formForo.fecha_fin_foro,
                    this.formForo.fecha_inicio_foro
                );

                if (verifyDiferenceDate.invalid) {
                    alertWarning(verifyDiferenceDate.message);
                    return;
                }

                const verifyDiferenceTime = this.verifyDiferenceTime(
                    this.formForo.hora_fin_foro,
                    this.formForo.hora_inicio_foro,
                    this.formForo.fecha_fin_foro,
                    this.formForo.fecha_inicio_foro
                );

                if (verifyDiferenceTime.invalid) {
                    alertWarning(verifyDiferenceTime.message);
                    return;
                }

                const dataForo = this.formForo.data();
                const {
                    semana,
                    puntaje,
                    maximo_comentarios,
                    ...dataForoToClean
                } = dataForo;
                const dataForoClean = cleanObject(dataForoToClean);

                const newData = {
                    id_curso_docente_seccion,
                    semana: Number(semana),
                    puntaje: Number(puntaje),
                    maximo_comentarios: Number(maximo_comentarios),
                    ...dataClean,
                    ...dataForoClean,
                };

                console.log("new_data", newData);

                this.formUrl.processing = true;
                this.formTarea.processing = true;

                if (this.actividadEditMode) {
                    this.$http({
                        method: "PUT",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-foros/update/${this.actividadEditId}`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursosForos",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Actualizados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formForo.reset();
                            this.page = 0;

                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.formForo.processing = false;
                            this.formUrl.processing = false;
                        });
                } else {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-foros/store`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursosForos",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Guardados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formForo.reset();
                            this.page = 0;
                            this.toggleClase(id_curso_docente_seccion);
                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.formForo.processing = false;
                            this.formUrl.processing = false;
                        });
                }
            } else if (this.page == 6) {
                const id_curso_docente_seccion = this.id_curso_docente_seccion;
                const dataActividad = this.formUrl.data();
                const { url, tipo, ...dataToClean } = dataActividad;
                const dataClean = cleanObject(dataToClean);

                const verifyDiferenceDate = this.verifyDiferenceDate(
                    this.formExamen.fecha_fin_examen,
                    this.formExamen.fecha_inicio_examen
                );

                if (verifyDiferenceDate.invalid) {
                    alertWarning(verifyDiferenceDate.message);
                    return;
                }

                const verifyDiferenceTime = this.verifyDiferenceTime(
                    this.formExamen.hora_fin_examen,
                    this.formExamen.hora_inicio_examen,
                    this.formExamen.fecha_fin_examen,
                    this.formExamen.fecha_inicio_examen
                );

                if (verifyDiferenceTime.invalid) {
                    alertWarning(verifyDiferenceTime.message);
                    return;
                }

                const dataExamen = this.formExamen.data();

                const { duracion_hora, duracion_minuto, data_examen } =
                    dataExamen;

                const newData = {
                    ...dataExamen,
                    duracion_hora: parseInt(duracion_hora),
                    duracion_minuto: parseInt(duracion_minuto),
                    ...dataClean,
                    id_curso_docente_seccion,
                };

                this.formUrl.processing = true;
                this.formExamen.processing = true;

                if (this.actividadEditMode) {
                    this.$http({
                        method: "PUT",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-examenes/update/${this.actividadEditId}`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursosExamenes",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Actualizados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formExamen.reset();
                            this.page = 0;

                            // Recargar los datos
                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.formExamen.processing = false;
                            this.formUrl.processing = false;
                        });
                } else {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `${this.urlRoute}/virtual-classroom/recursos-examenes/store`
                        ),
                        data: newData,
                        headers: {
                            "X-Inertia-Error-Bag": "recursosExamenes",
                        },
                    })
                        .then(() => {
                            alertSuccess("Datos Guardados Correctamente");
                            this.closeActividades();
                            this.formUrl.reset();
                            this.formExamen.reset();
                            this.page = 0;

                            this.$inertia.reload({ only: ["secciones"] });
                        })
                        .catch(() => {
                            console.error("Error");
                        })
                        .finally(() => {
                            this.toggleClase(id_curso_docente_seccion);
                            this.formExamen.processing = false;
                            this.formUrl.processing = false;
                        });
                }
            }
        },
    },
    computed: {
        errors() {
            return (
                this.$page.props.errors.secciones_data ||
                this.$page.props.errors.recursos ||
                this.$page.props.errors.recursosTareas ||
                this.$page.props.errors.recursosForos ||
                this.$page.props.errors.recursosExamenes
            );
        },
    },
    watch: {
        secciones: {
            immediate: true, // Ejecutar en el montaje
            handler(newSecciones) {
                this.secciones_data = [...newSecciones];
            },
        },
    },
};
</script>

<style scoped>
.badge-arrow:hover {
    border: 1px solid #28c76f;
}
.badge-arrow:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem #28c76f;
}

.import-modal {
    display: grid;
    grid-template-columns: 1fr 0.2fr 1fr;
}
.import-actividad {
    border-radius: 10px;
    height: 50vh;
    overflow: auto;
    border: 1px solid #e2e2e2;
}
.title-secciones_data {
    display: flex;
    justify-content: space-between;
}

.contenidos {
    border-radius: 10px;
}

.acordeon {
    position: relative;
}

.acordeon-container {
    border: 1px solid #e2e2e2;
    border-radius: 20px;
    margin-bottom: 15px;
}
/* .acordeon-container:last-child {
    border-bottom: none;
} */

.acordeon-header {
    padding: 1.5rem 0;
}

.acordeon-header__title {
    align-items: center;
    gap: 10px;
}

.acordeon-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    padding-bottom: 1.5rem;
}

.aniadir {
    cursor: pointer;
    font-size: 12px;
}

.aniadir:hover {
    text-decoration: underline;
}

.activity-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.activity-item {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
    gap: 4px;

    border: 1px solid #dcdcdc;
}

.activity-item:hover {
    background-color: #f5f5f5;
    cursor: pointer;
}

.activity-item p {
    font-size: 12px;
}

.tmplate {
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.modal-body {
    padding: 0 !important;
}

.empty {
    gap: 4px;
    p {
        font-size: 11px;
    }
}

/* Transiciones */
.slide-enter-active,
.slide-leave-active {
    transition: height 0.2s ease, opacity 0.2s ease;
}

.slide-enter,
.slide-leave-to {
    opacity: 0;
}
.import-modal_button-todo {
    height: 50vh;
    display: flex;
    flex-direction: column;
    gap: 15px;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 10px;
    margin-left: 10px;
    margin-right: 10px;
}
.custom-file-input ~ .custom-file-label[Subir]::after {
    content: "Subir" !important;
}
.custom-file-input:lang(en) ~ .custom-file-label::after {
    content: "Browse" !important;
}
@media (max-width: 990px) {
    .import-modal {
        display: grid;
        grid-template-columns: none;
        grid-template-rows: 1fr 0.2fr 1fr;
        .btn-primary {
            margin-top: 10px;
            margin-bottom: 10px;
            transform: rotate(90deg);
        }
    }
    .import-modal_button-todo {
        flex-direction: row;
        height: auto !important;
        .btn {
            margin-top: 10px;
            margin-bottom: 10px;
            transform: rotate(90deg);
        }
    }
}
.not-length {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.not-length .feather {
    height: 4rem !important;
}
</style>
