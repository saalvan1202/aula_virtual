<template>
    <LayoutContent>
        <ReturnButton
            title="Volver"
            :path_remove="`recursos-examenes/${recurso_examen.id}`"
        />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <div
                        class="header-exam d-flex justify-content-center align-items-center"
                    >
                        <div>
                            <h3 class="p-0 m-0">
                                <strong>Configurar examen</strong>
                            </h3>
                        </div>
                        <div class="signal">
                            <OnlineSignal :active="!mostrar_validation" />
                        </div>
                    </div>
                </template>
                <div class="header-options">
                    <div>
                        <h5>
                            <strong>Examen: </strong>
                            {{
                                capitalizeWord(
                                    recurso_examen.seccion_recurso.nombre
                                )
                            }}
                        </h5>
                        <div>
                            <p class="m-0 p-0">
                                {{
                                    capitalizeWord(
                                        recurso_examen.seccion_recurso
                                            .descripcion
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="btn-control">
                        <button
                            v-if="mostrar_validation"
                            class="btn btn-dark btn-sm"
                        >
                            <p class="p-0 m-0" @click="createPuntajes">
                                Asignar puntajes
                            </p>
                            <div v-if="alert_validation" class="alert-icon">
                                <span>
                                    <feather-icon
                                        stroke-width="3px"
                                        size="12"
                                        icon="AlertTriangleIcon"
                                    ></feather-icon>
                                </span>
                            </div>
                        </button>
                        <button
                            v-if="!mostrar_validation"
                            class="btn btn-danger btn-sm"
                            @click.prevent="deshabilitarExamen"
                        >
                            <p class="p-0 m-0">
                                Deshabilitar
                            </p>
                        </button>
                        <button
                            v-else-if="mostrar_validation"
                            class="btn btn-success btn-sm"
                            :disabled="verify_alert || estado_incompleto"
                            @click.prevent="habilitarExamen"
                        >
                            <p class="p-0 m-0" >
                                Habilitar
                            </p>
                        </button>
                    </div>
                </div>
                <div class="pt-1">
                    <p><strong>Preguntas:</strong></p>
                    <div
                        v-for="(pregunta, index) in recurso_examen.preguntas"
                        :key="pregunta.id"
                    >
                        <Option
                            v-if="pregunta.tipo_pregunta.codigo == 'SM'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #options-btn v-if="mostrar_validation">
                                <OptionButtons :id_question="pregunta.id" :id_tipo_pregunta="pregunta.id_tipo_pregunta" :incompleto="pregunta?.incompleto" @create="createEditSimpleAlternativas(pregunta, index + 1)" @delete="deleteQuestion" @edit="(value) => createEditPregunta(value, pregunta, index + 1)" />
                            </template>
                            <template #default>
                                <SimpleSelection
                                    :opciones="pregunta.alternativas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo == 'OM'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #options-btn v-if="mostrar_validation">
                                <OptionButtons :id_question="pregunta.id" :id_tipo_pregunta="pregunta.id_tipo_pregunta" :incompleto="pregunta?.incompleto" @create="createEditAlternativas(pregunta, index + 1)" @delete="deleteQuestion" @edit="(value) => createEditPregunta(value, pregunta, index + 1)" />
                            </template>
                            <template #default>
                                <MultipleOption
                                    :opciones="pregunta.alternativas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'VF'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #options-btn v-if="mostrar_validation">
                                <OptionButtons :id_question="pregunta.id" :id_tipo_pregunta="pregunta.id_tipo_pregunta" :incompleto="pregunta?.incompleto" @create="createEditTrueFalseAlternativas(pregunta, index + 1)" @delete="deleteQuestion" @edit="(value) => createEditPregunta(value, pregunta, index + 1)" />
                            </template>
                            <template #default>
                                <TrueFalseSelection
                                    :opciones="pregunta.alternativas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'T'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #options-btn v-if="mostrar_validation">
                                <OptionButtons :id_question="pregunta.id" :id_tipo_pregunta="pregunta.id_tipo_pregunta" :incompleto="pregunta?.incompleto" @delete="deleteQuestion" @edit="(value) => createEditPregunta(value, pregunta, index + 1)" />
                            </template>
                            <template #default>
                                <TextAnswer :disabled="true" />
                            </template>
                        </Option>
                    </div>
                </div>
                <AddActivity
                    v-if="mostrar_validation"
                    :title="'Añadir pregunta'"
                    @add="createPregunta()"
                />

                <Modal ref="modalPregunta" :loading="formPregunta.processing">
                    <template #title>Crear pregunta</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <SelectSchedule
                                    label="Tipo de pregunta"
                                    :modelValue="formPregunta.id_tipo_pregunta"
                                    :options="tipo_pregunta"
                                    :nombre_label="true"
                                    :autoValidate="true"
                                    :autoHasError="errors?.id_tipo_pregunta"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_tipo_pregunta',
                                                formPregunta
                                            )
                                    "
                                    :onChange="handleTipoPregunta"
                                />
                            </div>
                            <div class="col-md-12 col-lg-12">
                                <InputSchedule
                                    v-if="formPregunta.id_tipo_pregunta != 0"
                                    label="Descripción"
                                    :modelValue="formPregunta.descripcion"
                                    :autoValidate="true"
                                    :autoHasError="errors?.descripcion"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'descripcion',
                                                formPregunta
                                            )
                                    "
                                />
                            </div>
                            <div
                                v-if="formPregunta.id_tipo_pregunta == 3"
                                class="col-md-12 col-lg-12"
                            >
                                <SelectSchedule
                                    label="Respuesta correcta"
                                    :modelValue="formPregunta.contestacion"
                                    :options="[
                                        { id: 1, nombre: 'Verdadero' },
                                        { id: 2, nombre: 'Falso' },
                                    ]"
                                    :nombre_label="true"
                                    :autoValidate="true"
                                    :autoHasError="errors?.contestacion"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'contestacion',
                                                formPregunta
                                            )
                                    "
                                />
                            </div>
                            <AddSimpleOption
                                v-if="formPregunta.id_tipo_pregunta == 1"
                                :value="
                                    formPregunta.simple_selection_alternativas
                                "
                            />
                            <AddMultiOption
                                v-if="formPregunta.id_tipo_pregunta == 2"
                                :value="
                                    formPregunta.multi_selection_alternativas
                                "
                            />
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closePregunta"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cancelar</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storePregunta"
                            :disabled="formPregunta.processing || formPregunta.descripcion == ''"
                        >
                            <feather-icon icon="PlusIcon" />
                            <span>Añadir</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalPuntajes" :loading="formPuntajes.processing">
                    <template #title>Asignar puntajes</template>
                    <form class="form form-vertical">
                        <div class="calcular-puntajes-container pb-1">
                            <div
                                class="calcular-btn"
                                @click.prevent="autoCalculatePuntajes"
                            >
                                <button class="btn btn-secondary btn-sm">
                                    <feather-icon icon="RefreshCcwIcon" />
                                </button>
                                <div class="title">
                                    <p class="p-0 m-0">Equitativo</p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-for="(puntaje, index) in formPuntajes.puntajes"
                            :key="puntaje.id"
                            class="question-score-wrapper"
                        >
                            <QuestionScore
                                :index="index + 1"
                                :descripcion="
                                    capitalizeWord(puntaje.descripcion)
                                "
                                :puntaje="puntaje.puntaje"
                                :disabled="index == formPuntajes.puntajes.length - 1 || disable_score_validation"
                                :active_plus="active_plus_validation"
                                :active_subtract="active_subtract_validation"
                                :active_edit="index == formPuntajes.puntajes.length - 1 && active_edit_validation"
                                @create="createAsignScore(puntaje, index + 1)"
                                @add="createAddScore(puntaje, index + 1)"
                                @subtract="createSubtractScore(puntaje, index + 1)"
                                @edit="createManualEdit(puntaje, index + 1)"
                            />
                        </div>

                        <p
                            v-if="
                                total_puntajes !== 20.0 &&
                                formPuntajes.puntajes.length != 0
                            "
                            class="text-danger mt-1"
                        >
                            ⚠ La suma de los puntajes debe ser exactamente
                            20.00. Actualmente es
                            {{ total_puntajes.toFixed(2) }}.
                            {{
                                total_puntajes - 20 > 0
                                    ? `El excedente es ${(
                                          total_puntajes - 20
                                      ).toFixed(2)}`
                                    : `El fantante es ${(
                                          (total_puntajes - 20) *
                                          -1
                                      ).toFixed(2)}`
                            }}.
                        </p>
                        <p
                            v-else-if="
                                validar_puntajes &&
                                formPuntajes.puntajes.length != 0
                            "
                            class="text-danger mt-1"
                        >
                            Los puntajes suman 20.00 pero no todas tienen
                            puntajes asignados.
                        </p>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closePuntajes"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cerrar</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storePuntajes"
                            :disabled="
                                total_puntajes > 20.0 ||
                                total_puntajes < 20.0 ||
                                validar_puntajes ||
                                formPuntajes.processing
                            "
                        >
                            <feather-icon icon="PlusIcon" />
                            <span>Guardar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalAsignScore">
                    <template #title>Actualizar puntaje</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Pregunta {{ formPuntaje.index }}:</h5>
                                    <p class="m-0 p-0">{{ formPuntaje.descripcion }}</p>
                                </div>
                                <InputSchedule
                                    :modelValue="formPuntaje.puntaje"
                                    @click-input="() => resetInput(formPuntaje, 'puntaje')"
                                    :hasError="v$.formPuntaje.puntaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'puntaje',
                                                formPuntaje
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPuntaje.puntaje.$error
                                            ? v$.formPuntaje.puntaje.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeAsignScore"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeAsignScore"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalAddScore">
                    <template #title>Agregar puntaje</template>
                    <form class="form form-vertical">
                        <div class="container-score__pregunta">
                            <h5 class="m-0 p-0">Pregunta {{ formPuntaje.index }}:</h5>
                            <p class="m-0 p-0">{{ formPuntaje.descripcion }}</p>
                        </div>
                        <div class="row mt-1">
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Puntaje actual"
                                    :modelValue="formPuntaje.puntaje"
                                    :disabled="true"
                                />
                                
                            </div>
                            <div class="col-md-5 col-lg-5">
                                <InputSchedule
                                    label="Puntaje a sumar"
                                    :modelValue="formAddScore.puntaje"
                                    @click-input="() => resetInput(formAddScore, 'puntaje')"
                                    :hasError="v$.formAddScore.puntaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'puntaje',
                                                formAddScore
                                            )
                                    "
                                    :errorMessage="
                                        v$.formAddScore.puntaje.$error
                                            ? 'El valor no puede ser 0 y no debe contener (,).'
                                            : ''
                                    "
                                    :disabled="asignar_todo"
                                    :onChange="() => totalPlus(formAddScore.puntaje, formPuntaje.puntaje)"
                                />
                            </div>
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Total"
                                    :modelValue="total_plus"
                                    :disabled="true"
                                />
                                
                            </div>
                        </div>
                    </form>
                    <div class="footer-add-score">
                        <p class="puntos-disponibles m-0 p-0">
                        Puntos disponibles:
                        <span>{{ (20 - total_puntajes).toFixed(2) }}</span>
                    </p>
                    <div class="form-check">
                        
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="asignar_todo"
                            v-model="asignar_todo"
                            @change="() => asignarTodo((20 - total_puntajes).toFixed(2), formPuntaje.puntaje)"
                        />
                        <label class="form-check-label" for="asignar_todo">
                            Asignar todo
                        </label>
                    </div>
                    </div>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeAddScore"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeAddScore"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalSubtractScore">
                    <template #title>Restar puntaje</template>
                    <form class="form form-vertical">
                        <div class="container-score__pregunta">
                            <h5 class="m-0 p-0">Pregunta {{ formPuntaje.index }}:</h5>
                            <p class="m-0 p-0">{{ formPuntaje.descripcion }}</p>
                        </div>
                        <div class="row mt-1">
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Puntaje actual"
                                    :modelValue="formPuntaje.puntaje"
                                    :disabled="true"
                                />
                                
                            </div>
                            <div class="col-md-6 col-lg-6"">
                                <InputSchedule
                                    label="Puntaje a restar"
                                    :modelValue="formSubtractScore.puntaje"
                                    @click-input="() => resetInput(formSubtractScore, 'puntaje')"
                                    :hasError="v$.formSubtractScore.puntaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'puntaje',
                                                formSubtractScore
                                            )
                                    "
                                    :errorMessage="
                                        v$.formSubtractScore.puntaje.$error
                                            ? 'El valor no puede ser 0 y no debe contener (,).'
                                            : ''
                                    "
                                    :disabled="asignar_todo"
                                    :onChange="() => totalSubtract(formSubtractScore.puntaje, formPuntaje.puntaje)"
                                />
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Total"
                                    :modelValue="total_subtract"
                                    :disabled="true"
                                />
                                
                            </div>
                        </div>
                    </form>
                    <div class="footer-add-score">
                        <p class="puntos-disponibles m-0 p-0">
                        Puntos disponibles:
                        <span>{{ (20 - total_puntajes).toFixed(2) * -1 }}</span>
                    </p>
                    <div class="form-check">
                        
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="asignar_todo_subtract"
                            v-model="asignar_todo"
                            @change="() => asignarTodoSubtract((20 - total_puntajes).toFixed(2), formPuntaje.puntaje)"
                        />
                        <label class="form-check-label" for="asignar_todo_subtract">
                            Asignar todo
                        </label>
                    </div>
                    </div>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeSubtractScore"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeSubtractScore"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalManualEdit">
                    <template #title>Editar puntaje manualmente</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Pregunta {{ formPuntaje.index }}:</h5>
                                    <p class="m-0 p-0">{{ formPuntaje.descripcion }}</p>
                                </div>
                                <InputSchedule
                                    :modelValue="formPuntaje.puntaje"
                                    @click-input="() => resetInput(formPuntaje, 'puntaje')"
                                    :hasError="v$.formPuntaje.puntaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'puntaje',
                                                formPuntaje
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPuntaje.puntaje.$error
                                            ? v$.formPuntaje.puntaje.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeManualEdit"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeManualEdit"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>

                <Modal ref="modalEditAlternativas" :loading="formPregunta.processing">
                    <template #title>Editar alternativas</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <p class="title-alternativas m-0 p-0"><strong>Pregunta {{ index_alternativa }}:</strong></p>
                                <div class="header-alternativas d-flex justify-content-between align-items-center pb-1">
                                    <p class="p-0 m-0 mt-1">{{ capitalizeWord(formPregunta.descripcion) }}</p>
                                    <div class="btn-control  mt-1">
                                        <button
                                            class="btn btn-dark btn-sm btn-header-alternativa"
                                            @click.prevent="createPorcentajes"
                                        >
                                            <p class="p-0 m-0">
                                                Asignar porcentajes
                                            </p>
                                            <div v-if="estado_incompleto" class="alert-icon">
                                                <span>
                                                    <feather-icon
                                                        stroke-width="3px"
                                                        size="12"
                                                        icon="AlertTriangleIcon"
                                                    ></feather-icon>
                                                </span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <AddMultiOption
                                v-if="formPregunta.id_tipo_pregunta == 2"
                                :value="
                                    formPregunta.multi_selection_alternativas
                                "
                                :save_mode="true"
                                @delete="deleteAlternativa"
                                @update-check="checkAlternativa"
                                @create="createAlternativa"
                                @update-alternativa="createDescripcion"
                            />
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeEditAlternativas"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cancelar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalPorcentajes" :loading="formPorcentajes.processing">
                    <template #title>Asignar porcentajes</template>
                    <form class="form form-vertical">
                        <div class="calcular-puntajes-container pb-1">
                            <div
                                class="calcular-btn"
                                @click.prevent="autoCalculatePorcentajes"
                            >
                                <button class="btn btn-secondary btn-sm">
                                    <feather-icon icon="RefreshCcwIcon" />
                                </button>
                                <div class="title">
                                    <p class="p-0 m-0">Equitativo</p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-for="(porcentaje, index) in formPorcentajes.porcentajes"
                            :key="porcentaje.id"
                            class="question-score-wrapper"
                        >
                            <QuestionScore
                                label="Alternativa"
                                :index="index + 1"
                                :descripcion="
                                    capitalizeWord(porcentaje.descripcion)
                                "
                                :puntaje="porcentaje.porcentaje"
                                :disabled="index == formPorcentajes.porcentajes.length - 1 || disable_percent_validation"
                                :active_plus="active_plus_percent_validation"
                                :active_subtract="active_subtract_percent_validation"
                                :active_edit="index == formPorcentajes.porcentajes.length - 1 && active_edit_percent_validation"
                                @create="createAsignPercent(porcentaje, index + 1)"
                                @add="createAddPercent(porcentaje, index + 1)"
                                @subtract="createSubtractPercent(porcentaje, index + 1)"
                                @edit="createManualPercentEdit(porcentaje, index + 1)"
                            />
                        </div>

                        <p
                            v-if="
                                total_porcentajes !== 100.0 &&
                                formPorcentajes.porcentajes.length != 0
                            "
                            class="text-danger mt-1"
                        >
                            ⚠ La suma de los porcentajes debe ser exactamente
                            100.00. Actualmente es
                            {{ total_porcentajes.toFixed(2) }}.
                            {{
                                total_porcentajes - 100 > 0
                                    ? `El excedente es ${(
                                        total_porcentajes - 100
                                      ).toFixed(2)}`
                                    : `El fantante es ${(
                                          (total_porcentajes - 100) *
                                          -1
                                      ).toFixed(2)}`
                            }}.
                        </p>
                        <p
                            v-else-if="
                                validar_porcentajes &&
                                formPorcentajes.porcentajes.length != 0
                            "
                            class="text-danger mt-1"
                        >
                            Los porcentajes suman 100.00 pero no todos tienen
                            valores asignados.
                        </p>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closePorcentajes"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storePorcentajes"
                            :disabled="
                                total_porcentajes > 100.0 ||
                                total_porcentajes < 100.0 ||
                                validar_porcentajes ||
                                formPorcentajes.processing
                            "
                        >
                            <feather-icon icon="PlusIcon" />
                            <span>Guardar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalAsignPercent">
                    <template #title>Actualizar porcentaje</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Alternativa {{ formPorcentaje.index }}:</h5>
                                    <p class="m-0 p-0">{{ formPorcentaje.descripcion }}</p>
                                </div>
                                <InputSchedule
                                    :modelValue="formPorcentaje.porcentaje"
                                    @click-input="() => resetInput(formPorcentaje, 'porcentaje')"
                                    :hasError="v$.formPorcentaje.porcentaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'porcentaje',
                                                formPorcentaje
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPorcentaje.porcentaje.$error
                                            ? v$.formPorcentaje.porcentaje.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeAsignPercent"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeAsignPercent"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalAddPercent">
                    <template #title>Agregar porcentaje</template>
                    <form class="form form-vertical">
                        <div class="container-score__pregunta">
                            <h5 class="m-0 p-0">Alternativa {{ formPorcentaje.index }}:</h5>
                            <p class="m-0 p-0">{{ formPorcentaje.descripcion }}</p>
                        </div>
                        <div class="row mt-1">
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="P. actual"
                                    :modelValue="formPorcentaje.porcentaje"
                                    :disabled="true"
                                />
                                
                            </div>
                            <div class="col-md-5 col-lg-5">
                                <InputSchedule
                                    label="Porcentaje a sumar"
                                    :modelValue="formAddPercent.porcentaje"
                                    @click-input="() => resetInput(formAddPercent, 'porcentaje')"
                                    :hasError="v$.formAddPercent.porcentaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'porcentaje',
                                                formAddPercent
                                            )
                                    "
                                    :errorMessage="
                                        v$.formAddPercent.porcentaje.$error
                                            ? 'El valor no puede ser 0 y no debe contener (,).'
                                            : ''
                                    "
                                    :disabled="asignar_todo_porcentaje"
                                    :onChange="() => totalPlusPercent(formAddPercent.porcentaje, formPorcentaje.porcentaje)"
                                />
                            </div>
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Total"
                                    :modelValue="total_plus_percent"
                                    :disabled="true"
                                />
                                
                            </div>
                        </div>
                    </form>
                    <div class="footer-add-score">
                        <p class="puntos-disponibles m-0 p-0">
                        Puntos disponibles:
                        <span>{{ (100 - total_porcentajes).toFixed(2) }}</span>
                    </p>
                    <div class="form-check">
                        
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="asignar_todo_porcentaje"
                            v-model="asignar_todo_porcentaje"
                            @change="() => asignarTodoPorcentaje((100 - total_porcentajes).toFixed(2), formPorcentaje.porcentaje)"
                        />
                        <label class="form-check-label" for="asignar_todo_porcentaje">
                            Asignar todo
                        </label>
                    </div>
                    </div>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeAddPercent"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeAddPercent"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalSubtractPercent">
                    <template #title>Restar porcentaje</template>
                    <form class="form form-vertical">
                        <div class="container-score__pregunta">
                            <h5 class="m-0 p-0">Alternativa {{ formPorcentaje.index }}:</h5>
                            <p class="m-0 p-0">{{ formPorcentaje.descripcion }}</p>
                        </div>
                        <div class="row mt-1">
                            
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="P. actual"
                                    :modelValue="formPorcentaje.porcentaje"
                                    :disabled="true"
                                />
                                
                            </div>
                            <div class="col-md-6 col-lg-6"">
                                <InputSchedule
                                    label="Porcentaje a restar"
                                    :modelValue="formSubtractPercent.porcentaje"
                                    @click-input="() => resetInput(formSubtractPercent, 'porcentaje')"
                                    :hasError="v$.formSubtractPercent.porcentaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'porcentaje',
                                                formSubtractPercent
                                            )
                                    "
                                    :errorMessage="
                                        v$.formSubtractPercent.porcentaje.$error
                                            ? 'El valor no puede ser 0 y no debe contener (,).'
                                            : ''
                                    "
                                    :disabled="asignar_todo_porcentaje"
                                    :onChange="() => totalSubtractPercent(formSubtractPercent.porcentaje, formPorcentaje.porcentaje)"
                                />
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <InputSchedule
                                    label="Total"
                                    :modelValue="total_subtract_percent"
                                    :disabled="true"
                                />
                                
                            </div>
                        </div>
                    </form>
                    <div class="footer-add-score">
                        <p class="puntos-disponibles m-0 p-0">
                        Puntos disponibles:
                        <span>{{ (100 - total_porcentajes).toFixed(2) * -1 }}</span>
                    </p>
                    <div class="form-check">
                        
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="asignar_todo_subtract_percent"
                            v-model="asignar_todo_porcentaje"
                            @change="() => asignarTodoSubtractPorcentaje((100 - total_porcentajes).toFixed(2), formPorcentaje.porcentaje)"
                        />
                        <label class="form-check-label" for="asignar_todo_subtract_percent">
                            Asignar todo
                        </label>
                    </div>
                    </div>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeSubtractPercent"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeSubtractPercent"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>
                <Modal ref="modalManualPercentEdit">
                    <template #title>Editar porcentaje manualmente</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Alternativa {{ formPorcentaje.index }}:</h5>
                                    <p class="m-0 p-0">{{ formPorcentaje.descripcion }}</p>
                                </div>
                                <InputSchedule
                                    :modelValue="formPorcentaje.porcentaje"
                                    @click-input="() => resetInput(formPorcentaje, 'porcentaje')"
                                    :hasError="v$.formPorcentaje.porcentaje.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'porcentaje',
                                                formPorcentaje
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPorcentaje.porcentaje.$error
                                            ? v$.formPorcentaje.porcentaje.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeManualPercentEdit"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeManualPercentEdit"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Asignar</span>
                        </button>
                    </template>
                </Modal>

                <Modal ref="modalDescripcion" :loading="formPorcentajes.processing">
                    <template #title>Actualizar alternativa</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Alternativa {{ formPorcentaje.index }}:</h5>
                                </div>
                                <InputSchedule
                                    :modelValue="formPorcentaje.descripcion"
                                    @click-input="() => resetInput(formPorcentaje, 'descripcion')"
                                    :hasError="v$.formPorcentaje.descripcion.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'descripcion',
                                                formPorcentaje
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPorcentaje.descripcion.$error
                                            ? v$.formPorcentaje.descripcion.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeDescripcion"
                        >
                            <feather-icon icon="ArrowLeftIcon" />
                            <span>Atrás</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeDescripcion"
                            :disabled="formPorcentaje.processing"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Editar</span>
                        </button>
                    </template>
                </Modal>

                <Modal ref="modalEditSimpleAlternativas" :loading="formPregunta.processing">
                    <template #title>Editar alternativas</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <p class="title-alternativas"><strong>Pregunta {{ index_alternativa }}:</strong></p>
                                <div class="d-flex justify-content-between align-items-center pb-1">
                                    <p class="p-0 m-0">{{ capitalizeWord(formPregunta.descripcion) }}</p>
                                </div>
                            </div>

                            <AddSimpleOption
                                v-if="formPregunta.id_tipo_pregunta == 1"
                                :value="formPregunta.simple_selection_alternativas"
                                :save_mode="true"
                                :validar_correctas="validar_correctas"
                                :validar_cantidad_alternativas="validar_cantidad_alternativas"
                                @delete="deleteAlternativa"
                                @update-check="checkAlternativa"
                                @create="createAlternativa"
                                @update-alternativa="createDescripcion"
                            />
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeEditSimpleAlternativas"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cerrar</span>
                        </button>
                    </template>
                </Modal>

                <Modal ref="modalEditTrueFalseAlternativas" :loading="formPregunta.processing">
                    <template #title>Editar alternativas</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <p class="title-alternativas m-0 p-0"><strong>Pregunta {{ index_alternativa }}:</strong></p>
                                <div class="header-alternativas d-flex justify-content-between align-items-center pb-1">
                                    <p class="p-0 m-0 mt-1">{{ capitalizeWord(formPregunta.descripcion) }}</p>
                                    <div class="btn-control mt-1">
                                        <button
                                            class="btn btn-primary btn-sm btn-header-alternativa"
                                            @click.prevent="changeCorrect"
                                        >
                                            <p class="p-0 m-0 header-text-alternativa">
                                                Cambiar correcto
                                            </p>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <AddTrueFalseSelection
                                v-if="formPregunta.id_tipo_pregunta == 3"
                                :value="formPregunta.simple_selection_alternativas"
                                :save_mode="true"
                                :validar_correctas="validar_correctas"
                                :validar_cantidad_alternativas="validar_cantidad_alternativas"
                                @delete="deleteAlternativa"
                                @update-check="checkAlternativa"
                                @create="createAlternativa"
                                @update-alternativa="createDescripcion"
                            />
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeEditTrueFalseAlternativas"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cerrar</span>
                        </button>
                    </template>
                </Modal>

                <Modal ref="modalEditPregunta" :loading="formPregunta.processing">
                    <template #title>Actualizar pregunta</template>
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="container-score__pregunta">
                                    <h5 class="m-0 p-0">Pregunta {{ index_alternativa }}:</h5>
                                </div>
                                <InputSchedule
                                    :modelValue="formPregunta.descripcion"
                                    @click-input="() => resetInput(formPregunta, 'descripcion')"
                                    :hasError="v$.formPregunta.descripcion.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'descripcion',
                                                formPregunta
                                            )
                                    "
                                    :errorMessage="
                                        v$.formPregunta.descripcion.$error
                                            ? v$.formPregunta.descripcion.$errors[0]
                                                  .$message
                                            : ''
                                    "
                                />
                            </div>
                        </div>
                    </form>
                    <template #footer>
                        <button
                            class="btn btn-outline-danger"
                            @click.prevent="closeEditPregunta"
                        >
                            <feather-icon icon="XIcon" />
                            <span>Cerrar</span>
                        </button>
                        <button
                            class="btn btn-success"
                            @click.prevent="storeEditPregunta"
                            :disabled="formPregunta.processing"
                        >
                            <feather-icon icon="EditIcon" />
                            <span>Editar</span>
                        </button>
                    </template>
                </Modal>
            </Card>
        </div>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import { capitalizeFirstWord } from "../../utils/textProcess";
