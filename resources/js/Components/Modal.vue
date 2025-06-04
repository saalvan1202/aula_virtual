<template>
    <!-- modal-dialog-centered modal-dialog-scrollable-->
    <div class="modal fade" :class="{ show: open }">
        <div class="modal-dialog" :class="[modal]">
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
    name: "Modal",
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
            open: false,
        };
    },
    methods: {
        show() {
            this.open = true;
        },
        close() {
            this.open = false;
            console.log(this.open);
        },
        hide() {
            this.open = false;
        },
    },
    watch: {
        open: {
            immediate: true,
            handler(open) {
                if (open) {
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
