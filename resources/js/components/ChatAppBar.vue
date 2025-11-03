<template>
    <v-toolbar flat dark color="primary">
        <v-toolbar-title class="mr-2">
            {{ title ? title : 'Chat' }}
        </v-toolbar-title>
        <v-spacer />
        <v-btn dark icon @click="() => $emit('drawerClick')">
            <v-icon>mdi-menu</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
export default {
    props: {
        title: { type: String, default: null },
    },
    data: () => ({
        lang: null,
    }),
    methods: {},
}
//
</script>
