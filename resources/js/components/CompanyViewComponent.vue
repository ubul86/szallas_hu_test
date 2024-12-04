<template>
    <v-row>
        <v-col>
            <h1 v-if="companyData">{{ companyData.name }}</h1>
        </v-col>
    </v-row>

</template>

<script setup>
defineProps({
    companyData: Object
});
</script>
