<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <button class="btn btn-primary" @click.prevent="create">
                        <feather-icon icon="PlusIcon" class="mr-50" />
                        <span class="align-middle">Nuevo</span>
                    </button>
                    <Button
                        ref="btnEdit"
                        class="btn btn-warning"
                        @click.native.prevent="edit"
                    >
                        <feather-icon icon="EditIcon" class="mr-50" />
                        <span class="align-middle">Modificar</span>
                    </Button>
                    <Button
                        ref="btnDestroy"
                        class="btn btn-danger"
                        @click.native.prevent="destroy"
                    >
                        <feather-icon icon="Trash2Icon" class="mr-50" />
                        <span class="align-middle">Eliminar</span>
                    </Button>
                    ||
                    <Button
                        ref="btnModule"
                        class="btn btn-success"
                        @click.native.prevent="createImportStudents"
                    >
                        <feather-icon icon="FilePlusIcon" class="mr-50" />
                        <span class="align-middle">Importar</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">Administrar Estudiantes</h4>
                        </template>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
            <Modal
                ref="modalProfile"
                :loading="form.processing"
                modal="modal-lg"
            >
                <template #title>Estudiante</template>
                <form class="form form-vertical">
                    <BTabs v-model="activeTab">
                        <BTab title="DATOS PERSONALES">
                            <div class="row">
                                <div class="image col-md-12 col-lg-12">
                                    <img
                                        :src="imagePreview"
                                        alt=""
                                        class="img-student"
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="image col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label>Fotografia</label>
                                        <BFormFile
                                            v-on:change="upImage"
                                            accept="image/* "
                                            placeholder="imagen formato png"
                                        />
                                        <InputError :error="errors.foto" />
                                    </div>
                                </div>
                            </div>
                            <FormPerson :form="form" :errors="errors" />
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Fecha Nacimiento
                                            <span
                                                style="
                                                    color: red;
                                                    margin-left: 2px;
                                                "
                                            >
                                                *
                                            </span>
                                        </label>
                                        <input
                                            class="form-control"
                                            type="date"
                                            v-model="form.fecha_nacimiento"
                                            :class="{
                                                'is-invalid':
                                                    errors.fecha_nacimiento,
                                            }"
                                        />
                                        <InputError
                                            :error="errors.fecha_nacimiento"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Correo Electrónico
                                            <span
                                                style="
                                                    color: red;
                                                    margin-left: 2px;
                                                "
                                            >
                                                *
                                            </span>
                                        </label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.email"
                                            :class="{
                                                'is-invalid': errors.email,
                                            }"
                                        />
                                        <InputError :error="errors.email" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Telefono
                                            <span class="required"> * </span>
                                        </label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.telefono"
                                            :class="{
                                                'is-invalid': errors.telefono,
                                            }"
                                        />
                                        <InputError :error="errors.telefono" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Usuario
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <input
                                            :value="form.numero_documento"
                                            class="form-control"
                                            type="text"
                                            disabled
                                            :class="{
                                                'is-invalid': errors.usuario,
                                            }"
                                        />
                                        <InputError :error="errors.usuario" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Contraseña
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <input
                                            :value="form.numero_documento"
                                            class="form-control"
                                            type="password"
                                            disabled
                                            :class="{
                                                'is-invalid': errors.password,
                                            }"
                                        />
                                        <InputError :error="errors.password" />
                                    </div>
                                </div>
                            </div>
                        </BTab>
                        <BTab title="DATOS DE CONTACTO">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>País de Procedencia </label>
                                        <select
                                            class="form-control"
                                            v-model="form.id_pais"
                                            :class="{
                                                'is-invalid': errors.id_pais,
                                            }"
                                        >
                                            <option
                                                v-for="option in paises"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError :error="errors.id_pais" />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectDepartament
                                        :departamentos="departamentos"
                                        :error="errors.id_ubigeo"
                                        :modelValue="cod_dpto"
                                        @update:modelValue="
                                            ($event) => (cod_dpto = $event)
                                        "
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Dirección </label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.direccion"
                                            v-input-upper
                                            :class="{
                                                'is-invalid': errors.direccion,
                                            }"
                                        />
                                        <InputError :error="errors.direccion" />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectProvince
                                        :error="errors.id_ubigeo"
                                        :modelValue="cod_prov"
                                        @update:modelValue="
                                            ($event) => (cod_prov = $event)
                                        "
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Lengua Materna</label>
                                        <select
                                            class="form-control"
                                            v-model="form.id_lengua_materna"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_lengua_materna,
                                            }"
                                        >
                                            <option
                                                v-for="option in lenguas_maternas"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_lengua_materna"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectDistrict
                                        :error="errors.id_ubigeo"
                                        :modelValue="form.id_ubigeo"
                                        @update:modelValue="
                                            ($event) =>
                                                (form.id_ubigeo = $event)
                                        "
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.celular"
                                            :class="{
                                                'is-invalid': errors.celular,
                                            }"
                                        />
                                        <InputError :error="errors.celular" />
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>¿Tiene Discapacidad?</label>
                                        <BFormCheckbox
                                            v-model="form.discapacidad"
                                            name="check-button"
                                            switch
                                        >
                                        </BFormCheckbox>
                                        <InputError
                                            :error="errors.discapacidad"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label
                                            >¿Menor de Edad?
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <BFormCheckbox
                                            v-model="form.menor_edad"
                                            name="check-button"
                                            switch
                                        >
                                        </BFormCheckbox>
                                        <InputError
                                            :error="errors.menor_edad"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-show="form.menor_edad">
                                <div class="col-md-10 col-lg-10">
                                    <div class="form-group">
                                        <label
                                            >Apoderado
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <VSelect
                                            ref="apoderado"
                                            :path="
                                                routeTo(
                                                    `apoderados/autocomplete`
                                                )
                                            "
                                            place-holder="Escribe para buscar"
                                            label="label"
                                            @input="addApoderado"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button
                                            class="btn btn-primary"
                                            title="Registrar Apoderado"
                                            style="display: block"
                                            @click.prevent="createApoderado"
                                        >
                                            <feather-icon
                                                icon="UserPlusIcon"
                                                class="mr-50"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <DetalleApoderado
                                :items="form.apoderados"
                                v-show="form.menor_edad"
                            />
                        </BTab>
                        <BTab title="DATOS ACADÉMICOS">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>I.E de Procedencia</label>
                                        <select
                                            class="form-control"
                                            v-model="
                                                form.id_modalidad_educativa
                                            "
                                            :class="{
                                                'is-invalid':
                                                    errors.id_modalidad_educativa,
                                            }"
                                        >
                                            <option
                                                v-for="option in modalidades_educativas"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }} ({{
                                                    option.abreviatura
                                                }})
                                            </option>
                                        </select>
                                        <InputError
                                            :error="
                                                errors.id_modalidad_educativa
                                            "
                                        />
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Código Modular</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.codigo_modular"
                                            :class="{
                                                'is-invalid':
                                                    errors.codigo_modular,
                                            }"
                                        />
                                        <InputError
                                            :error="errors.codigo_modular"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Nombre de la I.E</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.institucion"
                                            :class="{
                                                'is-invalid':
                                                    errors.institucion,
                                            }"
                                        />
                                        <InputError
                                            :error="errors.institucion"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Año de Egreso</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="
                                                form.anio_egreso_institucion
                                            "
                                            :class="{
                                                'is-invalid':
                                                    errors.anio_egreso_institucion,
                                            }"
                                        />
                                        <InputError
                                            :error="
                                                errors.anio_egreso_institucion
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Nivel</label>
                                        <select
                                            class="form-control"
                                            v-model="form.id_nivel_educativa"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_nivel_educativa,
                                            }"
                                        >
                                            <option
                                                v-for="option in niveles_educativos"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_nivel_educativa"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectDepartament
                                        :departamentos="departamentos"
                                        :error="errors.id_ubigeo_institucion"
                                        :modelValue="cod_dptoI"
                                        :index="1"
                                        @update:modelValue="
                                            ($event) => (cod_dptoI = $event)
                                        "
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Tipo de Gestión</label>
                                        <select
                                            class="form-control"
                                            v-model="
                                                form.id_tipo_gestion_educativa
                                            "
                                            :class="{
                                                'is-invalid':
                                                    errors.id_tipo_gestion_educativa,
                                            }"
                                        >
                                            <option
                                                v-for="option in tipo_gestion_educativas"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="
                                                errors.id_tipo_gestion_educativa
                                            "
                                        />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectProvince
                                        :error="errors.id_ubigeo_institucion"
                                        :modelValue="cod_provI"
                                        :index="1"
                                        @update:modelValue="
                                            ($event) => (cod_provI = $event)
                                        "
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            v-model="form.direccion_institucion"
                                            v-input-upper
                                            :class="{
                                                'is-invalid':
                                                    errors.direccion_institucion,
                                            }"
                                        />
                                        <InputError
                                            :error="
                                                errors.direccion_institucion
                                            "
                                        />
                                    </div>
                                </div>
                                <div
                                    class="col-md-6 col-lg-6"
                                    v-if="form.id_pais == id_peru"
                                >
                                    <SelectDistrict
                                        :error="errors.id_ubigeo_institucion"
                                        :modelValue="form.id_ubigeo_institucion"
                                        :index="1"
                                        @update:modelValue="
                                            ($event) =>
                                                (form.id_ubigeo_institucion =
                                                    $event)
                                        "
                                    />
                                </div>
                            </div>
                        </BTab>
                        <BTab title="PROGRAMA DE ESTUDIO">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Sede
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            @change="getProgramas"
                                            class="form-control"
                                            v-model="form.id_sede"
                                            :class="{
                                                'is-invalid': errors.id_sede,
                                            }"
                                        >
                                            <option
                                                v-for="option in sedes"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError :error="errors.id_sede" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Programa de Estudio
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            @change="getPlanes"
                                            class="form-control"
                                            v-model="form.id_programa_estudio"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_programa_estudio,
                                            }"
                                        >
                                            <option
                                                v-for="option in programas"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_programa_estudio"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Plan de Estudio
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            class="form-control"
                                            v-model="form.id_plan_estudio"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_plan_estudio,
                                            }"
                                        >
                                            <option
                                                v-for="option in planes"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_plan_estudio"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Tipo de Estudiante
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            @change="disableFunction"
                                            class="form-control"
                                            v-model="form.id_tipo_estudiante"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_tipo_estudiante,
                                            }"
                                        >
                                            <option
                                                v-for="option in tipo_estudiantes"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_tipo_estudiante"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Ciclo
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            :disabled="disableCiclo"
                                            class="form-control"
                                            v-model="form.id_ciclo"
                                            :value="form.id_ciclo"
                                            :class="{
                                                'is-invalid': errors.id_ciclo,
                                            }"
                                        >
                                            <option
                                                v-for="option in ciclos"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError :error="errors.id_ciclo" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label
                                            >Periodo de Ingreso
                                            <span class="required">
                                                *
                                            </span></label
                                        >
                                        <select
                                            class="form-control"
                                            v-model="form.id_periodo_ingreso"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_periodo_ingreso,
                                            }"
                                        >
                                            <option
                                                v-for="option in periodo_clases"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_periodo_ingreso"
                                        />
                                    </div>
                                </div>
                            </div>
                        </BTab>
                    </BTabs>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="close"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="store"
                        :disabled="form.processing"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Guardar</span>
                    </button>
                </template>
            </Modal>
            <ModalApoderado @store="addApoderado" ref="newApoderado" />
            <Modal ref="modalImportStudents">
                <template #title>Importar estudiantes</template>
                <form class="form form-vertical">
                    <div class="col-md-12 col-lg-12">
                        <!-- Selector de sede -->
                        <div class="form-group">
                            <label
                                >Sede <span class="required"> * </span></label
                            >
                            <select
                                class="form-control"
                                v-model="form_import_students.id_sede"
                                :class="{ 'is-invalid': errors.id_sede }"
                            >
                                <option
                                    v-for="sede in sedes"
                                    :key="sede.id"
                                    :value="sede.id"
                                >
                                    {{ sede.descripcion }}
                                </option>
                            </select>
                            <InputError :error="errors.id_sede" />
                        </div>

                        <!-- Selector de archivo -->
                        <div class="form-group">
                            <label>
                                Documento <span class="required">*</span>
                            </label>

                            <FileSelector
                                v-model="form_import_students.excel_file"
                                accept=".xlsx,.xls,.csv"
                                button-variant="btn-primary"
                                :error="errors.excel_file"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'excel_file',
                                            form_import_students
                                        )
                                "
                            />
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeImportStudents"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>

                    <button
                        class="btn btn-success"
                        @click.prevent="storeImportStudents"
                    >
                        <feather-icon icon="UploadIcon" />
                        <span>Importar</span>
                    </button>
                </template>
            </Modal>
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import {
    alertError,
    alertSuccess,
    alertWarning,
    confirm,
} from "../../sweetAlert2.js";
import FormPerson from "../../Components/FormPerson.vue";
import { objectToFormData } from "../../formData.js";
import { BFormCheckbox, BFormFile, BTabs, BTab } from "bootstrap-vue";
import SelectDepartament from "../../Components/SelectDepartament.vue";
import SelectProvince from "../../Components/SelectProvince.vue";
import SelectDistrict from "../../Components/SelectDistrict.vue";
import DetalleApoderado from "./DetalleApoderado.vue";
import ModalApoderado from "./ModalApoderado.vue";
import FileSelector from "../../Components/FileSelector.vue";
import VSelect from "../../Components/VSelect.vue";
import _ from "lodash";

