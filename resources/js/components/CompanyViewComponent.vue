<template>
    <v-card class="pa-4" outlined>
        <v-toolbar flat>
            <v-toolbar-title>{{ companyData?.name || ' - ' }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="onEditClick">
                <v-icon>mdi-pencil</v-icon>
            </v-btn>
        </v-toolbar>

        <v-card-text>
            <v-row>
                <v-col cols="12" md="6">
                    <strong>Activity:</strong> {{ companyData?.activity || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Registration Number:</strong> {{ companyData?.registration_number || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Foundation Date:</strong>
                    {{ new Date(companyData?.foundation_date).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Active:</strong> {{ companyData?.active ? 'Active' : 'Inactive' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Created At:</strong> {{ new Date(companyData?.created_at).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
                <v-col cols="12" md="6">
                    <strong>Updated At:</strong> {{ new Date(companyData?.updated_at).toLocaleDateString('hu-HU') || ' - ' }}
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
    <edit-company-dialog-form :edited-index="editedIndex"></edit-company-dialog-form>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import EditCompanyDialogForm from "@/components/dialogs/EditCompanyDialogForm.vue";

const props = defineProps({
    companyData: Object
});

const editedIndex = ref(null);

const onEditClick = () => {

};

</script>
