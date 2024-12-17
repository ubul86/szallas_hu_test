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
import { useCompanyStore } from '@/stores/company.store.js';
import { useToast } from 'vue-toastification';
import DialogForm from './DialogForm.vue';
import useForm from '@/composables/useForm.js';

const { formErrors, resetErrors, handleApiError } = useForm();

const companyStore = useCompanyStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Company');

const isLoading = ref(false);

const editedItem = ref({
    name: null,
    registration_number: null,
    foundation_date: null,
    activity: null,
    active: false
})

const defaultItem = {
    name: null,
    registration_number: null,
    foundation_date: null,
    activity: null,
    active: false
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
        title.value = newVal < 0 ? 'New Company' : 'Edit Company';

        editedItem.value = {
            ...defaultItem,
        };

        if (newVal >= 0) {
            const company = companyStore.companies[newVal];
            if (company) {
                editedItem.value = {
                    ...company,
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
            await companyStore.update(props.editedIndex, itemToSubmit);
            toast.success('You have successfully edited the item!');
        } else {
            await companyStore.store(itemToSubmit)
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
    { model: 'registration_number', component: 'v-text-field', props: { label: 'Registration Number', error: !!formErrors.value.registration_number, 'error-messages': formErrors.value.registration_number || [] } },
    {
        model: 'foundation_date',
        component: 'v-date-input',
        props: {
            label: "Select a Foundation Date",
            variant: "outlined",
            persistentPlaceholder: true,
            'model-value': editedItem.value.foundation_date
        },
    },
    {
        model: 'active',
        component: 'v-select',
        props: {
            label: 'Active',
            items: [
                { title: 'Active', text: 'Active', value: true },
                { title: 'Inactive', text: 'Inactive', value: false }
            ],
            'item-text': 'text',
            'item-value': 'value',
            error: !!formErrors.value.active,
            'error-messages': formErrors.value.active || [],
            returnObject: false
        }
    },
    { model: 'activity', component: 'v-select', props: { label: 'Activity', error: !!formErrors.value.activity, items: ['Development', 'Design', 'Marketing', 'Sales'], 'error-messages': formErrors.value.activity || [] } },
]);

</script>
