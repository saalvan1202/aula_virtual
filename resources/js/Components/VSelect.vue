<template>
    <div style="width: 100%; position: relative">
        <span
            ref="toggle"
            class="select2 select2-container select2-container--default select2-container--below"
            :class="[
                isOpen
                    ? 'select2-container--open select2-container--focus'
                    : '',
            ]"
            style="/*width: inherit*/"
            @mousedown.prevent="onToggle"
        >
            <span class="selection">
                <span
                    class="select2-selection select2-selection--single"
                    :style="disabled ? 'background-color:#efefef' : ''"
                >
                    <span class="select2-selection__rendered">
                        <span
                            class="select2-selection__placeholder"
                            v-if="placeHolder && isEmpty(selection)"
                            >{{ placeHolder }}</span
                        >
                        {{ displayLabel(selection) }}
                    </span>
                    <span class="select2-selection__arrow">
                        <b role="presentation"></b>
                    </span>
                </span>
            </span>
        </span>
        <span
            v-append-to-body
            class="select2-container select2-container--default select2-container--open"
            v-if="isOpen"
            style="position: absolute"
        >
            <span class="select2-dropdown select2-dropdown--below">
                <span class="select2-search select2-search--dropdown">
                    <input
                        class="select2-search__field"
                        ref="search"
                        placeholder="Buscar..."
                        @click.stop
                        @blur="onSearchBlur"
                        @input.stop="onSearch"
                        @keydown.esc="onEsc"
                        @keydown.up="onUpKey"
                        @keydown.down="onDownKey"
                        @keydown.enter.prevent="onEnterKey"
                        v-focus
                    />
                </span>
                <span class="select2-results">
                    <ul
                        class="select2-results__options"
                        v-show="filteredOptions.length || noResults"
                    >
                        <li
                            class="select2-results__option"
                            v-for="(option, index) in filteredOptions"
                            :key="option.id"
                            @mousedown.prevent="select(option)"
                            @mouseover.prevent="onMouse(index)"
                            :aria-selected="[
                                selectIndex === index ? true : false,
                            ]"
                            :class="[
                                selectIndex === index
                                    ? 'select2-results__option--highlighted'
                                    : '',
                            ]"
                        >
                            {{ option[label] }}
                        </li>
                        <li
                            v-if="noResults"
                            role="treeitem"
                            aria-live="assertive"
                            class="select2-results__option select2-results__message"
                        >
                            No se encontro resultados
                        </li>
                    </ul>
                </span>
            </span>
        </span>
    </div>
</template>

