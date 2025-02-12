<template>
    <DialogForm
        :title="title"
        :fields="fields"
        v-model:formData="editedItem"
        :dialog-visible="localDialogVisible"
        @cancel="handleCancel"
        @submit="handleSubmit"
        :is-loading="isLoading"
    />
</template>

<script setup>
import 'vuetify/styles';
import { computed, ref, watch } from 'vue'
import { useToast } from 'vue-toastification';
import DialogForm from './DialogForm.vue';
import useForm from '@/composables/useForm.js';
import {useCompanyEmployeeStore} from "@/stores/company.employee.store.js";

const { formErrors, resetErrors, handleApiError } = useForm();

const companyEmployeeStore = useCompanyEmployeeStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
    companyId: Number,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Company Employee');

const isLoading = ref(false);

const editedItem = ref({
    name: null,
    active: false,
})

const defaultItem = {
    name: null,
    active: false,
}

watch(
    () => props.dialogVisible,
    (newVal) => {
        localDialogVisible.value = newVal;
    }
);

watch(
    () => props.editedIndex,
    (newVal) => {
        title.value = newVal < 0 ? 'New Company Employee' : 'Edit Company Employee';

        editedItem.value = {
            ...defaultItem,
        };

        if (newVal >= 0) {
            const companyEmployees = companyEmployeeStore.company_employees[newVal];
            if (companyEmployees) {
                editedItem.value = {
                    ...companyEmployees,
                };
            }
        }
    }
);

const handleCancel = () => {
    localDialogVisible.value = false;
    editedItem.value = { ...defaultItem };
    emit('close');
};

const handleSubmit = async (itemToSubmit) => {
    resetErrors();
    isLoading.value = true;
    try {
         if (props.editedIndex > -1) {
            await companyEmployeeStore.update(props.companyId, props.editedIndex, itemToSubmit);
            toast.success('You have successfully edited the item!');
        } else {
            await companyEmployeeStore.store(props.companyId, itemToSubmit)
            toast.success('You have successfully created a new item!');
        }

        localDialogVisible.value = false;
        editedItem.value = { ...defaultItem };
        emit('close');
    }
    catch(error) {
        handleApiError(error);
        toast.error(error.response.data.message);
    }
    finally {
        isLoading.value = false;
    }

};


const fields = computed(() => [
    { model: 'name', component: 'v-text-field', props: { label: 'Name', error: !!formErrors.value.name, 'error-messages': formErrors.value.name || [] } },
    { model: 'active', component: 'v-checkbox', props: { value: 1, label: 'Active', error: !!formErrors.value.active, 'error-messages': formErrors.value.active || [] } },
]);

</script>
