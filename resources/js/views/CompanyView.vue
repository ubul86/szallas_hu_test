<script setup>
import { ref, onMounted } from 'vue';
import {useCompanyStore} from "@/stores/company.store.js";
import { useRoute } from 'vue-router';

const companyStore = useCompanyStore();

const route = useRoute();
const company = ref(null);

onMounted(() => {
    const id = route.params.id;
    company.value = companyStore.show(id);
});

</script>

<template>
    asd
</template>
