<template>
    <v-card class="pa-4" outlined>
        <v-toolbar flat>
            <v-toolbar-title>{{ props.companyData?.name || ' - ' }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="onEditClick" v-if="props.companyData">
                <v-icon>mdi-pencil</v-icon>
            </v-btn>
        </v-toolbar>

        <v-card-text>
            <v-row>
                <v-col cols="12" md="6">
                    <strong>Activity:</strong> {{ props.companyData?.activity || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Registration Number:</strong> {{ props.companyData?.registration_number || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Foundation Date:</strong>
                    {{ new Date(props.companyData?.foundation_date).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Active:</strong> {{ props.companyData?.active ? 'Active' : 'Inactive' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Created At:</strong> {{ new Date(props.companyData?.created_at).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Updated At:</strong> {{ new Date(props.companyData?.updated_at).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
    <edit-company-dialog-form :edited-index="editedIndex" :dialog-visible="dialogVisible" @close="onClose"></edit-company-dialog-form>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import EditCompanyDialogForm from "@/components/dialogs/EditCompanyDialogForm.vue";
import {useCompanyStore} from "@/stores/company.store.js";

const companyStore = useCompanyStore();

const props = defineProps({
    companyData: Object
});

const editedIndex = ref(null);

const dialogVisible = ref(false);

const onEditClick = () => {
    const companyId = props.companyData.id;
    editedIndex.value = companyStore.companies.findIndex((company) => company.id === companyId);
    dialogVisible.value = true;
};

const onClose = () => {
    editedIndex.value = null;
    dialogVisible.value = false;
}

</script>
