<template>
    <div v-if="items">
        <template v-for="(item, j) in items" :key="j">
            <v-slide-x-transition
                :style="'transition-delay:' + j * 300 + 'ms;'"
            >
                <v-chip
                    v-show="show"
                    target="_blank"
                    :href="getUrl(item)"
                    :style="'height:auto;white-space: normal;border-radius:24px;'"
                    class="pa-4 mb-2 mr-1"
                >
                    <v-icon small class="mr-1">
                        {{ getIcon(item) }}
                    </v-icon>
                    {{ getText(item) }}
                </v-chip>
            </v-slide-x-transition>
        </template>
    </div>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            default: null,
        },
        show: {
            type: Boolean,
        },
    },
    methods: {
        getIcon(item) {
            switch (item.type) {
                case 'voorwaarden':
                    return 'fa-file'
                case 'telefoonnummer':
                    return 'fa-phone'
                case 'email':
                    return 'fa-envelope'
                default:
                    return 'fa-info-circle'
            }
        },
        getText(item) {
            switch (item.type) {
                case 'voorwaarden':
                    return 'Polis ' + item.value
                default:
                    return item.value
            }
        },
        getUrl(item) {
            switch (item.type) {
                case 'voorwaarden':
                    return 'https://voorwaarden.nh1816.nl/0' + item.value
                case 'telefoonnummer':
                    return 'tel:' + item.value
                case 'email':
                    return 'mailto:' + item.value
                default:
                    return ''
            }
        },
    },
}
</script>