<script>
import _ from "lodash";
import appendToBody from "../appendToBody";
import Vue from "vue";
export default {
    name: "VueSelect",
    directives: { appendToBody },
    props: {
        placeHolder: {
            type: String,
            default: null,
        },
        options: {
            type: Array,
            default: () => [],
        },
        path: {
            type: String,
            default: null,
        },
        appendToBody: {
            type: Boolean,
            default: false,
        },
        calculatePosition: {
            type: Function,
            default(dropdownList, component, { width, top, left }) {
                //console.log('calculatePosition',dropdownList);
                console.log("width", width);
                dropdownList.style.top = top;
                dropdownList.style.left = left;
                dropdownList.style.width = width;
            },
        },
        trackBy: {
            type: String,
            default: "id",
        },
        label: {
            type: String,
            default: "name",
        },
        value: {},
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    data: (v) => ({
        isOpen: false,
        optionList: v.options,
        selectIndex: -1,
        isSearch: false,
        isSelect: false,
        selection: {},
        search: "",
    }),

    computed: {
        serverSide() {
            return this.path !== null;
        },
        filteredOptions() {
            return this.search && !this.serverSide
                ? this.optionList.filter((option) => this.matchesQuery(option))
                : this.optionList;
        },
        noResults() {
            return this.isSearch && this.filteredOptions.length === 0;
        },
    },
    methods: {
        clean() {
            this.selection = {};
        },
        onToggle() {
            if (this.isOpen) {
                // this.isOpen = false;
                // this.close();
            } else if (!this.disabled) {
                this.open();
            }
        },
        onKey(e) {
            const KeyCode = e.KeyCode || e.which;
            if (!e.shiftKey && KeyCode !== 9 && !this.isOpen) {
                this.open();
            }
        },
        setValue(value) {
            //console.log('setValue',value)
            setTimeout(() => {
                if (this.serverSide) {
                    this.selection = value;
                    this.selectIndex = 0;
                    return;
                }
                if (this.isObject(value)) {
                    this.selection = value;
                    this.selectIndex = this.filteredOptions.findIndex(
                        (item) => {
                            return (
                                item[this.trackBy] ===
                                this.selection[this.trackBy]
                            );
                        }
                    );

                    return;
                }
                this.selectIndex = this.filteredOptions.findIndex((item) => {
                    return item[this.trackBy] === value;
                });
                if (this.selectIndex == -1) {
                    return;
                }
                this.selection = this.filteredOptions[this.selectIndex];
            }, 0);
        },
        open() {
            //this.fetchData('')
            Vue.set(this.$data, "isOpen", true);
            //console.log('isOpen-open',this.isOpen);
            if (
                _.isEmpty(this.selection) &&
                this.filteredOptions.length &&
                this.selectIndex === -1
            ) {
                this.selectIndex = 0;
            }
            //
            this.$nextTick(() => {
                this.$refs.search.focus();
            });
            if (this.serverSide) {
                this.fetchData("");
            }
        },
        matchesQuery(option) {
            const label = this.displayLabel(option);

            return this.search
                .toLowerCase()
                .split(" ")
                .filter((arg) => arg !== "")
                .every((arg) => `${label}`.toLowerCase().indexOf(arg) >= 0);
        },
        fetchData(q) {
            this.$http
                .get(this.path, {
                    params: {
                        term: q,
                    },
                    headers: {
                        "X-Inertia-Error-Bag": "select",
                    },
                })
                .then((res) => {
                    Vue.set(this.$data, "optionList", res.data);
                    if (this.selectIndex === -1) {
                        this.selectIndex = 0;
                    }
                });
        },
        onSearchBlur() {
            this.close();
        },
        onBlur() {
            this.close();
        },
        onEsc() {
            this.close();
        },
        close() {
            if (!this.isSelect && this.isSearch) {
                this.$emit("blur", {});
            }
            this.isOpen = false;
            this.isSearch = false;
            this.search = "";
            this.$refs.search.value = "";
            this.isSelect = false;
        },
        onSearch(e) {
            this.isSearch = true;
            const q = e.target.value;
            this.search = q;
            this.selectIndex = 0;
            if (this.serverSide) {
                this.fetchData(q);
            }
        },
        onUpKey(e) {
            if (this.selectIndex > 0) {
                this.selectIndex--;
            }
        },
        onDownKey(e) {
            if (this.filteredOptions.length - 1 > this.selectIndex) {
                this.selectIndex++;
            }
        },
        onEnterKey() {
            const found = this.filteredOptions[this.selectIndex];
            if (found) {
                this.select(found);
            }
        },
        select(result) {
            result.focus = false;
            this.isSelect = true;
            //this.$emit('input',result)
            this.selection = result;
            this.close();
        },
        onMouse(index) {
            this.selectIndex = index;
        },
        displayLabel(option) {
            if (!option) {
                return null;
            }

            const displayLabel = this.label
                .split(".")
                .reduce((result, property) => result[property], option);

            return displayLabel;
        },
    },
    watch: {
        options: {
            handler(options) {
                this.optionList = options;
            },
            deep: true,
        },
        selection() {
            this.$emit("input", this.selection);
        },
        value: {
            immediate: true,
            handler(val) {
                //this.setValue(val)
            },
        },
    },
};
</script>

<style scoped>
.select2-container {
    z-index: 100;
}
.select2-container--open {
    z-index: 200;
    width: 100%;
    left: 0;
}
</style>
