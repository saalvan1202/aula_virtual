<template>
    <span
        v-if="badge == 'full-color'"
        :class="[
            'badge',
            green_mode ? 'bg-primary-green' : 'bg-primary-purple',
        ]"
    >
        <span class="feather">
            <feather-icon
                :icon="custom_icon ? custom_icon : 'ZapIcon'"
                size="15"
                stroke-width="2.5"
            />
        </span>
        {{ name }}
    </span>
    <span
        v-else-if="badge == 'bordered'"
        :class="[
            'badge',
            green_mode
                ? 'bg-primary-green-bordered'
                : 'bg-primary-purple-bordered',
        ]"
    >
        <span class="feather">
            <feather-icon
                :icon="custom_icon ? custom_icon : 'UsersIcon'"
                size="15"
                stroke-width="2.5"
            />
        </span>
        {{ name }}
    </span>
    <span
        v-else-if="badge == 'conditional'"
        :class="[
            'badge',
            conditional_owner
                ? 'bg-primary-green-bordered'
                : 'bg-primary-red-bordered',
        ]"
    >
        <span class="feather">
            <feather-icon
                :icon="conditional_owner ? 'CheckCircleIcon' : 'XCircleIcon'"
                size="15"
                stroke-width="2.5"
            />
        </span>
        {{ conditional_owner ? "Disponible" : "Inhabilitado" }}
    </span>
    <span v-else-if="badge == 'aqua'" class="badge bg-primary-aqua">
        {{ name }}
    </span>
</template>

<script>
export default {
    props: {
        badge: {
            type: String,
            required: true,
        },
        name: {
            type: String,
        },
        custom_icon: {
            type: String,
        },
        green_mode: {
            type: Boolean,
            default: false,
        },
        conditional_owner: {
            type: Boolean,
            default: false,
        },
    },
};
</script>

<style scoped>
.bg-primary-purple {
    background-color: #7100c9;
    color: white;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}

.bg-primary-purple-bordered {
    background-color: white;
    color: #7100c9;
    border: #7100c9 solid 1px;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}

.bg-primary-green {
    background-color: #39c239;
    color: white;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}

.bg-primary-green-bordered {
    background-color: white;
    color: #39c239;
    border: #39c239 solid 1px;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}

.bg-primary-red-bordered {
    background-color: white;
    color: #e84201;
    border: #e84201 solid 1px;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}

.bg-primary-aqua {
    background-color: #d5f8f8;
    border: #8be6e5 solid 1px;
    color: #474658;
    padding: 8px;
    font-size: 12px;

    .feather {
        margin-right: 2px;
    }
}
</style>