export default {
    components: {
        VSelect,
        ModalApoderado,
        FileSelector,
        FileSelector,
        DetalleApoderado,
        SelectDistrict,
        SelectProvince,
        SelectDepartament,
        FormPerson,
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        BFormFile,
        BFormCheckbox,
        BTabs,
        BTab,
    },
    name: "Student",
    props: {
        urlRoute: String,
        tipo_contratos: Array,
        paises: Array,
        modalidades_educativas: Array,
        niveles_educativos: Array,
        tipo_gestion_educativas: Array,
        departamentos: Array,
        lenguas_maternas: Array,
        tipo_estudiantes: Array,
        sedes: Array,
        ciclos: Array,
        id_peru: Number,
        periodo_clases: Array,
    },
    data() {
        return {
            disableCiclo: false,
            cod_dpto: "0",
            cod_prov: "0",
            cod_dptoI: "0",
            cod_provI: "0",
            programas: "",
            planes: "",
            imagePreview:
                "https://upload.wikimedia.org/wikipedia/commons/7/72/Default-welcomer.png",
            columns: [
                {
                    data: null,
                    title: "#",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                    width: "2rem",
                },
                { data: "nombres", title: "Nombres" },
                { data: "apellido_paterno", title: "Apellido Paterno" },
                { data: "apellido_materno", title: "Apellido Materno" },
                { data: "tipo_documento", title: "Documento" },
                { data: "numero_documento", title: "Numero" },
            ],
            form: useForm({
                id_tipo_documento_identidad: 0,
                numero_documento: "",
                nombres: "",
                apellido_paterno: "",
                apellido_materno: "",
                sexo: "M",
                estado_civil: "SOLTERO",
                usuario: "",
                password: "",
                telefono: "",
                email: "",
                id: "-1",
                id_usuario: "-1",
                id_personas: "-1",
                id_sede: "",
                id_programa_estudio: "",
                id_plan_estudio: "",
                id_tipo_estudiante: "",
                id_lengua_materna: "1",
                id_pais: "",
                id_modalidad_educativa: "",
                id_nivel_educativa: 3,
                id_tipo_gestion_educativa: "1",
                id_ubigeo: "",
                direccion: "",
                celular: "",
                fecha_nacimiento: "",
                discapacidad: false,
                menor_edad: false,
                codigo_modular: "",
                institucion: "",
                id_ubigeo_institucion: "",
                direccion_institucion: "",
                anio_egreso_institucion: "",
                id_ciclo: "",
                id_periodo_ingreso: 0,
                estado_matricula: "REGULAR",
                foto: "",
                apoderados: [],
            }),
            form_import_students: useForm({
                id_sede: 0,
                excel_file: null,
            }),
            activeTab: 0,
        };
    },
    methods: {
        updateField(value, field, form) {
            this.$set(form, field, value); // Usamos $set para asegurar reactividad
        },
        disableFunction() {
            if (this.form.id_tipo_estudiante != 1) {
                this.form.id_ciclo = 1;
                this.disableCiclo = true;
            } else {
                this.form.id_ciclo = "";
                this.disableCiclo = false;
            }
        },
        upImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.foto = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        setForm(data) {
            this.setFormData(this.form, data);
            this.form.password = data.password_vista;
            this.form.id_sede = data.id_sede;
            this.form.id_programa_estudio = data.id_programa_estudio;
            this.getProgramas();
            if (data.discapacidad == "S") {
                this.form.discapacidad = true;
            } else {
                this.form.discapacidad = false;
            }
            if (data.menor_edad == "S") {
                this.form.menor_edad = true;
            } else {
                this.form.menor_edad = false;
            }
            this.getPlanes();
            if (data.distrito) {
                this.cod_dpto = data.distrito.cod_dpto;
                this.cod_prov = data.distrito.cod_prov;
            }
            if (data.distrito_i) {
                this.cod_dptoI = data.distrito_i.cod_dpto;
                this.cod_provI = data.distrito_i.cod_prov;
            }

            if (data.foto != null) {
                this.imagePreview = data.foto.base64;
            }
            if (data.detalle_apoderado) {
                this.form.apoderados = data.detalle_apoderado;
            }
        },
        reset() {
            this.form.reset();
            this.clearErrors();
            this.imagePreview =
                "https://upload.wikimedia.org/wikipedia/commons/7/72/Default-welcomer.png";
            this.form.id_pais = this.id_peru;
            this.cod_dpto = "0";
            this.cod_prov = "0";
            this.cod_provI = "0";
            this.cod_dptoI = "0";
        },
        create() {
            this.reset();
            this.activeTab = 0;
            this.$refs.modalProfile.show();
        },
        close() {
            this.$refs.modalProfile.hide();
        },
        store() {
            this.form.password = this.form.numero_documento;
            this.form.usuario = this.form.numero_documento;
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(this.urlRoute),
                data: objectToFormData(this.form.data()),
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modalProfile.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
        },
        getProgramas() {
            this.$http
                .get(
                    this.routeTo(
                        `${this.urlRoute}/programasFilter/${this.form.id_sede}`
                    )
                )
                .then((res) => {
                    this.programas = res.data;
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
                })
                .finally(() => {});
        },
        getPlanes() {
            this.$http
                .get(
                    this.routeTo(
                        `${this.urlRoute}/planesFilter/${this.form.id_programa_estudio}`
                    )
                )
                .then((res) => {
                    this.planes = res.data;
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
                })
                .finally(() => {});
        },
        edit() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.reset();
            this.activeTab = 0;
            this.$refs.btnEdit.load();
            this.$http
                .get(this.routeTo(`${this.urlRoute}/${row.id}/edit`))
                .then((res) => {
                    this.setForm(res.data);
                    this.$refs.modalProfile.show();
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                });
        },
        destroy() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    this.$refs.btnDestroy.load();
                    this.$http
                        .delete(this.routeTo(`${this.urlRoute}/${row.id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$refs.datatable.reload();
                        })
                        .catch((error) => {
                            if (this.errors.record != undefined) {
                                alertWarning(this.errors.record);
                            }
                        })
                        .finally(() => {
                            this.$refs.btnDestroy.reset();
                        });
                }
            );
        },
        createApoderado() {
            this.$refs.newApoderado.create();
        },
        addApoderado(apoderado) {
            const found = this.form.apoderados.find((el) => {
                return el.id_apoderado == apoderado.id;
            });
            if (found != undefined) {
                alertWarning("el apoderado ya fue agregado");
                return;
            }
            this.form.apoderados.push({
                id_apoderado: apoderado.id,
                apoderado: apoderado.apoderado,
                tipo_documento: apoderado.tipo_documento,
                numero_documento: apoderado.numero_documento,
                parentesco: "MADRE",
            });
        },
        activeTabsIndex(tab, index) {
            let active = false;
            tab.forEach((el) => {
                if (this.errors.hasOwnProperty(el)) {
                    this.activeTab = index;
                    active = true;
                    return false;
                }
            });
            //console.log(this.activeTab)
            return active;
        },
        validateTabs() {
            if (!_.isEmpty(this.errors)) {
                let activetab = false;
                const tab1 = [
                    "numero_documento",
                    "nombres",
                    "apellido_paterno",
                    "apellido_materno",
                    "sexo",
                    "fecha_nacimiento",
                    "email",
                    "telefono",
                    "usuario",
                    "password",
                ];
                activetab = this.activeTabsIndex(tab1, 0);
                if (!activetab) {
                    const tab2 = [
                        "id_pais",
                        "direccion",
                        "id_ubigeo",
                        "celular",
                        "id_lengua_materna",
                    ];
                    activetab = this.activeTabsIndex(tab2, 1);
                }
                if (!activetab) {
                    const tab3 = [
                        "codigo_modular",
                        "institucion",
                        "anio_egreso_institucion",
                        "id_nivel_educativa",
                        "id_ubigeo_institucion",
                        "id_tipo_gestion_educativa",
                        "direccion_institucion",
                    ];
                    activetab = this.activeTabsIndex(tab3, 2);
                }
                if (!activetab) {
                    const tab4 = [
                        "id_sede",
                        "id_programa_estudio",
                        "id_plan_estudio",
                        "id_tipo_estudiante",
                        "id_ciclo",
                    ];
                    activetab = this.activeTabsIndex(tab4, 3);
                }
            }
        },

        createImportStudents() {
            this.form_import_students.reset();
            this.$refs.modalImportStudents.show();
        },
        closeImportStudents() {
            this.$refs.modalImportStudents.hide();
        },
        storeImportStudents() {
            // Validación básica
            if (this.form_import_students.id_sede === 0) {
                alertWarning("Por favor seleccione una sede");
                return;
            }

            console.log(this.form_import_students.excel_file);

            if (!this.form_import_students.excel_file) {
                alertWarning("Por favor seleccione un archivo");
                return;
            }

            // Validar tipo de archivo
            const validTypes = [
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "application/vnd.ms-excel",
                "text/csv",
            ];

            if (
                !validTypes.includes(this.form_import_students.excel_file.type)
            ) {
                alert(
                    "Por favor suba un archivo Excel válido (.xlsx, .xls, .csv)"
                );
                return;
            }

            // Crear FormData
            const formData = new FormData();
            formData.append("id_sede", this.form_import_students.id_sede);
            formData.append("excel_file", this.form_import_students.excel_file);

            this.form_import_students.processing = true;

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/importar-estudiantes-excel`
                ),
                data: formData,
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
                .then((res) => {
                    this.closeImportStudents();
                    this.$refs.datatable.reload(); // Actualiza la tabla
                    alertSuccess("Estudiantes importados correctamente");
                })
                .catch((error) => {
                    alertError("Error al subir el archivo: " + error);
                })
                .finally(() => {
                    this.form_import_students.processing = false;
                });
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors || {};
        },
    },
    watch: {
        errors: {
            handler: function (value) {
                this.validateTabs();
            },
        },
    },
};
</script>
<style scoped>
.required {
    color: red;
    margin-left: 2px;
}
.img-student {
    width: 25vh;
    height: 23vh;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06),
        0 1px 3px rgba(0, 0, 0, 0.1);
}
.image {
    display: flex;
    justify-content: center;
    align-items: center;
}
.custom-file-label::after {
    content: "Subir" !important;
}

/* FILE */

.file-input-container {
    position: relative;
    margin-bottom: 1rem;
}

.file-input-label {
    display: block;
}

.file-input {
    position: absolute;
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}

.file-input-custom {
    display: flex;
    align-items: center;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 8px 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-input-custom:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.file-input-icon {
    margin-right: 10px;
    color: #495057;
}

.file-input-text {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #495057;
}

.file-input-button {
    margin-left: 10px;
}

.is-invalid ~ .file-input-custom {
    border-color: #dc3545;
}

.form-text {
    display: block;
    margin-top: 4px;
    font-size: 0.875em;
    color: #6c757d;
}
</style>
