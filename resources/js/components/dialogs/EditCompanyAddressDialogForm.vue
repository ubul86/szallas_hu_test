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
import {useCompanyAddressStore} from "@/stores/company.address.store.js";

const { formErrors, resetErrors, handleApiError } = useForm();

const companyAddressStore = useCompanyAddressStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
    companyId: Number,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Company Address');

const isLoading = ref(false);

const editedItem = ref({
    country: null,
    city: null,
    street_address: null,
    latitude: null,
    longitude: null,
})

const defaultItem = {
    country: null,
    city: null,
    street_address: null,
    latitude: null,
    longitude: null,
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
        title.value = newVal < 0 ? 'New Company Address' : 'Edit Company Address';

        editedItem.value = {
            ...defaultItem,
        };

        if (newVal >= 0) {
            const companyAddress = companyAddressStore.company_addresses[newVal];
            if (companyAddress) {
                editedItem.value = {
                    ...companyAddress,
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
            await companyAddressStore.update(props.companyId, props.editedIndex, itemToSubmit);
            toast.success('You have successfully edited the item!');
        } else {
            await companyAddressStore.store(props.companyId, itemToSubmit)
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
    { model: 'country', component: 'v-text-field', props: { label: 'Country', error: !!formErrors.value.country, 'error-messages': formErrors.value.country || [] } },
    { model: 'city', component: 'v-text-field', props: { label: 'City', error: !!formErrors.value.city, 'error-messages': formErrors.value.city || [] } },
    { model: 'street_address', component: 'v-text-field', props: { label: 'Street Address', error: !!formErrors.value.street_address, 'error-messages': formErrors.value.street_address || [] } },
    { model: 'latitude', component: 'v-text-field', props: { label: 'Latitude', error: !!formErrors.value.latitude, 'error-messages': formErrors.value.latitude || [] } },
    { model: 'longitude', component: 'v-text-field', props: { label: 'Longitude', error: !!formErrors.value.longitude, 'error-messages': formErrors.value.longitude || [] } },
]);

</script>
