<template>
    <div class="activity activity-caducado" v-if="studentMode && tarea?.caducado || studentMode && foro?.caducado || studentMode && examen?.caducado">
        <div class="activity-recursos ml-1">
            <div class="activity-recursos__icon" :style="{ backgroundColor: `${color_activity}` }">
                <span>
                    <feather-icon size="25" :icon="icon"></feather-icon>
                </span>
            </div>
            <div class="activity-recursos__title">
                <p class="p-0 m-0 title">{{ typeActivity }}</p>
                <p v-if="!(tipo == 'URL' && !foro)" class="p-0 m-0 sub-title sub-title-caducado">
                    {{ subTitle }}
                </p>
                <div class="sub-title__contenedor" v-else>
                    <p :href="url" class="p-0 m-0 sub-title sub-title-caducado">{{
                        subTitle
                    }}</p>
                    <span>
                        <feather-icon size="10" icon="LinkIcon"></feather-icon>
                    </span>
                </div>
                <div v-if="id_tipo_recurso == 3" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="AlertCircleIcon" class="padding-feather"></feather-icon> Tarea caducada: </strong> {{ formattedEndDate }}
                    </p>
                </div>
                <div v-else-if="id_tipo_recurso == 4" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="AlertCircleIcon" class="padding-feather"></feather-icon> Foro caducado: </strong> {{ formattedEndDateForo }}
                    </p>
                </div>
                <div v-else-if="id_tipo_recurso == 6" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="AlertCircleIcon" class="padding-feather"></feather-icon> Examen caducado: </strong> {{ formattedEndDateExamen }}
                    </p>
                </div>
            </div>
            <div v-if="getCalificacionTareas(tarea?.recurso_tarea_estudiante) == 'S/C' || getCalificacionTareas(tarea?.recurso_tarea_estudiante) == 'N/E'" class="nota-calificacion nota-caducado">
                <p class="m-0 p-0">{{getCalificacionTareas(tarea?.recurso_tarea_estudiante)}}</p>
                <span class="tooltip">{{ getCalificacionTareas(tarea?.recurso_tarea_estudiante) == 'S/C' ? 'Sin calificar' : 'No enviado' }}</span>
            </div>
            <div v-else-if="getCalificacionForos(foro?.recurso_foro_respuesta) == 'S/C' || getCalificacionForos(foro?.recurso_foro_respuesta) == 'N/E'" class="nota-calificacion nota-caducado">
                <p class="m-0 p-0">{{getCalificacionForos(foro?.recurso_foro_respuesta)}}</p>
                <span class="tooltip">{{ getCalificacionTareas(tarea?.recurso_tarea_estudiante) == 'S/C' ? 'Sin calificar' : 'No enviado' }}</span>
            </div>
            <div v-else-if="getCalificacionExamenes(examen?.estudiante_examen) == 'S/C' || getCalificacionExamenes(examen?.estudiante_examen) == 'N/E'" class="nota-calificacion nota-caducado">
                <p class="m-0 p-0">{{getCalificacionExamenes(examen?.estudiante_examen)}}</p>
                <span class="tooltip">{{ getCalificacionTareas(tarea?.recurso_tarea_estudiante) == 'S/C' ? 'Sin calificar' : 'No enviado' }}</span>
            </div>
            <div :class="getCalificacionTareas(tarea?.recurso_tarea_estudiante) >= 10.5 ? 'nota-calificacion nota-calificacion-pointer' : 'nota-calificacion nota-calificacion-pointer-mala nota-mala'" v-else-if="tarea && getCalificacionTareas(tarea?.recurso_tarea_estudiante) && studentMode" @click="createModalIntentoEnviado">
                <div class="nota-calificacion__icon">
                    <feather-icon size="10" :color="getCalificacionTareas(tarea?.recurso_tarea_estudiante) >= 10.5 ? '#01b200' : '#b23200'" :icon="getCalificacionTareas(tarea?.recurso_tarea_estudiante) >= 10.5 ? 'CheckCircleIcon' : 'XCircleIcon'"></feather-icon>
                </div>
                <p class="m-0 p-0">{{ getCalificacionTareas(tarea?.recurso_tarea_estudiante) }}</p>
                <span class="tooltip">Ver intentos enviados</span>
            </div>
            <div :class="getCalificacionForos(foro?.recurso_foro_respuesta) >= 10.5 ? 'nota-calificacion' : 'nota-calificacion nota-mala'" v-else-if="foro && getCalificacionForos(foro?.recurso_foro_respuesta) && studentMode">
                <div class="nota-calificacion__icon">
                    <feather-icon size="10" :color="getCalificacionForos(foro?.recurso_foro_respuesta) >= 10.5 ? '#01b200' : '#b23200'" :icon="getCalificacionForos(foro?.recurso_foro_respuesta) >= 10.5 ? 'CheckCircleIcon' : 'XCircleIcon'"></feather-icon>
                </div>
                <p class="m-0 p-0">{{ getCalificacionForos(foro?.recurso_foro_respuesta) }}</p>
            </div>
            <div :class="getCalificacionExamenes(examen?.estudiante_examen) >= 10.5 ? 'nota-calificacion nota-calificacion-pointer' : 'nota-calificacion nota-calificacion-pointer-mala nota-mala'" v-else-if="examen && getCalificacionExamenes(examen?.estudiante_examen) && studentMode" @click="redirect(`${uuid_curso_matricula}/recursos-examenes/${examen.id}/calificar/${id_curso_matricula}`, false)">
                <div class="nota-calificacion__icon">
                    <feather-icon size="10" :color="getCalificacionExamenes(examen?.estudiante_examen) >= 10.5 ? '#01b200' : '#b23200'" :icon="getCalificacionExamenes(examen?.estudiante_examen) >= 10.5 ? 'CheckCircleIcon' : 'XCircleIcon'"></feather-icon>
                </div>
                <p class="m-0 p-0">{{ getCalificacionExamenes(examen?.estudiante_examen) }}</p>
                <span class="tooltip">Ver respuestas</span>
            </div>
        </div>
        <Modal ref="modalIntentoEnviado"> 
            <template #title>Intentos enviados</template>
            <template>
                <section class="info-intentos-enviados">
                    <Archive v-for="(tarea_enviada, index) in tarea?.recurso_tarea_estudiante" :key="tarea_enviada.id" :id="tarea_enviada.archivos.id" :archivo="tarea_enviada.archivos.archivo" :nombre="tarea_enviada.archivos.nombre" :primary="tarea.recurso_tarea_estudiante.length - 1 == index" :primary_text="`Calificado - ${getCalificacionTareas(tarea?.recurso_tarea_estudiante)}`" />
                </section>
            </template>
            <template #footer>
                <button
                    class="btn btn-outline-danger"
                    @click.prevent="closeModalIntentoEnviado"
                >
                    <feather-icon icon="XIcon" />
                    <span>Cerrar</span>
                </button>
            </template>
        </Modal>
    </div>
    

    <div class="activity activity-caducado" v-else-if="studentMode && tarea?.no_disponible || studentMode && foro?.no_disponible || studentMode && examen?.no_disponible">
        <div class="activity-recursos ml-1">
            <div class="activity-recursos__icon" :style="{ backgroundColor: `${color_activity}` }">
                <span>
                    <feather-icon size="25" :icon="icon"></feather-icon>
                </span>
            </div>
            <div class="activity-recursos__title">
                <p class="p-0 m-0 title">{{ typeActivity }}</p>
                <p v-if="!(tipo == 'URL' && !foro)" class="p-0 m-0 sub-title sub-title-caducado">
                    {{ subTitle }}
                </p>
                <div class="sub-title__contenedor" v-else>
                    <p class="p-0 m-0 sub-title sub-title-caducado">{{
                        subTitle
                    }}</p>
                    <span>
                        <feather-icon size="10" icon="LinkIcon"></feather-icon>
                    </span>
                </div>
                <div v-if="id_tipo_recurso == 3" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="ClockIcon" class="padding-feather"></feather-icon> Tarea programada: </strong> {{ formattedStartDate }}
                    </p>
                    <span>|</span>
                    <p v-if="studentMode" class="m-0 p-0">
                        <strong>Intentos disponibles:</strong> {{ intentosDisponibles }}
                    </p>
                    <p v-else class="m-0 p-0">
                        <strong>Numero de intentos:</strong> {{ tarea.numero_intento }}
                    </p>
                </div>
                <div v-else-if="id_tipo_recurso == 4" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="ClockIcon" class="padding-feather"></feather-icon> Foro programado: </strong> {{ formattedStartDateForo }}
                    </p>
                    <span>|</span>
                    <p class="m-0 p-0">
                       <strong>Semana:</strong> {{ foro.semana }}
                    </p>
                </div>
                <div v-if="id_tipo_recurso == 6" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="ClockIcon" class="padding-feather"></feather-icon> Examen programado: </strong> {{ formattedStartDateExamen }}
                    </p>
                    <span>|</span>
                    <p class="m-0 p-0">
                        <strong>Numero de intentos:</strong> {{ examen.numero_intento }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="activity" v-else>
        <div class="activity-recursos ml-1">
            <div class="activity-recursos__icon" @click="createModalDetalle" :id="id" :style="{ backgroundColor: `${color_activity}` }">
                <span>
                    <feather-icon size="25" :icon="icon"></feather-icon>
                </span>
            </div>
            <div class="activity-recursos__title">
                <p class="p-0 m-0 title">{{ typeActivity }}</p>
                <template v-if="id_tipo_recurso == 2">
                    <p v-if="!(tipo == 'URL' && !foro)" class="p-0 m-0 sub-title" @click="redirect(`${uuid_curso_matricula}/foros/${foro.id}`)">
                        {{ subTitle }}
                    </p>
                    <div class="sub-title__contenedor" v-else>
                        <a :href="url" class="p-0 m-0 sub-title" target="_blank">{{
                            subTitle
                        }}</a>
                        <span>
                            <feather-icon size="10" icon="LinkIcon"></feather-icon>
                        </span>
                    </div>
                </template>
                <template v-else>
                    <div class="sub-title__contenedor" v-if="studentMode">
                        <a class="p-0 m-0 sub-title">{{
                            subTitle
                        }}</a>
                        <span>|</span>
                        <p class="m-0 p-0" v-if="mostrar">
                            <BBadge variant="light-success">
                                Disponible
                            </BBadge>
                        </p>
                        <p class="m-0 p-0" v-else>
                            <BBadge variant="light-danger">
                                Deshabilitado
                            </BBadge>
                        </p>
                    </div>
                    <div class="sub-title__contenedor" v-if="!studentMode">
                        <a class="p-0 m-0 sub-title">{{
                            subTitle
                        }}</a>
                        <template v-if="id_tipo_recurso == 6">
                            <span>|</span>
                            <p class="m-0 p-0" v-if="mostrar">
                                <BBadge variant="light-success">
                                    Habilitado
                                </BBadge>
                            </p>
                            <p class="m-0 p-0" v-else>
                                <BBadge variant="light-danger">
                                    Deshabilitado
                                </BBadge>
                            </p>
                        </template>
                    </div>
                </template>
                <div v-if="id_tipo_recurso == 3" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="PlayIcon" class="padding-feather"></feather-icon> Fecha de caducidad:</strong> {{ formattedEndDate }}
                    </p>
                    <span>|</span>
                    <p v-if="studentMode" class="m-0 p-0">
                        <strong>Intentos disponibles:</strong> {{ intentosDisponibles }}
                    </p>
                    <p v-else class="m-0 p-0">
                        <strong>Numero de intentos:</strong> {{ tarea.numero_intento }}
                    </p>
                </div>
                <div v-else-if="id_tipo_recurso == 4" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="PlayIcon" class="padding-feather"></feather-icon> Fecha de caducidad:</strong> {{ formattedEndDateForo }}
                    </p>
                    <span>|</span>
                    <p class="m-0 p-0">
                        <strong>Semana:</strong> {{ foro.semana }}
                    </p>
                </div>
                <div v-else-if="id_tipo_recurso == 6" class="fecha-caducidad">
                    <p class="m-0 p-0">
                        <strong><feather-icon icon="PlayIcon" class="padding-feather"></feather-icon> Fecha de caducidad:</strong> {{ formattedEndDateExamen }}
                    </p>
                    <span>|</span>
                    <p class="m-0 p-0">
                        <strong>Numero de intentos:</strong> {{ examen.numero_intento }}
                    </p>
                </div>
            </div>
        </div>
        <div v-if="!studentMode" class="activity-options mr-1">
            <div class="acordeon-header__actions">
                <DropdownMenu
                    @toggle="toggleDropdown(id)"
                    :active-dropdown="activeDropdown == id"
                >
                    <p @click="editarActividad()">Editar actividad</p>
                    <p @click="borrarActividad(id)">Borrar actividad</p>
                </DropdownMenu>
            </div>
        </div>
        <div :class="getCalificacionTareas(tarea.recurso_tarea_estudiante) >= 10.5 ? 'nota-calificacion' : 'nota-calificacion nota-mala'" v-if="tarea && getCalificacionTareas(tarea.recurso_tarea_estudiante) && studentMode">
            <div class="nota-calificacion__icon">
                    <feather-icon size="10" :color="getCalificacionTareas(tarea.recurso_tarea_estudiante) >= 10.5 ? '#01b200' : '#b23200'" :icon="getCalificacionTareas(tarea.recurso_tarea_estudiante) >= 10.5 ? 'CheckCircleIcon' : 'XCircleIcon'"></feather-icon>
            </div>
            <p class="m-0 p-0">{{ getCalificacionTareas(tarea.recurso_tarea_estudiante) }}</p>
        </div>
        <div :class="getCalificacionForos(foro.recurso_foro_respuesta) >= 10.5 ? 'nota-calificacion' : 'nota-calificacion nota-mala'" v-if="foro && getCalificacionForos(foro.recurso_foro_respuesta) && studentMode">
            <div class="nota-calificacion__icon">
                    <feather-icon size="10" :color="getCalificacionForos(foro.recurso_foro_respuesta) >= 10.5 ? '#01b200' : '#b23200'" :icon="getCalificacionForos(foro.recurso_foro_respuesta) >= 10.5 ? 'CheckCircleIcon' : 'XCircleIcon'"></feather-icon>
            </div>
            <p class="m-0 p-0">{{ getCalificacionForos(foro.recurso_foro_respuesta) }}</p>
        </div>
        <Modal ref="modalDetalle" :loading="formTarea.processing ">
            <template #title>Detalle de actividad</template>
            <template v-if="id_tipo_recurso === 2">
                <div class="info-container">
                    <section class="info">
                        <p class="info__label p-0 m-0">Nombre:</p>
                        <p class="info__data p-0 m-0">
                            {{ capitalizeWord(subTitle) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Descripcion:</p>
                        <p class="info__data p-0 m-0">
                            {{
                                descripcion
                                    ? capitalizeWord(descripcion)
                                    : "Sin descripción"
                            }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Enlace:</p>
                        <p class="info__data-url p-0 m-0" @click="goUrl(url)">
                            {{ url }}
                        </p>
                    </section>
                </div>
            </template>
            <template v-else-if="id_tipo_recurso === 3">
                <div class="info-container">
                    <section class="info">
                        <p class="info__label p-0 m-0">Nombre:</p>
                        <p class="info__data p-0 m-0">
                            {{ capitalizeWord(subTitle) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Descripcion:</p>
                        <p class="info__data p-0 m-0">
                            {{
                                descripcion
                                    ? capitalizeWord(descripcion)
                                    : "Sin descripción"
                            }}
                        </p>
                    </section>
                    <section v-if="tipo == 'URL'" class="info">
                        <p class="info__label p-0 m-0">Enlace:</p>
                        <p class="info__data-url p-0 m-0" @click="goUrl(url)">
                            {{ url }}
                        </p>
                    </section>
                    <section v-if="tipo=='ARCHIVO'" class="info">
                        <p class="info__label p-0 m-0">Archivo:</p>
                        <Archive 
                        :key="tarea.id" 
                        :id="tarea.archivos[0].id" 
                        :archivo="tarea.archivos[0].archivo" 
                        :nombre="tarea.archivos[0].nombre" />
                    </section>
                    <section v-if="studentMode" class="info">
                        <p class="info__label p-0 m-0">Intentos disponibles:</p>
                        <p  class="info__data p-0 m-0">
                            {{ intentosDisponibles }}
                        </p>
                    </section>
                    <section v-else class="info">
                        <p class="info__label p-0 m-0">Numero de intentos:</p>
                        <p  class="info__data p-0 m-0">
                            {{ tarea.numero_intento }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha inicio:</p>
                        <p class="info__data p-0 m-0">
                            {{ formattedStartDate }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha fin:</p>
                        <p class="info__data p-0 m-0"">
                            {{ formattedEndDate }}
                        </p>
                    </section>

                    <section v-if="studentMode && !getCalificacionTareas(tarea.recurso_tarea_estudiante)" class="info">
                        <p class="info__label p-0 m-0">Subir tarea:</p>
                        <div v-if="intentosDisponibles != 0" class="file-input">
                            <b-form-file
                            v-model="formTarea.files_tarea"
                            :state="Boolean(formTarea.files_tarea.length)"
                            placeholder="Subir archivos..."
                            drop-placeholder="Drop files here..."
                            :accept="formattedAcceptValues"
                            :browse-text="'Subir'"
                            multiple
                            @input="validateFileLimit"
                            ></b-form-file>
                            <p class="maxima-capacidad m-0 p-0">Máxima capacidad: 8 MB | Tipos de archivo: {{ tarea.tipo_archivos ? tarea.tipo_archivos : "Todos" }}</p>
                            <div v-if="!(formTarea.files_tarea.length == 0)" class="file-input__file-names">
                            Archivos seleccionados:
                            <ul>
                                <li v-for="(file, index) in formTarea.files_tarea" :key="index">{{ file.name }}</li>
                            </ul>
                            </div>
                        </div>

                        <p v-else class="info__data p-0 m-0"">
                            Ya no quedan intentos disponibles
                        </p>
                    </section>
                    <section v-if="!studentMode" class="info">
                        <button class="btn btn-success" @click.prevent="redirect(`${uuid_curso_matricula}/recursos-tareas/${tarea.id}`)">
                            Calificar tareas
                        </button>
                    </section>
                    <section v-if="studentMode && tarea?.recurso_tarea_estudiante.length > 0 && !getCalificacionTareas(tarea.recurso_tarea_estudiante)" class="info">
                        <p class="info__label p-0 m-0">Intentos previos:</p>
                        <Archive v-for="(tarea_enviada, index) in tarea.recurso_tarea_estudiante" :key="tarea_enviada.id" :id="tarea_enviada.archivos.id" :archivo="tarea_enviada.archivos.archivo" :nombre="tarea_enviada.archivos.nombre" :primary="tarea.recurso_tarea_estudiante.length - 1 == index"/>
                    </section>
                    <section v-if="studentMode && tarea?.recurso_tarea_estudiante.length > 0 && getCalificacionTareas(tarea.recurso_tarea_estudiante)" class="info">
                        <p class="info__label p-0 m-0">Intento calificado:</p>
                        <Archive :id="tarea.recurso_tarea_estudiante[tarea.recurso_tarea_estudiante.length - 1].archivos.id" :archivo="tarea.recurso_tarea_estudiante[tarea.recurso_tarea_estudiante.length - 1].archivos.archivo" :nombre="tarea.recurso_tarea_estudiante[tarea.recurso_tarea_estudiante.length - 1].archivos.nombre" />
                    </section>
                    <section class="info" v-if="studentMode && getCalificacionTareas(tarea.recurso_tarea_estudiante)">
                        <p class="info__label p-0 m-0">Calificación:</p>
                        <div>
                            <BBadge v-if="getCalificacionTareas(tarea.recurso_tarea_estudiante) >= 10.5" variant="light-success">
                                {{ getCalificacionTareas(tarea.recurso_tarea_estudiante) }}
                            </BBadge>
                            <BBadge v-else variant="light-danger">
                                {{ getCalificacionTareas(tarea.recurso_tarea_estudiante) }}
                            </BBadge>
                        </div>
                    </section>
                </div>
            </template>
            <template v-else-if="id_tipo_recurso === 4">
                <div class="info-container">
                    <section class="info">
                        <p class="info__label p-0 m-0">Nombre:</p>
                        <p class="info__data p-0 m-0">
                            {{ capitalizeWord(subTitle) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Descripcion:</p>
                        <p class="info__data p-0 m-0">
                            {{
                                descripcion
                                    ? capitalizeWord(descripcion)
                                    : "Sin descripción"
                            }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Semana:</p>
                        <p class="info__data p-0 m-0">
                            {{ foro.semana }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Modo de calificación:</p>
                        <p class="info__data p-0 m-0">
                            {{ capitalizeWord(foro.modo_calificacion) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Puntaje:</p>
                        <p class="info__data p-0 m-0">
                            {{ foro.puntaje }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Maximo de comentarios:</p>
                        <p class="info__data p-0 m-0">
                            {{ foro.maximo_comentarios }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha inicio:</p>
                        <p class="info__data p-0 m-0">
                            {{ formattedStartDateForo }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha fin:</p>
                        <p class="info__data p-0 m-0"">
                            {{ formattedEndDateForo }}
                        </p>
                    </section>
                    <section v-if="!studentMode" class="info">
                        <button class="btn btn-primary" @click.prevent="redirect(`${uuid_curso_matricula}/foros/${foro.id}`)">
                            Ver respuestas
                        </button>
                    </section>
                    <section v-if="studentMode && !getCalificacionForos(foro.recurso_foro_respuesta)" class="info">
                        <button class="btn btn-success" @click.prevent="redirect(`${uuid_curso_matricula}/foros/${foro.id}`)">
                            Responder
                        </button>
                    </section>
                    <section v-if="!studentMode" class="info">
                        <button class="btn btn-success" @click.prevent="redirect(`${uuid_curso_matricula}/foros/${foro.id}/calificar`)">
                            Calificar respuestas
                        </button>
                    </section>
                    <section class="info" v-if="studentMode && getCalificacionForos(foro.recurso_foro_respuesta)">
                        <p class="info__label p-0 m-0">Calificación:</p>
                        <div>
                            <BBadge v-if="getCalificacionForos(foro.recurso_foro_respuesta) >= 10.5" variant="light-success">
                                {{ getCalificacionForos(foro.recurso_foro_respuesta) }}
                            </BBadge>
                            <BBadge v-else variant="light-danger">
                                {{ getCalificacionForos(foro.recurso_foro_respuesta) }}
                            </BBadge>
                        </div>
                    </section>

                </div>
            </template>
            <template v-else-if="id_tipo_recurso === 6">
                <div class="info-container">
                    <section class="info">
                        <p class="info__label p-0 m-0">Nombre:</p>
                        <p class="info__data p-0 m-0">
                            {{ capitalizeWord(subTitle) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Descripcion:</p>
                        <p class="info__data p-0 m-0">
                            {{
                                descripcion
                                    ? capitalizeWord(descripcion)
                                    : "Sin descripción"
                            }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Duración:</p>
                        <p class="info__data p-0 m-0">
                            {{ conversorDuracion(examen.duracion) }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Semana:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.semana }}
                        </p>
                    </section>
                    <section class="info" v-if="!studentMode">
                        <p class="info__label p-0 m-0">Numero de intentos:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.numero_intento }}
                        </p>
                    </section>
                    <section class="info" v-if="studentMode && examen.estudiante_examen.length != 0">
                        <p class="info__label p-0 m-0">Numero de intentos:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.numero_intento }}
                        </p>
                    </section>
                    <!-- <section class="info">
                        <p class="info__label p-0 m-0">Barajar:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.barajar == 'S' ? 'Si' : 'No' }}
                        </p>
                    </section> -->
                    <!-- <section class="info">
                        <p class="info__label p-0 m-0">Recuperación:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.recuperacion == 'S' ? 'Si' : 'No' }}
                        </p>
                    </section> -->
                    <!-- <section class="info">
                        <p class="info__label p-0 m-0">Tiempo por pregunta:</p>
                        <p class="info__data p-0 m-0">
                            {{ examen.tiempo_pregunta == 'S' ? 'Si' : 'No' }}
                        </p>
                    </section> -->
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha inicio:</p>
                        <p class="info__data p-0 m-0">
                            {{ formattedStartDateExamen }}
                        </p>
                    </section>
                    <section class="info">
                        <p class="info__label p-0 m-0">Fecha fin:</p>
                        <p class="info__data p-0 m-0"">
                            {{ formattedEndDateExamen }}
                        </p>
                    </section>
                    <section v-if="!studentMode" class="info">
                        <button class="btn btn-success" @click.prevent="redirect(`${uuid_curso_matricula}/recursos-examenes/${examen.id}/calificar`)">
                            Calificar examenes
                        </button>
                    </section>
                    <section v-if="!studentMode" class="info">
                        <button class="btn btn-primary" @click.prevent="redirect(`${uuid_curso_matricula}/recursos-examenes/${examen.id}`)">
                            Configurar examen
                        </button>
                    </section>
                    <section v-if="studentMode && examen.estudiante_examen.length == 0" class="info">
                        <button class="btn btn-success" @click.prevent="redirect(`${uuid_curso_matricula}/resolver-examenes/${examen.id}`)">
                            Desarrollar examen
                        </button>
                    </section>
                    <section v-if="studentMode && examen.estudiante_examen.length != 0" class="info">
                        <p class="info__label p-0 m-0">Estado:</p>
                        <p class="info__data p-0 m-0">
                            Respuestas enviadas
                        </p>
                    </section>
                </div>
            </template>
            <template #footer>
                <button
                    class="btn btn-outline-danger"
                    @click.prevent="closeModalDetalle"
                >
                    <feather-icon icon="XIcon" />
                    <span>Cerrar</span>
                </button>
                <button
                v-if="id_tipo_recurso === 3 && intentosDisponibles != 0 && studentMode && !getCalificacionTareas(tarea?.recurso_tarea_estudiante)"
                    class="btn btn-outline-success"
                    @click.prevent="storeTarea"
                    :disabled="formTarea.processing"
                >
                    <feather-icon icon="CheckIcon" />
                    <span>Enviar tarrea</span>
                </button>
            </template>
        </Modal>
    </div>
</template>

<script>
import { useForm } from "@inertiajs/vue2";
import { alertError, alertSuccess, alertWarning, confirm } from "../../../sweetAlert2";
import { format } from "date-fns";
import { capitalizeFirstWord } from "../../../utils/textProcess";
import { objectToFormData } from "../../../formData";
import { BBadge } from "bootstrap-vue";
import DropdownMenu from "./DropdownMenu.vue";
import Modal from "../../../Components/Modal.vue";
import Archive from "./Archive.vue";

export default {
    components: {
        DropdownMenu,
        Modal,
        Archive,
        BBadge
    },
    props: {
        secciones_recursos:Array,
        typeActivity: {
            type: String,
            required: true,
        },
        subTitle: {
            type: String,
            required: true,
        },
        descripcion: {
            type: String,
            required: false,
        },
        id_tipo_recurso: {
            type: Number,
            required: true,
        },
        tarea: {
            type: Object,
            required: false,
        },
        foro: {
            type: Object,
            required: false
        },
        examen: {
            type: Object,
            required: false
        },
        tipo: {
            type: String,
            required: false,
        },
        icon: {
            type: String,
        },
        url: {
            type: String,
            required: false,
        },
        id: {
            type: Number,
            required: true,
        },
        studentMode: {
            type: Boolean,
            default: false
        },
        id_curso_matricula: {
            type: Number,
            required: false
        },
        uuid_curso_matricula: {
            type: String,
            required: true
        },
        mostrar: {
            type: Boolean,
            required: false
        },
        color_activity: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            formTarea: useForm({
                files_tarea: [],
                id_curso_matricula: this.id_curso_matricula,
                id_recurso_tarea: 0,
            }),
            activeDropdown: null,
        };
    },
    mounted(){
        console.log(this.tarea)
        this.$nextTick(() => {
        this.ids();
    });
    },
    methods: {
        ids(){
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            if(id){
                   document.getElementById(id).click();
            }
        },
        goUrl(url) {
            window.open(url, "_blank"); // Abre en una nueva pestaña
        },
        redirect(go = "", close_modal = true) {
            if (close_modal) this.closeModalDetalle()
            
            this.$inertia.visit(this.routeTo(`gestion-cursos/virtual-classroom/${go}`));
        },
        toggleDropdown(id) {
            if (this.activeDropdown == id) {
                console.log("Cerrando dropdown", id);
                this.activeDropdown = null;
            } else {
                console.log("Abriendo dropdown", id);
                this.activeDropdown = id;
            }
        },
        editarActividad(recurso) {
            this.$emit("edit", recurso);
        },
        borrarActividad(id) {
            confirm(
                {
                    text: "¿Desea eliminar la actividad?",
                },
                () => {
                    this.$http({
                        method: "DELETE",
                        url: this.routeTo(
                            `gestion-cursos/virtual-classroom/recursos/destroy/${id}`
                        ),
                    }).then(() => {
                        alertSuccess("Actividad eliminada correctamente");
                        this.$inertia.reload({ only: ["secciones"] });
                    });
                }
            );

            console.log(`Borrando ${id}`);
        },
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        validateFileLimit() {
            if (this.formTarea.files_tarea.length > 3) {
                this.formTarea.files_tarea = this.formTarea.files_tarea.slice(0, 3); // Limitar a los primeros 3 archivos

                alertWarning("Solo se pueden enviar 1 archivo como maximo");
            }
        },
        storeTarea() {
            if (this.formTarea.files_tarea.length == 0) {
                alertError("Usted no está enviando ningún archivo");
                return;
            }

            this.formTarea.id_recurso_tarea = this.tarea.id;

            const data = this.formTarea.data();
            console.log('data', data);
            const transformData = objectToFormData(data);

            this.formTarea.processing = true;

            this.$http({
                method: "POST",
                url: this.routeTo(`gestion-cursos/virtual-classroom/recursos-tareas-estudiante/store`),
                data: transformData,
            })
                .then(() => {
                    alertSuccess("Datos Guardados Correctamente");

                    // Resetear los archivos después de un POST exitoso
                    this.formTarea.files_tarea = [];

                    this.$inertia.reload({ only: ["secciones"] });
                    this.closeModalDetalle();
                })
                .catch((err) => {
                    console.error("Error", err);

                    if (err.response.status == 422) {
                        const errors = err.response.data.errors;
                        const errorMessages = [];

                        // Recorrer los errores para extraer solo los mensajes relevantes
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                errors[key].forEach((message) => {
                                    // Filtramos los mensajes que contienen "El archivo"
                                    if (message.includes("archivo")) {
                                        errorMessages.push(message);
                                    }
                                });
                            }
                        }

                        // Unir los mensajes en un string con saltos de línea
                        const errorString = errorMessages.map((msg) => `- ${msg}`).join("<br>");

                        // Mostrar el resultado
                        console.log(errorString);
                        alertError(errorString, 5000, true);
                    }

                    if (err.response.status == 409) {
                        alertError(err.response.data.message);
                    }
                })
                .finally(() => {
                    this.formTarea.processing = false;
                });
        },
        formatAccepts(values) {
            // Dividir el string en un array de extensiones
            const extensionArray = values.split(',');

            // Preprocesar para agregar el punto (.) antes de cada extensión
            const formattedExtensions = extensionArray.map(ext => `.${ext}`);

            // Unir las extensiones procesadas con comas
            const acceptValue = formattedExtensions.join(',');

            // Mostrar el valor que debe ir en el atributo accept
            return acceptValue
        },
        getCalificacionTareas(tareas) {
            if (tareas) {
                const tarea_para_nota = tareas[tareas.length - 1];
                if ( tareas.length > 0) {

                    if (tarea_para_nota.estado_calificacion == "PENDIENTE" && this.tarea.caducado) {
                        return 'S/C';
                    }

                    if (tarea_para_nota.estado_calificacion == "PENDIENTE") {
                        return null;
                    }

                    return tarea_para_nota.puntaje;
                }

                if (tareas.length == 0 && this.tarea.caducado) {
                    return 'N/E';
                }
            }
        },
        getCalificacionForos(respuestas) {
            if (respuestas) {
                const foro_nota = respuestas[respuestas.length - 1];
                if ( respuestas.length > 0) {

                    if (foro_nota.estado_calificacion == "PENDIENTE" && this.foro.caducado) {
                        return 'S/C';
                    }

                    if (foro_nota.estado_calificacion == "PENDIENTE") {
                        return null;
                    }

                    return foro_nota.puntaje;
                }

                if (respuestas.length == 0 && this.foro.caducado) {
                    return 'N/E';
                }
            }
        },
        getCalificacionExamenes(solucion) {
            if (solucion) {
                const examen_nota = solucion[solucion.length - 1];
                if ( solucion.length > 0) {

                    if (examen_nota.estado_examen == "PENDIENTE" && this.examen.caducado) {
                        return 'S/C';
                    }

                    if (examen_nota.estado_examen == "PENDIENTE") {
                        return null;
                    }

                    return examen_nota.puntaje;
                }

                if (solucion.length == 0 && this.examen.caducado) {
                    return 'N/E';
                }
            }
        },
        conversorDuracion(duracion) {
            const _duracion = parseFloat(duracion);
            
            const horas = Math.floor(_duracion / 60); // Horas completas
            const minutos = _duracion % 60; // Minutos restantes

            const new_duracion = `${horas} hora(s) ${minutos} minuto(s)`

            return new_duracion;
        },

        // MODALS

        createModalDetalle() {
            this.formTarea.reset();
            this.$refs.modalDetalle.show();
        },
        closeModalDetalle() {
            this.$refs.modalDetalle.hide();
        },
        
        createModalIntentoEnviado() {
            if (this.id_tipo_recurso != 3) return; 

            // this.$refs.modalDetalle.show();

            this.$refs.modalIntentoEnviado.show();
        },
        closeModalIntentoEnviado() {
            this.$refs.modalIntentoEnviado.hide()
        }

    },
    computed: {
        formattedStartDate() {
            if (this.id_tipo_recurso != 3) return;

            const date = new Date(this.tarea.fecha_inicio);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedEndDate() {
            if (this.id_tipo_recurso != 3) return;

            const date = new Date(this.tarea.fecha_fin);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedStartDateForo() {
            if (this.id_tipo_recurso != 4) return;

            const date = new Date(this.foro.fecha_inicio);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedEndDateForo() {
            if (this.id_tipo_recurso != 4) return;

            const date = new Date(this.foro.fecha_fin);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedStartDateExamen() {
            if (this.id_tipo_recurso != 6) return;

            const date = new Date(this.examen.fecha_inicio);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedEndDateExamen() {
            if (this.id_tipo_recurso != 6) return;

            console.log(this.examen)

            const date = new Date(this.examen.fecha_fin);
            return format(date, "dd/MM/yyyy HH:mm");
        },
        formattedAcceptValues () {
            if (this.tarea.tipo_archivos) {
                return this.formatAccepts(this.tarea.tipo_archivos)
            } else {
                return ""
            }
        },
        intentosDisponibles () {
            if (this.tarea && this.studentMode) {
                return this.tarea.numero_intento - this.tarea.recurso_tarea_estudiante.length
            }
        },
    },
};
</script>

<style scoped>
.activity {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    gap: 20px;
    position: relative;

    border: 1px solid #d6d6d6;
    padding: 15px 10px;
}

.activity:hover {
    background-color: #f5f9fc;
    cursor: pointer;
    border: 1px solid #0f6cbf;

    .nota-calificacion {
        border: 1px solid #0f6cbf;
        border-top: none;
        border-right: none;
    }
}

.activity-caducado:hover {
    background-color: #f6f6f6;
    cursor: not-allowed;
    border: 1px solid #bebebe;

    .nota-calificacion {
        border: 1px solid #bebebe;
        border-top: none;
        border-right: none;
    }
}

.activity-recursos {
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-recursos__icon {
    display: flex;
    justify-content: center;
    align-items: center;
    /* background-color: #eb66a2; */
    padding: 15px;
    border-radius: 5px;

    span {
        color: white;
    }
}

.activity-recursos__title {
    .title {
        color: black;
        font-size: 12px;
    }

    .sub-title {
        color: #0f6cbf;
    }

    .sub-title:hover {
        cursor: pointer;
        text-decoration: underline;
    }

    .sub-title-caducado:hover {
        cursor: not-allowed;
        text-decoration: none;
    }
}

.activity-caducado {
    .activity-recursos__title {

        .sub-title {
            color: #848484;
        }
    }

    .fecha-caducidad {

        span {
            padding-bottom: 2px;
            color: #848484;
        }

    }

    .nota-calificacion {
        border: 1px solid #bebebe;
        border-top: none;
        border-right: none;
    }

}

.activity-options {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;

    button {
        background: none;
        border: 1px solid #797979;
        border-radius: 4px;
        padding: 4px 8px;

        p {
            font-size: 12px;
            color: #797979;
        }
    }

    button:hover {
        background: #797979;

        p {
            color: white;
        }
    }
}

.sub-title__contenedor {
    display: flex;
    align-items: center;
    gap: 5px;

    span {
        padding-bottom: 2px;
    }
}

.fecha-caducidad {
    display: flex;
    justify-content: start;
    gap: 5px;

    span {
        padding-bottom: 2px;
        color: #0f6cbf;
    }

    p {
        font-size: 12px;
    }
}

.info-container {
    display: flex;
    flex-direction: column;
    gap: 15px;

    .info {
        display: flex;
        flex-direction: column;
        gap: 5px;

        .info__label {
            font-weight: 700;
        }

        .info__data-url {
            color: #0f6cbf;
        }

        .info__data-url:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    }
}

.file-input {
    padding-top: 6px;

    .file-input__file-names {
        padding-top: 6px;
        font-size: 12px;
    }
}

.maxima-capacidad {
font-size: 10px;

}

.nota-calificacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    border: 1px solid #d6d6d6;
    border-top: none;
    border-right: none;
    padding: 7px;
    border-bottom-left-radius: 4px;
    border-top-right-radius: 8px;
    background-color: #eaffea;
    min-width: 56px;

    position: absolute;
    top: 0;
    right: 0;

    p {
        font-size: 11px;
        color: #01b200;
        font-weight: bold;
    }
}

.nota-mala {
    background-color: #ffeaea;

    p {
        font-size: 11px;
        color: #b23200;
        font-weight: bold;
    }
}

.nota-caducado {
    background-color: #e4e4e4;

    p {
        font-size: 11px;
        color: #828282;
        font-weight: bold;
    }
}

.nota-calificacion-pointer, .nota-calificacion-pointer-mala {
    cursor: pointer;
}

.nota-calificacion-pointer:hover {
    background-color: #c4ffc4;
}

.nota-calificacion-pointer-mala:hover {
    background-color: #ffcbcb;
}

.tooltip {
    visibility: hidden;
    background-color: black;
    color: white;
    text-align: center;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 12px;
    position: absolute;
    top: 50%; /* Alinea verticalmente */
    right: 100%; /* Coloca el tooltip a la izquierda */
    transform: translateY(-50%) translateX(-10px); /* Ajusta la posición */
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 10; /* Asegura que el tooltip esté por encima de otros elementos */
}

/* Flecha del tooltip */
.tooltip::after {
    content: "";
    position: absolute;
    top: 50%; /* Alinea verticalmente */
    left: 100%; /* Coloca la flecha a la derecha del tooltip */
    transform: translateY(-50%); /* Centra verticalmente */
    border-width: 5px;
    border-style: solid;
    border-color: transparent transparent transparent black; /* Flecha hacia la derecha */
}

/* Mostrar tooltip al pasar el mouse */
.nota-calificacion:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

.padding-feather {
    padding-bottom: 2px;
}

.info-intentos-enviados {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
</style>
