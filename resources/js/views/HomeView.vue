<script setup>
import { onMounted } from 'vue';
import DataTableComponent from '@/components/DataTableComponent.vue'
import {useCompanyStore} from "@/stores/company.store.js";

const companyStore = useCompanyStore();

onMounted(() => {
    companyStore.fetchCompanies();
});

</script>

<template>
    <DataTableComponent />
</template>
