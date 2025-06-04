<template>
    <div class="contenidos row">
        <div
            v-for="_data in data"
            :key="_data.id"
            class="acordeon-container col-md-12"
            :id="'_data-' + _data.id"
        >
            <div class="acordeon">
                <div
                    class="acordeon-header d-flex justify-content-between align-items-center"
                >
                    <div
                        class="acordeon-header__title d-flex justify-content-start"
                        @click="toggleClase(_data.id)"
                        style="cursor: pointer"
                    >
                        <span>
                            <feather-icon
                                stroke-width="3px"
                                :icon="
                                    isVisible(_data.id)
                                        ? 'ArrowDownIcon'
                                        : 'ArrowRightIcon'
                                "
                            ></feather-icon>
                        </span>
                        <h4 class="m-0 p-0">{{ _data.titulo }}</h4>
                    </div>
                    <div class="acordeon-header__actions">
                        <DropdownMenu
                            @toggle="toggleDropdown(_data.id)"
                            :active-dropdown="activeDropdown === _data.id"
                        >
                            <p @click="editarClase(_data.id)">Editar clase</p>
                            <p @click="borrarClase(_data.id)">Borrar clase</p>
                        </DropdownMenu>
                    </div>
                </div>
                <transition
                    name="slide"
                    @before-enter="beforeEnter"
                    @enter="enter"
                    @leave="leave"
                >
                    <div v-show="isVisible(_data.id)" class="acordeon-body">
                        <Activity
                            typeActivity="CUESTIONARIO"
                            subTitle="Examen 1"
                            icon="CheckSquareIcon"
                        />
                        <AddActivity
                            title="Añadir una actividad o un recurso"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        data: {
            type: Array,
            default: [],
        },
    },
    methods: {
        isVisible(id) {
            console.log(id);
            return this.visibleClases.includes(id); // Verificar si la clase está visible
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
        editarClase(id) {
            alert(`Editar ${this.clases.find((c) => c.id === id).titulo}`);
        },
        borrarClase(id) {
            alert(`Borrar ${this.clases.find((c) => c.id === id).titulo}`);
        },
    },
};
</script>

<style scoped>
.contenidos {
    border-top: 1px solid #e2e2e2;
}

.acordeon {
    position: relative;
}

.acordeon-container {
    border-bottom: 1px solid #e2e2e2;
    border-bottom-left-radius: 20px;
}

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

/* Transiciones */
.slide-enter-active,
.slide-leave-active {
    transition: height 0.2s ease, opacity 0.2s ease;
}

.slide-enter,
.slide-leave-to {
    opacity: 0;
}
</style>
