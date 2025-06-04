<template>
    <!-- modal-dialog-centered modal-dialog-scrollable-->
    <div class="modal fade" :class="{ show: openI }">
        <div class="modal-dialog" :class="[modal]" style="max-width: 700px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <slot name="title"></slot>
                    </h4>
                    <button
                        type="button"
                        class="close"
                        @click="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Spinner v-if="loading" />
                    <slot />
                </div>
                <div class="modal-footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Spinner from "./Spinner";
export default {
    name: "ModalTable",
    components: { Spinner },
    props: {
        modal: {
            type: String,
            default: "",
        },
        loading: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            openI: false,
        };
    },
    methods: {
        show() {
            this.openI = true;
        },
        close() {
            this.openI = false;
        },
        hide() {
            this.openI = false;
        },
    },
    watch: {
        openI: {
            immediate: true,
            handler(openI) {
                if (openI) {
                    document.body.classList.add("modal-open");
                    //Incrementamos el valor de los modales abiertos
                    window.modalCount = (window.modalCount || 0) + 1;
                    if (window.modalCount == 1) {
                        $("body").append(
                            '<div class="modal-backdrop fade show"></div>'
                        );
                    }
                } else {
                    // Diminuimos el contador
                    window.modalCount = (window.modalCount || 0) - 1;
                    if (window.modalCount <= 0) {
                        document.body.classList.remove("modal-open");
                        $("body").find(".modal-backdrop").remove();
                        window.modalCount = 0;
                    }
                }
            },
        },
    },
};
</script>

<style scoped>
.modal.show {
    display: block;
}
</style>
