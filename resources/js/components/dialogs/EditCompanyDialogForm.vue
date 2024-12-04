<template>
    <DialogForm
        :title="title"
        :fields="fields"
        v-model:formData="editedItem"
        :dialog-visible="localDialogVisible"
        @cancel="handleCancel"
        @submit="handleSubmit"
    />
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import DialogForm from './DialogForm.vue';
import useForm from '@/composables/useForm.js';
import { useCompanyStore } from '@/stores/company.store.js';
import { useToast } from 'vue-toastification';

const { formErrors, resetErrors, handleApiError } = useForm();

const companyStore = useCompanyStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
    users: Array,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Company');

const editedItem = ref({
    user_id: null,
    description: '',
    estimated_time: null,
    used_time: null,
})

const defaultItem = {
    user_id: null,
    description: '',
    estimated_time: null,
    used_time: null,
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

    try {
        if (props.editedIndex.value > -1) {
            await companyStore.update(props.editedIndex.value, itemToSubmit);
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

};


const fields = computed(() => [
    { model: 'name', component: 'v-text-field', props: { label: 'Name', error: !!formErrors.value.name, 'error-messages': formErrors.value.name || [] } },
]);

</script>