import { cleanObject } from "../../utils/objectProcess";
import {
    alertError,
    alertSuccess,
    alertWarning,
    confirm,
} from "../../sweetAlert2";
import { qualificationsValidator } from "../../Validators/qualificationsValidator";
import useVuelidate from "@vuelidate/core";
import Card from "../../Components/Card.vue";
import Modal from "../../Components/Modal.vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import AddActivity from "./components/AddActivity.vue";
import MultipleOption from "./components/options/MultipleOption.vue";
import SimpleSelection from "./components/options/SimpleSelection.vue";
import TextAnswer from "./components/options/TextAnswer.vue";
import TrueFalseSelection from "./components/options/TrueFalseSelection.vue";
import InputSchedule from "../Schedule/components/InputSchedule.vue";
import SelectSchedule from "../Schedule/components/SelectSchedule.vue";
import AddSimpleOption from "./components/AddSimpleOption.vue";
import AddMultiOption from "./components/AddMultiOption.vue";
import Option from "./components/options/Option.vue";
import QuestionScore from "./components/QuestionScore.vue";
import OnlineSignal from "./components/OnlineSignal.vue";
import OptionButtons from "./components/options/OptionButtons.vue";
import AddTrueFalseSelection from "./components/AddTrueFalseSelection.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import { qualificationsValidator2 } from "../../Validators/qualificationsValidator2";
import { descriptionValidator } from "../../Validators/descriptionValidator";

