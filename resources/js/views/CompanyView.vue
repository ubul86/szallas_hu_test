<template>
    <v-card>
        <v-toolbar color="primary" dark>
            <v-toolbar-title>{{ getCompanyName || 'Loading...' }}</v-toolbar-title>
            <v-spacer></v-spacer>
        </v-toolbar>

        <v-tabs v-model="activeTab" align-tabs="title">
            <v-tab v-for="item in items" :key="item.value" :value="item.value">
                {{ item.label }}
            </v-tab>
        </v-tabs>

        <v-tabs-window v-model="activeTab">
            <v-tabs-window-item v-for="item in items" :key="item.value" :value="item.value">
                <component
                    :is="item.component"
                    v-bind="getComponentProps(item.value)"
                />
            </v-tabs-window-item>
        </v-tabs-window>
    </v-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCompanyStore } from '@/stores/company.store.js';
import { useCompanyAddressStore} from "@/stores/company.address.store.js";

import CompanyViewComponent from '@/components/CompanyViewComponent.vue';
import CompanyAddressViewComponent from '@/components/CompanyAddressViewComponent.vue';
import CompanyOwnerViewComponent from '@/components/CompanyOwnerViewComponent.vue';
import CompanyEmployeeViewComponent from '@/components/CompanyEmployeeViewComponent.vue';

const companyStore = useCompanyStore();
const companyAddressStore = useCompanyAddressStore();

const activeTab = ref('company');
const route = useRoute();
const company = ref(null);
const companyAddress = ref(null);

onMounted(async () => {
    const id = route.params.id;
    company.value = await companyStore.show(id);

    if (company.value?.id) {
        await companyAddressStore.fetchItems(company.value.id)
        companyAddress.value = companyAddressStore.company_addresses;
    }
});

const getCompanyName = computed(() => company.value?.name);

const items = computed(() => [
    { value: 'company', label: 'Company', component: CompanyViewComponent },
    { value: 'company_address', label: 'Address', component: CompanyAddressViewComponent },
    { value: 'company_owners', label: 'Owners', component: CompanyOwnerViewComponent },
    { value: 'company_employees', label: 'Employees', component: CompanyEmployeeViewComponent }
]);

const getComponentProps = (tab) => {
    switch (tab) {
        case 'company':
            return { companyData: company.value };
        case 'company_address':
            return { addresses: companyAddress.value };
        case 'company_owners':
            return { owners: company.value?.owners };
        case 'company_employees':
            return { employees: company.value?.employees };
        default:
            return {};
    }
};
</script>