export default {
    components: {
        Card,
        LayoutContent,
        SimpleSelection,
        MultipleOption,
        TrueFalseSelection,
        TextAnswer,
        AddActivity,
        Modal,
        InputSchedule,
        SelectSchedule,
        AddSimpleOption,
        AddMultiOption,
        AddTrueFalseSelection,
        Option,
        QuestionScore,
        OnlineSignal,
        OptionButtons,
        ReturnButton,
    },
    name: "Exam",
    props: {
        urlRoute: String,
        curso_docente: Object,
        recurso_examen: Object,
        tipo_pregunta: Array,
        verify_alert: Boolean,
        suma_puntaje: Number,
    },
    data() {
        return {
            formPregunta: useForm({
                id_recurso_examen: 0,
                id_tipo_pregunta: 0,
                descripcion: "",
                contestacion: 1,
                simple_selection_alternativas: [],
                multi_selection_alternativas: [],
            }),
            formPuntajes: useForm({
                puntajes: [],
            }),
            formPuntaje: useForm({
                id: 0,
                puntaje: "",
                descripcion: "",
                index: 0,
            }),
            formAddScore: useForm({
                pregunta: {},
                puntaje: "",
            }),
            formSubtractScore: useForm({
                pregunta: {},
                puntaje: "",
            }),
            total_puntajes: 0,

            formPorcentajes: useForm({
                porcentajes: [],
            }),
            formPorcentaje: useForm({
                id: 0,
                porcentaje: "",
                descripcion: "",
                index: 0,
            }),
            formAddPercent: useForm({
                alternativa: {},
                porcentaje: "",
            }),
            formSubtractPercent: useForm({
                alternativa: {},
                porcentaje: "",
            }),
            total_porcentajes: 0,

            // VALIDACIONES PUNTAJES

            validar_puntajes: false,
            validar_ultimo_puntaje: false,
            alert_validation: false,
            mostrar_validation: false,
            disable_score_validation: false,
            active_plus_validation: false,
            active_subtract_validation: false,
            active_edit_validation: false,
            asignar_todo: false,
            local_edit: false,

            total_plus: "",
            total_subtract: "",

            // VALIDACIONES PORCENTAJES
            validar_porcentajes: false,
            validar_ultimo_porcentaje: false,
            alert_validation_percent: false,
            disable_percent_validation: false,
            active_plus_percent_validation: false,
            active_subtract_percent_validation: false,
            active_edit_percent_validation: false,
            asignar_todo_porcentaje: false,
            local_edit_porcentaje: false,

            total_plus_percent: "",
            total_subtract_percent: "",

            // VALIDACIONES SIMPLE

            validar_correctas: false,
            validar_cantidad_alternativas: false,

            index_alternativa: 0,
            id_pregunta: 0,
            estado_incompleto: false,
            suma_porcentajes: 0,
        };
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        openParentModal() {
            this.$refs.modalPregunta.show()
        },

        // MODALS

        // PREGUNTA
         
        closePregunta() {
            this.$refs.modalPregunta.hide();
        },
        createPregunta() {
            this.formPregunta.reset();
            this.$refs.modalPregunta.show();
        },
        storePregunta() {
            this.formPregunta.id_recurso_examen = this.recurso_examen.id;
            const data = this.formPregunta.data();

            const newData = cleanObject(data);

            if (!newData.descripcion) {
                alertError('La descripción de la pregunta es obligatoria')
                return;
            } 

            this.formPregunta.processing = true;

            if (this.formPregunta.id_tipo_pregunta == 1) {
                const tieneCorrecta =
                    this.formPregunta.simple_selection_alternativas.some(
                        (alt) => alt.correcta
                    );

                if (
                    this.formPregunta.simple_selection_alternativas.length == 0
                ) {
                    alertWarning("Aun no haz creado ninguna alternativa");
                    this.formPregunta.processing = false;
                    return;
                } else if (
                    this.formPregunta.simple_selection_alternativas.length < 2
                ) {
                    alertWarning("Debes crear al menos 2 alternativas");
                    this.formPregunta.processing = false;
                    return;
                } else if (!tieneCorrecta) {
                    alertWarning(
                        "Debe seleccionar al menos una respuesta correcta"
                    );
                    this.formPregunta.processing = false;
                    return;
                }

                this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/store`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "recursosExamenesPreguntas",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                                "suma_puntaje",
                            ],
                        });
                    })
                    .catch((err) => alertError(err.response.data.error))
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
            } else if (this.formPregunta.id_tipo_pregunta == 2) {
                const tieneAlMenosDosCorrectas =
                    this.formPregunta.multi_selection_alternativas.filter(
                        (alt) => alt.correcta
                    ).length >= 2;

                if (
                    this.formPregunta.multi_selection_alternativas.length == 0
                ) {
                    alertWarning("Aun no haz creado ninguna alternativa");
                    this.formPregunta.processing = false;
                    return;
                } else if (
                    this.formPregunta.multi_selection_alternativas.length < 2
                ) {
                    alertWarning("Debes crear al menos 2 alternativas");
                    this.formPregunta.processing = false;
                    return;
                } else if (!tieneAlMenosDosCorrectas) {
                    alertWarning(
                        "Debe seleccionar al menos dos respuestas correctas"
                    );
                    this.formPregunta.processing = false;
                    return;
                }

                const alternativas = newData.multi_selection_alternativas.map(
                    (alternativa) => ({
                        ...alternativa,
                        porcentaje: (alternativa.porcentaje / 100).toFixed(4), // Divide entre 100 y mantiene 2 decimales
                    })
                );

                newData.multi_selection_alternativas = alternativas;

                this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/store`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "recursosExamenesPreguntas",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                                "suma_puntaje",
                            ],
                        });
                    })
                    .catch((err) => {
                        alertError(err.response.data.error)
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
            } else if (this.formPregunta.id_tipo_pregunta == 3) {
                this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/store`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "recursosExamenesPreguntas",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                                "suma_puntaje",
                            ],
                        });
                    })
                    .catch((err) => alertError(err.response.data.error))
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
            } else if (this.formPregunta.id_tipo_pregunta == 4) {
                this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/store`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "recursosExamenesPreguntas",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                                "suma_puntaje",
                            ],
                        });
                    })
                    .catch((err) => {
                        alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
            }
        },

        // PUNTAJE

        createPuntajes() {
            this.$refs.modalPuntajes.show();
        },
        closePuntajes() {
            this.$refs.modalPuntajes.hide();
        },
        storePuntajes() {
            if (this.total_puntajes != 20) {
                alertWarning(
                    "La suma de los puntajes debe ser exactamente 20.00."
                );
                return;
            }

            if (this.validar_puntajes) {
                alertWarning(
                    "Todas las preguntas deben tener puntajes asignados."
                );
                return;
            }

            const data = this.formPuntajes.data();

            const id_seccion_recurso = this.recurso_examen.seccion_recurso.id;

            const newData = {
                ...data,
                id_seccion_recurso,
            };

            this.formPuntajes.processing = true;

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/update-puntajes`
                ),
                data: newData,
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$inertia.reload({
                        only: [
                            "recurso_examen",
                            "verify_alert",
                            "suma_puntaje",
                        ],
                    });
                })
                .catch((err) => alertError(err.response.data.error))
                .finally(() => {
                    this.formPuntajes.processing = false;
                });

        },

        // ASIGNAR PUNTAJE

        createAsignScore(puntaje, index) {
            this.formPuntaje.reset();
            this.$refs.modalPuntajes.hide()
            this.$refs.modalAsignScore.show();

            this.formPuntaje.id = puntaje.id;
            this.formPuntaje.puntaje = puntaje.puntaje;
            this.formPuntaje.descripcion = puntaje.descripcion
            this.formPuntaje.index = index
        },
        closeAsignScore() {
            this.$refs.modalAsignScore.hide();
            this.$refs.modalPuntajes.show()
            this.formPuntaje.reset()
        },
        storeAsignScore() {
            this.v$.formPuntaje.$touch();
            if (this.v$.formPuntaje.$invalid) {
                return;
            }

            const tareaIndex = this.formPuntajes.puntajes.findIndex(
                (calificacion) => calificacion.id === this.formPuntaje.id
            );
            if (tareaIndex !== -1) {
                this.formPuntajes.puntajes[tareaIndex].puntaje = parseFloat(
                    this.formPuntaje.puntaje
                ).toFixed(2);
            }

            this.$refs.modalAsignScore.hide();
            this.formPuntaje.reset();

            const numPreguntas = this.formPuntajes.puntajes.length;
            const puntajesValidos = this.formPuntajes.puntajes
                .slice(0, numPreguntas - 1)
                .every(
                    (puntaje) =>
                        puntaje.puntaje !== null && !isNaN(puntaje.puntaje)
                );

            if (puntajesValidos) {
                this.ajustarUltimoPuntaje();
            }

            this.$refs.modalPuntajes.show()
            this.formPuntaje.reset()

            this.calcularPuntaje();
            this.validarPuntajes();
            this.validarUltimoPuntaje();
            this.disableScoreValidation();
            this.activePlusValidation();
            this.activeSubtractValidation();
            this.activeEditValidation()
        },

        // AGREGAR PUNTAJE
        
        createAddScore(puntaje, index) {
            this.$refs.modalPuntajes.hide()
            this.formAddScore.reset();
            this.$refs.modalAddScore.show();
            this.asignar_todo = false;
            this.total_plus = ""

            this.formAddScore.pregunta = puntaje
            this.formPuntaje.id = puntaje.id;
            this.formPuntaje.puntaje = puntaje.puntaje;
            this.formPuntaje.descripcion = puntaje.descripcion
            this.formPuntaje.index = index
        },
        closeAddScore() {
            this.$refs.modalAddScore.hide();
            this.$refs.modalPuntajes.show()
        },
        storeAddScore() {
            this.v$.formAddScore.$touch();
            if (this.v$.formAddScore.$invalid) {
                return;
            }

            const data = this.formAddScore.data()

            const data_puntaje = parseFloat(data.puntaje)

            if ((20 - this.total_puntajes).toFixed(2) < data_puntaje) {
                alertWarning(`El puntaje a sumar debe estar entre 0.01 y ${(20 - this.total_puntajes).toFixed(
                                                2
                                            )} y sin contener (,).`)
                return;
            }

            this.addPuntaje(data.pregunta, data.puntaje)

            if (this.total_puntajes == 20 || this.suma_puntaje != 0) {
                this.local_edit = false;
            }

            this.$refs.modalAddScore.hide();
            this.$refs.modalPuntajes.show()
            this.formAddScore.reset();
            this.formPuntaje.reset()
        },

        // RESTAR PUNTAJE

        createSubtractScore(puntaje, index) {
            this.$refs.modalPuntajes.hide()
            this.formSubtractScore.reset();
            this.$refs.modalSubtractScore.show();
            this.asignar_todo = false;
            this.total_subtract = ""

            this.formSubtractScore.pregunta = puntaje
            this.formPuntaje.id = puntaje.id;
            this.formPuntaje.puntaje = puntaje.puntaje;
            this.formPuntaje.descripcion = puntaje.descripcion
            this.formPuntaje.index = index
        },
        closeSubtractScore() {
            this.$refs.modalSubtractScore.hide();
            this.$refs.modalPuntajes.show()
        },
        storeSubtractScore() {
            this.v$.formSubtractScore.$touch();
            if (this.v$.formSubtractScore.$invalid) {
                return;
            }

            const data = this.formSubtractScore.data()

            const data_puntaje = parseFloat(data.puntaje)

            if ((20 - this.total_puntajes).toFixed(2) * -1 < data_puntaje) {
                alertWarning(`El puntaje a restar debe estar entre 0.01 y ${(20 - this.total_puntajes).toFixed(
                                                2
                                            )*-1} y sin contener (,).`)
                return;
            }

            if (this.total_subtract <= 0) {
                alertWarning('El total no debe ser 0 o un numero negativo')
                return;
            }

            this.subtractPuntaje(data.pregunta, data.puntaje)

            if (this.total_puntajes == 20 || this.suma_puntaje != 0) {
                this.local_edit = false
            }

            this.$refs.modalSubtractScore.hide();
            this.$refs.modalPuntajes.show()
            this.formSubtractScore.reset();
            this.formPuntaje.reset()
        },

        // EDITAR PUNTAJE MANUALMENTE
        
        createManualEdit(puntaje, index) {
            this.formPuntaje.reset();
            this.$refs.modalPuntajes.hide()
            this.$refs.modalManualEdit.show();

            this.formPuntaje.id = puntaje.id;
            this.formPuntaje.puntaje = puntaje.puntaje;
            this.formPuntaje.descripcion = puntaje.descripcion
            this.formPuntaje.index = index
        },
        closeManualEdit() {
            this.$refs.modalManualEdit.hide();
            this.$refs.modalPuntajes.show()
            this.formPuntaje.reset()
        },
        storeManualEdit() {
            this.v$.formPuntaje.$touch();
            if (this.v$.formPuntaje.$invalid) {
                return;
            }

            this.formPuntajes.puntajes[this.formPuntajes.puntajes.length - 1].puntaje = parseFloat(this.formPuntaje.puntaje).toFixed(2);
            this.local_edit = true

            this.$refs.modalManualEdit.hide();
            this.formPuntaje.reset();

            this.$refs.modalPuntajes.show()
            this.formPuntaje.reset()

            this.calcularPuntaje();
            this.validarPuntajes();
            this.validarUltimoPuntaje();
            this.disableScoreValidation();
            this.activePlusValidation();
            this.activeSubtractValidation();
            this.activeEditValidation()
        },

        // EDITAR ALTERNATIVAS
        createEditAlternativas(pregunta, index) {
            console.log('pregunta', pregunta)
            this.formPregunta.reset();
            
            this.index_alternativa = index
            this.id_pregunta = pregunta.id
            this.suma_porcentajes = pregunta.suma_porcentaje
            this.formPregunta.id_recurso_examen = pregunta.id_recurso_examen
            this.formPregunta.id_tipo_pregunta = pregunta.id_tipo_pregunta
            this.formPregunta.descripcion = pregunta.descripcion
            this.formPregunta.multi_selection_alternativas = pregunta.alternativas

            this.updateEstadoIncompleto(pregunta.id)

            this.asignarPorcentajes(pregunta.id)
            this.calcularPorcentaje(pregunta.id);
            this.validarPorcentajes(pregunta.id);
            this.validarUltimoPorcentaje(pregunta.id);

            // VALIDACIONES

            this.alertValidationPercent(pregunta.id);
            this.disablePercentValidation(pregunta.id);
            this.activePlusPercentValidation(pregunta.id);
            this.activeSubtractPercentValidation(pregunta.id);
            this.activeEditPercentValidation(pregunta.id);

            this.$refs.modalEditAlternativas.show();
        },
        closeEditAlternativas() {
            this.$refs.modalEditAlternativas.hide();
        },

        // CREAR PORCENTAJES

        createPorcentajes() {
            this.asignarPorcentajes(this.id_pregunta)
            this.$refs.modalEditAlternativas.hide();
            this.$refs.modalPorcentajes.show();
        },
        closePorcentajes() {
            this.$refs.modalPorcentajes.hide();
            this.$refs.modalEditAlternativas.show();
        },
        storePorcentajes() {
            if (this.total_porcentajes != 100) {
                alertWarning(
                    "La suma de los puntajes debe ser exactamente 100.00."
                );
                return;
            }

            if (this.validar_porcentajes) {
                alertWarning(
                    "Todas las preguntas deben tener puntajes asignados."
                );
                return;
            }

            const data = this.formPorcentajes.data();

            this.formPorcentajes.processing = true;

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/update-porcentajes/${this.id_pregunta}`
                ),
                data: data,
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$inertia.reload({
                        only: [
                            "recurso_examen",
                        ],
                    });

                    console.log(res.data)

                    // VALIDACIONES

                    this.alertValidationPercent(this.id_pregunta);
                    this.disablePercentValidation(this.id_pregunta);
                    this.activePlusPercentValidation(this.id_pregunta);
                    this.activeSubtractPercentValidation(this.id_pregunta);
                    this.activeEditPercentValidation(this.id_pregunta);
                })
                .catch((err) => alertError(err.response.data.error))
                .finally(() => {
                    this.formPorcentajes.processing = false;
                });

        },

        // ASIGNAR PORCENTAJES
        
        createAsignPercent(porcentaje, index) {
            this.formPorcentaje.reset();
            this.$refs.modalPorcentajes.hide()
            this.$refs.modalAsignPercent.show();

            this.formPorcentaje.id = porcentaje.id;
            this.formPorcentaje.porcentaje = porcentaje.porcentaje;
            this.formPorcentaje.descripcion = porcentaje.descripcion
            this.formPorcentaje.index = index
        },
        closeAsignPercent() {
            this.$refs.modalAsignPercent.hide();
            this.$refs.modalPorcentajes.show()
            this.formPorcentaje.reset()
        },
        storeAsignPercent() {
            this.v$.formPorcentaje.$touch();
            if (this.v$.formPorcentaje.$invalid) {
                return;
            }

            const porcentajeIndex = this.formPorcentajes.porcentajes.findIndex(
                (porcentaje) => porcentaje.id === this.formPorcentaje.id
            );
            if (porcentajeIndex !== -1) {
                this.formPorcentajes.porcentajes[porcentajeIndex].porcentaje = parseFloat(
                    this.formPorcentaje.porcentaje
                ).toFixed(2);
            }

            this.$refs.modalAsignPercent.hide();
            this.formPorcentaje.reset();

            const numAlternativas = this.formPorcentajes.porcentajes.length;
            const porcentajesValidos = this.formPorcentajes.porcentajes
                .slice(0, numAlternativas - 1)
                .every(
                    (porcentaje) =>
                        porcentaje.porcentaje !== null && !isNaN(porcentaje.porcentaje)
                );

            if (porcentajesValidos) {
                console.log('llega')
                this.ajustarUltimoPorcentaje();
            }

            this.$refs.modalPorcentajes.show()
            this.formPorcentaje.reset()

            this.calcularPorcentaje(this.id_pregunta);
            this.validarPorcentajes(this.id_pregunta);
            this.validarUltimoPorcentaje(this.id_pregunta);
            this.disablePercentValidation(this.id_pregunta);
            this.activePlusPercentValidation(this.id_pregunta);
            this.activeSubtractPercentValidation(this.id_pregunta);
            this.activeEditPercentValidation(this.id_pregunta)
        },

        // AGREGAR PORCENTAJE
        
        createAddPercent(porcentaje, index) {
            this.$refs.modalPorcentajes.hide()
            this.formAddPercent.reset();
            this.$refs.modalAddPercent.show();
            this.asignar_todo_porcentaje = false;
            this.total_plus_percent = ""

            this.formAddPercent.alternativa = porcentaje
            this.formPorcentaje.id = porcentaje.id;
            this.formPorcentaje.porcentaje = porcentaje.porcentaje;
            this.formPorcentaje.descripcion = porcentaje.descripcion
            this.formPorcentaje.index = index
        },
        closeAddPercent() {
            this.$refs.modalAddPercent.hide();
            this.$refs.modalPorcentajes.show()
        },
        storeAddPercent() {
            this.v$.formAddPercent.$touch();
            if (this.v$.formAddPercent.$invalid) {
                return;
            }

            const data = this.formAddPercent.data()

            const data_porcentaje = parseFloat(data.porcentaje)

            if ((100 - this.total_porcentajes).toFixed(2) < data_porcentaje) {
                alertWarning(`El porcentaje a sumar debe estar entre 0.01 y ${(100 - this.total_porcentajes).toFixed(
                                                2
                                            )} y sin contener (,).`)
                return;
            }

            this.addPorcentaje(data.alternativa, data.porcentaje)

            if (this.total_porcentajes == 100 || this.suma_porcentajes != 0) {
                this.local_edit_porcentaje = false;
            }

            this.$refs.modalAddPercent.hide();
            this.$refs.modalPorcentajes.show()
            this.formAddPercent.reset();
            this.formPuntaje.reset()
        },

        // RESTAR PORCENTAJE

        createSubtractPercent(porcentaje, index) {
            this.$refs.modalPorcentajes.hide()
            this.formSubtractPercent.reset();
            this.$refs.modalSubtractPercent.show();
            this.asignar_todo_porcentaje = false;
            this.total_subtract_percent = ""

            this.formSubtractPercent.alternativa = porcentaje
            this.formPorcentaje.id = porcentaje.id;
            this.formPorcentaje.porcentaje = porcentaje.porcentaje;
            this.formPorcentaje.descripcion = porcentaje.descripcion
            this.formPorcentaje.index = index
        },
        closeSubtractPercent() {
            this.$refs.modalSubtractPercent.hide();
            this.$refs.modalPorcentajes.show()
        },
        storeSubtractPercent() {
            this.v$.formSubtractPercent.$touch();
            if (this.v$.formSubtractPercent.$invalid) {
                return;
            }

            const data = this.formSubtractPercent.data()

            const data_porcentaje = parseFloat(data.porcentaje)

            if ((100 - this.total_porcentajes).toFixed(2) * -1 < data_porcentaje) {
                alertWarning(`El porcentaje a restar debe estar entre 0.01 y ${(100 - this.total_porcentajes).toFixed(
                                                2
                                            )*-1} y sin contener (,).`)
                return;
            }

            if (this.total_subtract_percent <= 0) {
                alertWarning('El total no debe ser 0 o un numero negativo')
                return;
            }

            this.subtractPorcentaje(data.alternativa, data.porcentaje)

            if (this.total_porcentajes == 100 || this.suma_porcentajes != 0) {
                this.local_edit_porcentaje = false
            }

            this.$refs.modalSubtractPercent.hide();
            this.$refs.modalPorcentajes.show()
            this.formSubtractPercent.reset();
            this.formPorcentaje.reset()
        },

        // EDITAR PORCENTAJE MANUALMENTE
        
        createManualPercentEdit(porcentaje, index) {
            this.formPorcentaje.reset();
            this.$refs.modalPorcentajes.hide()
            this.$refs.modalManualPercentEdit.show();

            this.formPorcentaje.id = porcentaje.id;
            this.formPorcentaje.porcentaje = porcentaje.porcentaje;
            this.formPorcentaje.descripcion = porcentaje.descripcion
            this.formPorcentaje.index = index
        },
        closeManualPercentEdit() {
            this.$refs.modalManualPercentEdit.hide();
            this.$refs.modalPorcentajes.show()
            this.formPorcentaje.reset()
        },
        storeManualPercentEdit() {
            this.v$.formPorcentaje.$touch();
            if (this.v$.formPorcentaje.$invalid) {
                return;
            }

            this.formPorcentajes.porcentajes[this.formPorcentajes.porcentajes.length - 1].porcentaje = parseFloat(this.formPorcentaje.porcentaje).toFixed(2);
            this.local_edit_porcentaje = true

            this.$refs.modalManualPercentEdit.hide();
            this.formPorcentaje.reset();

            this.$refs.modalPorcentajes.show()
            this.formPorcentaje.reset()

            // this.asignarPorcentajes(this.id_pregunta)
            this.calcularPorcentaje(this.id_pregunta);
            this.validarPorcentajes(this.id_pregunta);
            this.validarUltimoPorcentaje(this.id_pregunta);
            this.disablePercentValidation(this.id_pregunta);
            this.activePlusPercentValidation(this.id_pregunta);
            this.activeSubtractPercentValidation(this.id_pregunta);
            this.activeEditPercentValidation(this.id_pregunta)
        },

        // EDITAR DESCRIPCION
        createDescripcion(value) {
            this.formPorcentaje.reset();

            this.formPorcentaje.id = value.id
            this.formPorcentaje.porcentaje = value.porcentaje
            this.formPorcentaje.descripcion = value.descripcion
            this.formPorcentaje.index = value.index

            const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta

            if (id_tipo_pregunta == 1) {
                this.$refs.modalEditSimpleAlternativas.hide()
            } else if (id_tipo_pregunta == 2) {
                this.$refs.modalEditAlternativas.hide()
            }
            
            this.$refs.modalDescripcion.show()
        },
        closeDescripcion() {
            this.$refs.modalDescripcion.hide()
            
            const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta

            if (id_tipo_pregunta == 1) {
                this.$refs.modalEditSimpleAlternativas.show()
            } else if (id_tipo_pregunta == 2) {
                this.$refs.modalEditAlternativas.show()
            }
        },
        storeDescripcion() {
            this.v$.formPorcentaje.descripcion.$touch();
            if (this.v$.formPorcentaje.descripcion.$invalid) {
                return;
            }

            const data = this.formPorcentaje.data()

            const new_data = {
                id: data.id,
                descripcion: data.descripcion
            }

            console.log('new_data', new_data)

            this.formPorcentaje.processing = true;

            this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/update-descripcion/${this.id_pregunta}`
                    ),
                    data: new_data
                })
                    .then((res) => {
                        alertSuccess("Descripcion actualizada correctamente");
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        // edita la descripcion en el array
                        
                        const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta
                        
                        if (id_tipo_pregunta == 1) {
                            const index = this.formPregunta.simple_selection_alternativas.findIndex((alt) => alt.id == new_data.id)
                            this.formPregunta.simple_selection_alternativas[index].descripcion = new_data.descripcion

                            this.$refs.modalDescripcion.hide();
                            this.$refs.modalEditSimpleAlternativas.show()
                        } else if (id_tipo_pregunta == 2) {
                            const index = this.formPregunta.multi_selection_alternativas.findIndex((alt) => alt.id == new_data.id)
                            this.formPregunta.multi_selection_alternativas[index].descripcion = new_data.descripcion

                            this.$refs.modalDescripcion.hide();
                            this.$refs.modalEditAlternativas.show()
                        }

                        this.formPorcentaje.reset();
                    })
                    .catch((err) => {
                        // alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPorcentaje.processing = false;
                    });
        },

        // EDITAR ALTERNATIVAS SIMPLES

        createEditSimpleAlternativas(pregunta, index) {
            this.formPregunta.reset();
            
            this.index_alternativa = index
            this.id_pregunta = pregunta.id
            this.formPregunta.id_recurso_examen = pregunta.id_recurso_examen
            this.formPregunta.id_tipo_pregunta = pregunta.id_tipo_pregunta
            this.formPregunta.descripcion = pregunta.descripcion
            this.formPregunta.simple_selection_alternativas = pregunta.alternativas

            this.$refs.modalEditSimpleAlternativas.show();

            this.simpleValidation(pregunta.id)
        },
        closeEditSimpleAlternativas() {
            this.$refs.modalEditSimpleAlternativas.hide();
        },

        // EDITAR ALTERNATIVAS TRUE Y FALSE

        createEditTrueFalseAlternativas(pregunta, index) {
            console.log('pregunta', pregunta)
            this.formPregunta.reset();
            
            this.index_alternativa = index
            this.id_pregunta = pregunta.id
            this.formPregunta.id_recurso_examen = pregunta.id_recurso_examen
            this.formPregunta.id_tipo_pregunta = pregunta.id_tipo_pregunta
            this.formPregunta.descripcion = pregunta.descripcion
            this.formPregunta.simple_selection_alternativas = pregunta.alternativas

            this.$refs.modalEditTrueFalseAlternativas.show();

            this.simpleValidation(pregunta.id)
        },
        closeEditTrueFalseAlternativas() {
            this.$refs.modalEditTrueFalseAlternativas.hide();
        },

        // EDITAR PREGUNTA

        createEditPregunta(value, pregunta, index) {
            this.formPregunta.reset();
            
            this.index_alternativa = index
            this.id_pregunta = pregunta.id
            this.formPregunta.id_recurso_examen = pregunta.id_recurso_examen
            this.formPregunta.id_tipo_pregunta = pregunta.id_tipo_pregunta
            this.formPregunta.descripcion = pregunta.descripcion
            this.formPregunta.simple_selection_alternativas = pregunta.alternativas

            this.$refs.modalEditPregunta.show();
        },

        closeEditPregunta() {
            this.$refs.modalEditPregunta.hide()
        },
        storeEditPregunta() {
            this.v$.formPregunta.descripcion.$touch();
            if (this.v$.formPregunta.descripcion.$invalid) {
                return;
            }

            const data = this.formPregunta.data()

            const new_data = {
                id: this.id_pregunta,
                descripcion: data.descripcion
            }


            this.formPregunta.processing = true;

            this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-examenes-preguntas/update-question`
                    ),
                    data: new_data
                })
                    .then((res) => {
                        alertSuccess("Pregunta actualizada correctamente");
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        this.closeEditPregunta()
                        this.formPregunta.reset();
                    })
                    .catch((err) => {
                        // alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
        },

        // CALCULOS

        deleteAlternativa(value) {
            this.formPregunta.processing = true;

            this.$http({
                    method: "DELETE",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/destroy/${this.id_pregunta}/alternativa/${value}`
                    ),
                })
                    .then((res) => {
                        alertSuccess("Alternativa eliminada correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta

                        if (id_tipo_pregunta == 1) {
                            this.formPregunta.simple_selection_alternativas = this.formPregunta.simple_selection_alternativas.filter((alt) => alt.id != value)
                        } else if (id_tipo_pregunta == 2) {
                            this.formPregunta.multi_selection_alternativas = this.formPregunta.multi_selection_alternativas.filter((alt) => alt.id != value)
                        }
                    })
                    .catch((err) => {
                        alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
        },
        checkAlternativa(value) {
            this.formPregunta.processing = true;

            this.$http({
                    method: "PUT",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/update-check/${this.id_pregunta}`
                    ),
                    data: value
                })
                    .then((res) => {
                        alertSuccess("Alternativa actualizada correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta

                        if (id_tipo_pregunta == 1) {

                            // Actualiza el array de la siguiente manera, el id que estoy enviando en value es el id de la alternativa correcta, el resto de alternativas actualiza su correcta en 'N' y su porcentaje en '0.00'

                            this.formPregunta.simple_selection_alternativas.forEach((alt) => {
                                if (alt.id == value.id) {
                                    alt.correcta = value.value
                                } else {
                                    alt.correcta = 'N'
                                    alt.porcentaje = '0.00'
                                }
                            })

                        } else if (id_tipo_pregunta == 2) {
                            const index = this.formPregunta.multi_selection_alternativas.findIndex((alt) => alt.id == value.id)
                            this.formPregunta.multi_selection_alternativas[index].correcta = value.value
        
                            if (value.value == "N") {
                                this.formPregunta.multi_selection_alternativas[index].porcentaje = '0.00'
                            }
                        }

                        
                    })
                    .catch((err) => {
                        alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
        },
        createAlternativa(value) {
            this.formPregunta.processing = true;

            const data = {
                id_recurso_examen_pregunta: this.id_pregunta,
                ...value,
            }

            this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/store/${this.id_pregunta}`
                    ),
                    data: data
                })
                    .then((res) => {
                        alertSuccess("Alternativa creada correctamente");
                        this.closePregunta();
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        const newId = res.data.recurso_pregunta_alternativa.id

                        const id_tipo_pregunta = this.formPregunta.id_tipo_pregunta

                        if (id_tipo_pregunta == 1) {
                            this.formPregunta.simple_selection_alternativas.push({
                                id: newId,
                                ...value
                            })
                        } else if (id_tipo_pregunta == 2) {
                            this.formPregunta.multi_selection_alternativas.push({
                                id: newId,
                                ...value
                            })
                        }
                    })
                    .catch((err) => {
                        alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
        },

        autoCalculatePorcentajes() {
            confirm(
                {
                    text: "¿Desea calcular el porcentaje equitativamente?",
                    confirmButtonText: "Aceptar",
                },
                () => {
                    const numAlternativas = this.formPorcentajes.porcentajes.length;
                    const porcentajeBase = (100.0 / numAlternativas).toFixed(2); // Puntaje base por pregunta

                    // Asigna porcentajes a todas las preguntas excepto la última
                    this.formPorcentajes.porcentajes.forEach((porcentaje, index) => {
                        if (index < numAlternativas - 1) {
                            porcentaje.porcentaje = porcentajeBase.toString(); // Convertir a string antes de asignar
                        }
                    });

                    // Ajusta el puntaje de la última pregunta para que la suma sea 20.00
                    this.ajustarUltimoPorcentaje();

                    // Recalcula el total y valida los puntajes
                    this.calcularPorcentaje(this.id_pregunta);
                    this.validarPorcentajes(this.id_pregunta);
                    this.disablePercentValidation(this.id_pregunta)
                    this.activePlusPercentValidation(this.id_pregunta)
                    this.activeSubtractPercentValidation(this.id_pregunta)
                    this.activeEditPercentValidation(this.id_pregunta)
                    this.local_edit_porcentaje = false
                }
            );
        },
        calcularPorcentaje(id_pregunta) {
            if (id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            this.total_porcentajes = this.formPorcentajes?.porcentajes.reduce(
                (total, item) => {
                    // Convertimos el puntaje a número con 2 decimales antes de sumarlo
                    const porcentaje = parseFloat(
                        parseFloat(item.porcentaje || 0).toFixed(2)
                    );
                    return total + porcentaje;
                },
                0
            );

            // Asegurar que el total también tenga el formato 0.00
            this.total_porcentajes = parseFloat(this.total_porcentajes.toFixed(2));
        },  
        ajustarUltimoPorcentaje() {
            // Verifica si todos los puntajes anteriores han sido asignados
            const numAlternativas = this.formPorcentajes.porcentajes.length;
            const porcentajesValidos = this.formPorcentajes.porcentajes
                .slice(0, numAlternativas - 1)
                .every(
                    (porcentaje) =>
                        !isNaN(parseFloat(porcentaje.porcentaje)) &&
                        porcentaje.porcentaje !== null
                );

            if (!porcentajesValidos) {
                return;
            }

            // Calcula la suma de los puntajes asignados (excepto el último)
            let totalAsignado = this.formPorcentajes.porcentajes
                .slice(0, numAlternativas - 1)
                .reduce(
                    (sum, porcentaje) => sum + (parseFloat(porcentaje.porcentaje) || 0),
                    0
                );

            // Ajusta el último puntaje para que la suma total sea 20.00
            const porcentajeFinal = (100.0 - totalAsignado).toFixed(2);

            // Verifica si el puntajeFinal es negativo o cero antes de asignarlo
            if (parseFloat(porcentajeFinal) < 0) {
                return;
            }

            if (parseFloat(porcentajeFinal) == 0) {
                return;
            }

            // Convertir a string antes de asignar
            this.formPorcentajes.porcentajes[numAlternativas - 1].porcentaje =
                porcentajeFinal.toString();
        },
        validarPorcentajes(id_pregunta) {
            if (id_pregunta == 0) return;

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            this.validar_porcentajes = this.formPorcentajes.porcentajes.some(
                (porcentaje) => {
                    return parseFloat(porcentaje.porcentaje) == 0.0;
                }
            );

            if (this.validar_porcentajes) {
                // Resetea el valor del último puntaje
                const numAlternativas = this.formPorcentajes.porcentajes.length;
                this.formPorcentajes.porcentajes[numAlternativas - 1].porcentaje = "0.00";
            }

            this.calcularPorcentaje(this.id_pregunta);
        },
        validarUltimoPorcentaje(id_pregunta) {
            if (id_pregunta == 0) return 

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            const porcentajes = this.formPorcentajes.porcentajes;

            // Extraemos solo los valores de puntaje
            const porcentajesValores = porcentajes.map((p) => p.porcentaje);

            // Filtramos los elementos con puntaje "0.00"
            const indicesCero = porcentajesValores
                .map((porcentaje, index) => (porcentaje === "0.00" ? index : -1))
                .filter((index) => index !== -1);


            this.validar_ultimo_porcentaje = indicesCero.length <= 1; // Retorna false si hay más de un "0.00", true en otros casos
        },
        addPorcentaje(porcentaje, add) {
            // Convertir `add` a número
            const addNum = parseFloat(add);

            // Busca el índice del puntaje en el array
            const index = this.formPorcentajes.porcentajes.findIndex(
                (p) => p.id === porcentaje.id
            );

            if (index !== -1) {
                // Convertir el puntaje existente a número
                const porcentajeExistente = parseFloat(this.formPorcentajes.porcentajes[index].porcentaje);
                if (isNaN(porcentajeExistente)) {
                    alertWarning("El porcentaje existente no es un número válido.");
                    return;
                }

                // Sumar el valor a agregar al puntaje existente
                const nuevoPorcentaje = porcentajeExistente + addNum;

                // Formatear a 2 decimales y actualizar el puntaje
                this.formPorcentajes.porcentajes[index].porcentaje = nuevoPorcentaje.toFixed(2);
            }

            // Llamar a las funciones adicionales
            this.calcularPorcentaje(this.id_pregunta);
            this.validarPorcentajes(this.id_pregunta);
            this.validarUltimoPorcentaje(this.id_pregunta);
            this.disablePercentValidation(this.id_pregunta);
            this.activePlusPercentValidation(this.id_pregunta);
            this.activeSubtractPercentValidation(this.id_pregunta);
            this.activeEditPercentValidation(this.id_pregunta)
        },
        subtractPorcentaje(porcentaje, subtract) {
            // Convertir `subtract` a número
            const subtractNum = parseFloat(subtract);

            // Busca el índice del puntaje en el array
            const index = this.formPorcentajes.porcentajes.findIndex(
                (p) => p.id === porcentaje.id
            );

            if (index !== -1) {
                // Convertir el puntaje existente a número
                const porcentajeExistente = parseFloat(this.formPorcentajes.porcentajes[index].porcentaje);
                if (isNaN(porcentajeExistente)) {
                    alertWarning("El porcentaje existente no es un número válido.");
                    return;
                }

                // Restar el valor a restar del puntaje existente
                const nuevoPorcentaje = porcentajeExistente - subtractNum;

                // Formatear a 2 decimales y actualizar el puntaje
                this.formPorcentajes.porcentajes[index].porcentaje = nuevoPorcentaje.toFixed(2);
            }

            // Llamar a las funciones adicionales
            this.calcularPorcentaje(this.id_pregunta);
            this.validarPorcentajes(this.id_pregunta);
            this.validarUltimoPorcentaje(this.id_pregunta);
            this.disablePercentValidation(this.id_pregunta);
            this.activePlusPercentValidation(this.id_pregunta);
            this.activeSubtractPercentValidation(this.id_pregunta);
            this.activeEditPercentValidation(this.id_pregunta)
        },
        asignarTodoPorcentaje(add, value) {
            if (this.asignar_todo_porcentaje) {
                this.formAddPercent.porcentaje = add
                this.totalPlusPercent(add,value)
            } else {
                this.formAddPercent.porcentaje = ""
                this.total_plus_percent = ""
            }
        },
        asignarTodoSubtractPorcentaje(subtract, value) {
            if (this.asignar_todo_porcentaje) {
                this.formSubtractPercent.porcentaje = subtract * -1
                this.totalSubtractPercent(subtract*-1,value)
            } else {
                this.formSubtractPercent.porcentaje = ""
                this.total_subtract_percent = ""
            }
        },
        totalPlusPercent(add, value) {
            this.v$.formAddPercent.$touch();
            if (this.v$.formAddPercent.$invalid) {
                return;
            }
            // Convertir add y value a números antes de redondearlos
            const addNum = parseFloat(add);
            const valueNum = parseFloat(value);

            // Verificar si los valores son números válidos
            if (isNaN(addNum) || isNaN(valueNum)) {
                console.error("Los valores proporcionados no son números válidos.");
                return;
            }

            // Sumar los valores y redondear el resultado a 2 decimales
            this.total_plus_percent = (addNum + valueNum).toFixed(2);
        },
        totalSubtractPercent(subtract, value) {
            this.v$.formSubtractPercent.$touch();
            if (this.v$.formSubtractPercent.$invalid) {
                return;
            }
            // Convertir add y value a números antes de redondearlos
            const subtractNum = parseFloat(subtract);
            const valueNum = parseFloat(value);

            // Verificar si los valores son números válidos
            if (isNaN(subtractNum) || isNaN(valueNum)) {
                console.error("Los valores proporcionados no son números válidos.");
                return;
            }

            // Sumar los valores y redondear el resultado a 2 decimales
            this.total_subtract_percent = (valueNum - subtractNum).toFixed(2);
        },

        autoCalculatePuntajes() {
            confirm(
                {
                    text: "¿Desea calcular el puntaje equitativamente?",
                    confirmButtonText: "Aceptar",
                },
                () => {
                    const numPreguntas = this.formPuntajes.puntajes.length;
                    const puntajeBase = (20.0 / numPreguntas).toFixed(2); // Puntaje base por pregunta

                    // Asigna puntajes a todas las preguntas excepto la última
                    this.formPuntajes.puntajes.forEach((puntaje, index) => {
                        if (index < numPreguntas - 1) {
                            puntaje.puntaje = puntajeBase.toString(); // Convertir a string antes de asignar
                        }
                    });

                    // Ajusta el puntaje de la última pregunta para que la suma sea 20.00
                    this.ajustarUltimoPuntaje();

                    // Recalcula el total y valida los puntajes
                    this.calcularPuntaje();
                    this.validarPuntajes();
                    this.disableScoreValidation()
                    this.activePlusValidation()
                    this.activeSubtractValidation()
                    this.activeEditValidation()
                    this.local_edit = false
                }
            );
        },
        calcularPuntaje() {
            this.total_puntajes = this.formPuntajes.puntajes.reduce(
                (total, item) => {
                    // Convertimos el puntaje a número con 2 decimales antes de sumarlo
                    const puntaje = parseFloat(
                        parseFloat(item.puntaje || 0).toFixed(2)
                    );
                    return total + puntaje;
                },
                0
            );

            // Asegurar que el total también tenga el formato 0.00
            this.total_puntajes = parseFloat(this.total_puntajes.toFixed(2));
        },
        asignarPuntajes() {
            this.formPuntajes.puntajes = this.recurso_examen.preguntas.map(
                (pregunta) => {
                    return {
                        id: pregunta.id,
                        descripcion: pregunta.descripcion,
                        puntaje: pregunta.puntaje,
                    };
                }
            );
        },
        ajustarUltimoPuntaje() {
            // Verifica si todos los puntajes anteriores han sido asignados
            const numPreguntas = this.formPuntajes.puntajes.length;
            const puntajesValidos = this.formPuntajes.puntajes
                .slice(0, numPreguntas - 1)
                .every(
                    (puntaje) =>
                        !isNaN(parseFloat(puntaje.puntaje)) &&
                        puntaje.puntaje !== null
                );

            if (!puntajesValidos) {
                return;
            }

            // Calcula la suma de los puntajes asignados (excepto el último)
            let totalAsignado = this.formPuntajes.puntajes
                .slice(0, numPreguntas - 1)
                .reduce(
                    (sum, puntaje) => sum + (parseFloat(puntaje.puntaje) || 0),
                    0
                );

            // Ajusta el último puntaje para que la suma total sea 20.00
            const puntajeFinal = (20.0 - totalAsignado).toFixed(2);

            // Verifica si el puntajeFinal es negativo o cero antes de asignarlo
            if (parseFloat(puntajeFinal) < 0) {
                return;
            }

            if (parseFloat(puntajeFinal) == 0) {
                return;
            }

            // Convertir a string antes de asignar
            this.formPuntajes.puntajes[numPreguntas - 1].puntaje =
                puntajeFinal.toString();
        },
        validarPuntajes() {
            this.validar_puntajes = this.formPuntajes.puntajes.some(
                (puntaje) => {
                    return parseFloat(puntaje.puntaje) === 0.0;
                }
            );

            if (this.validar_puntajes) {
                // Resetea el valor del último puntaje
                const numPreguntas = this.formPuntajes.puntajes.length;
                this.formPuntajes.puntajes[numPreguntas - 1].puntaje = "0.00";
            }

            this.calcularPuntaje();
        },
        validarUltimoPuntaje() {
            const puntajes = this.formPuntajes.puntajes;

            // Extraemos solo los valores de puntaje
            const puntajesValores = puntajes.map((p) => p.puntaje);

            // Filtramos los elementos con puntaje "0.00"
            const indicesCero = puntajesValores
                .map((puntaje, index) => (puntaje === "0.00" ? index : -1))
                .filter((index) => index !== -1);


            this.validar_ultimo_puntaje = indicesCero.length <= 1; // Retorna false si hay más de un "0.00", true en otros casos

        },
        addPuntaje(puntaje, add) {

            // Convertir `add` a número
            const addNum = parseFloat(add);

            // Busca el índice del puntaje en el array
            const index = this.formPuntajes.puntajes.findIndex(
                (p) => p.id === puntaje.id
            );

            if (index !== -1) {
                // Convertir el puntaje existente a número
                const puntajeExistente = parseFloat(this.formPuntajes.puntajes[index].puntaje);
                if (isNaN(puntajeExistente)) {
                    alertWarning("El puntaje existente no es un número válido.");
                    return;
                }

                // Sumar el valor a agregar al puntaje existente
                const nuevoPuntaje = puntajeExistente + addNum;

                // Formatear a 2 decimales y actualizar el puntaje
                this.formPuntajes.puntajes[index].puntaje = nuevoPuntaje.toFixed(2);
            }

            // Llamar a las funciones adicionales
            this.calcularPuntaje();
            this.validarPuntajes();
            this.validarUltimoPuntaje();
            this.disableScoreValidation();
            this.activePlusValidation();
            this.activeSubtractValidation();
            this.activeEditValidation()
        },
        subtractPuntaje(puntaje, subtract) {
            // Convertir `subtract` a número
            const subtractNum = parseFloat(subtract);

            // Busca el índice del puntaje en el array
            const index = this.formPuntajes.puntajes.findIndex(
                (p) => p.id === puntaje.id
            );

            if (index !== -1) {
                // Convertir el puntaje existente a número
                const puntajeExistente = parseFloat(this.formPuntajes.puntajes[index].puntaje);
                if (isNaN(puntajeExistente)) {
                    alertWarning("El puntaje existente no es un número válido.");
                    return;
                }

                // Restar el valor a restar del puntaje existente
                const nuevoPuntaje = puntajeExistente - subtractNum;

                // Formatear a 2 decimales y actualizar el puntaje
                this.formPuntajes.puntajes[index].puntaje = nuevoPuntaje.toFixed(2);
            }

            // Llamar a las funciones adicionales
            this.calcularPuntaje();
            this.validarPuntajes();
            this.validarUltimoPuntaje();
            this.disableScoreValidation();
            this.activePlusValidation();
            this.activeSubtractValidation();
            this.activeEditValidation()
        },
        asignarTodo(add, value) {
            if (this.asignar_todo) {
                this.formAddScore.puntaje = add
                this.totalPlus(add,value)
            } else {
                this.formAddScore.puntaje = ""
                this.total_plus = ""
            }
        },
        asignarTodoSubtract(subtract, value) {
            if (this.asignar_todo) {
                this.formSubtractScore.puntaje = subtract * -1
                this.totalSubtract(subtract*-1,value)
            } else {
                this.formSubtractScore.puntaje = ""
                this.total_subtract = ""
            }
        },
        totalPlus(add, value) {
            this.v$.formAddScore.$touch();
            if (this.v$.formAddScore.$invalid) {
                return;
            }
            // Convertir add y value a números antes de redondearlos
            const addNum = parseFloat(add);
            const valueNum = parseFloat(value);

            // Verificar si los valores son números válidos
            if (isNaN(addNum) || isNaN(valueNum)) {
                console.error("Los valores proporcionados no son números válidos.");
                return;
            }

            // Sumar los valores y redondear el resultado a 2 decimales
            this.total_plus = (addNum + valueNum).toFixed(2);
        },
        totalSubtract(subtract, value) {
            this.v$.formSubtractScore.$touch();
            if (this.v$.formSubtractScore.$invalid) {
                return;
            }
            // Convertir add y value a números antes de redondearlos
            const subtractNum = parseFloat(subtract);
            const valueNum = parseFloat(value);

            // Verificar si los valores son números válidos
            if (isNaN(subtractNum) || isNaN(valueNum)) {
                console.error("Los valores proporcionados no son números válidos.");
                return;
            }

            // Sumar los valores y redondear el resultado a 2 decimales
            this.total_subtract = (valueNum - subtractNum).toFixed(2);
        },
        resetInput(form, field) {
            form[field] = "";
            this.total_plus = "";
            this.total_subtract = "";
        },

        asignarPorcentajes(id_pregunta) {
            if (id_pregunta == 0) return

            // Obtener la pregunta por el id

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            this.formPorcentajes.porcentajes = pregunta?.alternativas.filter((alternativa) => alternativa.correcta == 'S').map(
                (alternativa) => {
                    return {
                        id: alternativa.id,
                        descripcion: alternativa.descripcion,
                        porcentaje: alternativa.porcentaje,
                    };
                }
            );

            console.log('porcentajes', this.formPorcentajes.porcentajes)
        },
        deshabilitarExamen() {
            const id_seccion_recurso = this.recurso_examen.seccion_recurso.id;

            confirm(
                {
                    text: "¿Deseas deshabilitar el examen?",
                    confirmButtonText: "Aceptar",
                },
                () => {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `gestion-cursos/virtual-classroom/recursos-examenes/disable-exam/${id_seccion_recurso}`
                        ),
                    })
                        .then((res) => {
                            alertSuccess(res.data.message);
                            this.$inertia.reload({ only: ["recurso_examen"] });
                        })
                        .catch((err) => {
                            alertError(err.response.data.error);
                        });
                }
            );
        },
        habilitarExamen() {
            const id_recurso_examen = this.recurso_examen.id;

            confirm(
                {
                    text: "¿Deseas habilitar el examen?",
                    confirmButtonText: "Aceptar",
                },
                () => {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `gestion-cursos/virtual-classroom/recursos-examenes/enable-exam/${id_recurso_examen}`
                        ),
                    })
                        .then((res) => {
                            alertSuccess(res.data.message);
                            this.$inertia.reload({ only: ["recurso_examen"] });
                        })
                        .catch((err) => {
                            alertError(err.response.data.error);
                        });
                }
            );
        },

        changeCorrect() {
            const id_pregunta = this.id_pregunta

            const data = {
                id_pregunta: id_pregunta,
            }

            this.formPregunta.processing = true;

            this.$http({
                    method: "POST",
                    url: this.routeTo(
                        `${this.urlRoute}/virtual-classroom/recursos-preguntas-alternativas/change-correct`
                    ),
                    data: data
                })
                    .then((res) => {
                        alertSuccess("Alternativa actualizada correctamente");
                        this.$inertia.reload({
                            only: [
                                "recurso_examen",
                                "verify_alert",
                            ],
                        });

                        this.formPregunta.simple_selection_alternativas.forEach((alt) => {
                                if (alt.correcta == 'S') {
                                    alt.correcta = 'N'
                                    alt.porcentaje = '0.00'
                                } else {
                                    alt.correcta = 'S'
                                    alt.porcentaje = '1.00'
                                }
                            })
                    })
                    .catch((err) => {
                        alertError(err.response.data.error);
                    })
                    .finally(() => {
                        this.formPregunta.processing = false;
                    });
            
        },
        handleTipoPregunta() {
            console.log('llego')
            this.formPregunta.multi_selection_alternativas = []
            this.formPregunta.simple_selection_alternativas = []
        },
        deleteQuestion(id) {
            confirm(
                    {
                        text: "¿Desea eliminar la pregunta?",
                    },
                    () => {
                        this.$http({
                            method: "DELETE",
                            url: this.routeTo(
                                `gestion-cursos/virtual-classroom/recursos-examenes-preguntas/destroy/${id}`
                            ),
                        })
                            .then((res) => {
                                alertSuccess("Eliminado Correctamente");
                                this.$inertia.reload({
                                    only: [
                                        "recurso_examen",
                                        "verify_alert",
                                        "suma_puntaje",
                                    ],
                                });
                            })
                            .catch((err) => {
                                alertError(err.response.data.error);
                            });
                    }
                );

            this.calcularPorcentaje(this.id_pregunta);
            this.validarPorcentajes(this.id_pregunta);
            this.validarUltimoPorcentaje(this.id_pregunta);
            this.disablePercentValidation(this.id_pregunta);
            this.activePlusPercentValidation(this.id_pregunta);
            this.activeSubtractPercentValidation(this.id_pregunta);
            this.activeEditPercentValidation(this.id_pregunta)
        },

        // VALIDACIONES PUNTAJES

        alertValidation() {
            this.alert_validation =
                (this.total_puntajes != 20.0 || this.verify_alert) &&
                this.formPuntajes.puntajes.length != 0;
        },
        disableScoreValidation() {
            const puntajes_size = this.formPuntajes.puntajes.length
            const ultimo_puntaje = this.formPuntajes.puntajes[puntajes_size - 1]?.puntaje

            const a = ultimo_puntaje == '0.00'
            const b = this.total_puntajes > 20
            const c = this.validar_ultimo_puntaje
            const d = this.total_puntajes < 20
            const e = this.total_puntajes != 0
            const f = puntajes_size != 1
            const g = this.local_edit

            if (this.suma_puntaje != 0) {
                this.disable_score_validation = c && ((b || (a && !d)) || (d && e && f))
            } else {
                this.disable_score_validation = c && ((b || (a && !d)) || (d && e && f && g))
            }

        },
        activePlusValidation() {
            const puntajes_size = this.formPuntajes.puntajes.length

            const a = this.total_puntajes < 20
            const b = this.total_puntajes != 0
            const c = this.validar_ultimo_puntaje
            const d = puntajes_size != 1
            const e = this.local_edit

            if (this.suma_puntaje != 0) {
                this.active_plus_validation = a && b && c && d;
            } else {
                this.active_plus_validation = a && b && c && d && e;
            }
        },
        activeSubtractValidation() {
            const puntajes_size = this.formPuntajes.puntajes.length
            const ultimo_puntaje = this.formPuntajes.puntajes[puntajes_size - 1]?.puntaje

            const a = ultimo_puntaje != '0.00'
            const b = this.total_puntajes > 20
            const c = this.validar_ultimo_puntaje

            this.active_subtract_validation = a && b && c;
        },
        activeEditValidation() {
            const puntajes_size = this.formPuntajes.puntajes.length
            const ultimo_puntaje = this.formPuntajes.puntajes[puntajes_size - 1]?.puntaje

            const a = ultimo_puntaje != '0.00'
            const b = this.total_puntajes > 20
            const c = this.validar_ultimo_puntaje
            const d = this.total_puntajes < 20
            const e = this.total_puntajes != 0
            const f = puntajes_size != 1
            const g = this.local_edit

            if (this.suma_puntaje != 0) {
                this.active_edit_validation = (!a || !b) && (!d || !e || !f) && c
            } else {
                this.active_edit_validation = (!a || !b) && (!d || !e || !g) && c && f
            }
        },

        // VALIDACIONES PORCENTAJES

        alertValidationPercent(id_pregunta) {
            if (id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            this.alert_validation_percent =
                (this.total_porcentajes != 100.0 || this.estado_incompleto) &&
                this.formPorcentajes.porcentajes.length != 0;
        },
        disablePercentValidation(id_pregunta) {
            if (id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            const porcentajes_size = this.formPorcentajes.porcentajes.length
            const ultimo_porcentaje = this.formPorcentajes.porcentajes[porcentajes_size - 1]?.porcentaje

            const a = ultimo_porcentaje == '0.00'
            const b = this.total_porcentajes > 100
            const c = this.validar_ultimo_porcentaje
            const d = this.total_porcentajes < 100
            const e = this.total_porcentajes != 0
            const f = porcentajes_size != 1
            const g = this.local_edit_porcentaje

            if (this.suma_porcentajes != 0) {
                this.disable_percent_validation = c && ((b || (a && !d)) || (d && e && f))
            } else {
                this.disable_percent_validation = c && ((b || (a && !d)) || (d && e && f && g))
            }

        },
        activePlusPercentValidation(id_pregunta) {
            if (id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            const porcentajes_size = this.formPorcentajes.porcentajes.length

            const a = this.total_porcentajes < 100
            const b = this.total_porcentajes != 0
            const c = this.validar_ultimo_porcentaje
            const d = porcentajes_size != 1
            const e = this.local_edit_porcentaje

            if (this.suma_porcentajes != 0) {
                this.active_plus_percent_validation = a && b && c && d;
            } else {
                this.active_plus_percent_validation = a && b && c && d && e;
            }
        },
        activeSubtractPercentValidation(id_pregunta) {
            if(id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            const porcentajes_size = this.formPorcentajes.porcentajes.length
            const ultimo_porcentaje = this.formPorcentajes.porcentajes[porcentajes_size - 1]?.porcentaje

            const a = ultimo_porcentaje != '0.00'
            const b = this.total_porcentajes > 100
            const c = this.validar_ultimo_porcentaje

            this.active_subtract_percent_validation = a && b && c;
        },
        activeEditPercentValidation(id_pregunta) {
            if (id_pregunta == 0) return;

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            if (!pregunta) {
                return;
            }

            const porcentajes_size = this.formPorcentajes.porcentajes.length
            const ultimo_porcentaje = this.formPorcentajes.porcentajes[porcentajes_size - 1]?.porcentaje

            const a = ultimo_porcentaje != '0.00'
            const b = this.total_porcentajes > 100
            const c = this.validar_ultimo_porcentaje
            const d = this.total_porcentajes < 100
            const e = this.total_porcentajes != 0
            const f = porcentajes_size != 1
            const g = this.local_edit_porcentaje

            if (this.suma_puntaje != 0) {
                this.active_edit_percent_validation = (!a || !b) && (!d || !e || !f) && c
            } else {
                this.active_edit_percent_validation = (!a || !b) && (!d || !e || !g) && c && f
            }
        },

        // VALIDACIONES SIMPLES

        simpleValidation(id_pregunta) {
            if (id_pregunta == 0 || this.formPregunta.id_tipo_pregunta != 1) return

            this.validar_cantidad_alternativas = this.formPregunta.simple_selection_alternativas.length < 2
            this.validar_correctas = this.formPregunta.simple_selection_alternativas.filter((alternativa) => alternativa.correcta == 'S').length != 1
        },

        // OTRAS VALIDACIONES

        mostrarValidation() {
            this.mostrar_validation =
                this.recurso_examen.seccion_recurso.mostrar == "I";
        },
        updateEstadoIncompleto(id_pregunta) {
            if (this.id_pregunta == 0) return

            const pregunta = this.recurso_examen.preguntas.find(
                (pregunta) => pregunta.id === id_pregunta
            );

            this.estado_incompleto = pregunta?.incompleto
        },
        estadoIncompletoInit() {
            this.estado_incompleto = this.recurso_examen.preguntas.some(
                (pregunta) => pregunta?.incompleto
            );
        }
    },
    computed: {
        errors() {
            return this.$page.props.errors.recursosExamenesPreguntas;
        },
    },
    mounted() {
        this.asignarPuntajes();
        this.calcularPuntaje();
        this.validarPuntajes();
        this.validarUltimoPuntaje();

        this.asignarPorcentajes(this.id_pregunta);
        this.calcularPorcentaje(this.id_pregunta);
        this.validarPorcentajes(this.id_pregunta);
        this.validarUltimoPorcentaje(this.id_pregunta);

        this.updateEstadoIncompleto(this.id_pregunta)
        this.simpleValidation(this.id_pregunta)
        
        this.estadoIncompletoInit()
    },
    setup() {
        const v$ = useVuelidate();

        return {
            v$,
        };
    },
    validations() {
        return {
            formPuntaje: {
                puntaje: {
                    qualificationsValidator: qualificationsValidator(20),
                },
            },
            formAddScore: {
                puntaje: {
                    qualificationsValidator: qualificationsValidator2(20)
                }
            },
            formSubtractScore: {
                puntaje: {
                    qualificationsValidator: qualificationsValidator2(20)
                }
            },
            formPorcentaje: {
                porcentaje: {
                    qualificationsValidator: qualificationsValidator(100),
                },
                descripcion: descriptionValidator()
            },
            formAddPercent: {
                porcentaje: {
                    qualificationsValidator: qualificationsValidator2(20)
                }
            },
            formSubtractPercent: {
                porcentaje: {
                    qualificationsValidator: qualificationsValidator2(20)
                }
            },
            formPregunta: {
                descripcion: descriptionValidator()
            },
        };
    },
    watch: {
        "recurso_examen.preguntas": {
            handler(newPreguntas) {
                this.asignarPuntajes();
                this.calcularPuntaje();
                this.validarPuntajes();
                this.validarUltimoPuntaje();

                this.updateEstadoIncompleto(this.id_pregunta)

                this.asignarPorcentajes(this.id_pregunta);
                this.calcularPorcentaje(this.id_pregunta);
                this.validarPorcentajes(this.id_pregunta);
                this.validarUltimoPorcentaje(this.id_pregunta);

                // VALIDACIONES

                this.mostrarValidation();

                this.alertValidation();
                this.disableScoreValidation();
                this.activePlusValidation();
                this.activeSubtractValidation();
                this.activeEditValidation();

                this.local_edit = false

                this.alertValidationPercent(this.id_pregunta)
                this.disablePercentValidation(this.id_pregunta)
                this.activePlusPercentValidation(this.id_pregunta)
                this.activeSubtractPercentValidation(this.id_pregunta)
                this.activeEditPercentValidation(this.id_pregunta)

                this.local_edit_porcentaje = false;

                this.simpleValidation(this.id_pregunta)
            },
            deep: true, // Permite detectar cambios dentro del array
            immediate: true, // Se ejecuta la primera vez al montar el componente
        },
    },
};
</script>

<style scoped>
.header-exam {
    gap: 8px;

    .signal {
        padding-top: 2.8px;
    }
}
.header-options {
    display: flex;
    justify-content: space-between;
}

.btn-control {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    p {
        font-size: 12px;
    }

    button {
        position: relative;

        .alert-icon {
            position: absolute;
            top: -10px;
            left: -10px;

            border: 1px solid #b71c1c; /* Rojo oscuro para el borde */
            border-radius: 100%;
            background-color: #d32f2f; /* Rojo intenso */

            padding: 6px;

            display: flex;
            justify-content: center;
            align-items: center;

            animation: blink 2s infinite alternate; /* Alterna cada 1s */
        }
    }
}

.container-score__pregunta {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.container-score__pregunta p {
    font-size: 13px;
}

.puntos-disponibles {
    font-size: 12px;
    span {
        font-weight: bold;
    }
}

@keyframes blink {
    0% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
    50% {
        background-color: #f44336; /* Rojo más brillante (fondo) */
        border-color: #d32f2f; /* Rojo intenso (borde) */
        color: white; /* Texto blanco cuando el fondo se hace más brillante */
    }
    100% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
}

.form {
    display: flex;
    flex-direction: column;
}

.question-score-wrapper:not(:last-child) {
    border-bottom: 1px solid #ddd;
    margin-bottom: 8px; /* Espaciado opcional */
}

.calcular-puntajes-container {
    display: flex;
    gap: 6px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 8px;

    button {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }

    .calcular-btn {
        display: flex;
        align-items: center;
        cursor: pointer;

        .title {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #82868b;
            color: white;
            height: 100%;
            min-width: 80px;
            padding: 0 10px;
            border-left: 1px solid #ffffff;

            border-top-right-radius: 0.358rem;
            border-bottom-right-radius: 0.358rem;

            p {
                font-size: 12px;
            }
        }

        .title-dark {
            background-color: #4b4b4b;
        }
    }
}

.footer-add-score {
    display: flex;
    justify-content: space-between;
    
}

.title-alternativas {
    font-size: 16px;
}

.header-alternativas {
    gap: 20px;
    
    p {
        text-align: justify;
    }

    .btn-header-alternativa {
        min-width: 140px;
    }
}

.header-text-alternativa {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}
</style>
