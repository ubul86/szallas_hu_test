import { ref } from 'vue';

export default function useDialogForm(defaultItem, storeActions, toast, resetErrors, handleApiError) {
    const isLoading = ref(false);
    const localDialogVisible = ref(false);
    const editedItem = ref({ ...defaultItem });

    const openDialog = (visible, editedIndex, getDataCallback) => {
        localDialogVisible.value = visible;
        if (editedIndex >= 0 && getDataCallback) {
            const data = getDataCallback(editedIndex);
            if (data) {
                editedItem.value = { ...defaultItem, ...data };
                console.log(editedItem.value)
            }
        } else {
            editedItem.value = { ...defaultItem };
        }
    };

    const handleCancel = (resetCallback) => {
        localDialogVisible.value = false;
        editedItem.value = { ...defaultItem };
        if (resetCallback) resetCallback();
    };

    const handleSubmit = async (editedIndex, itemToSubmit, closeCallback) => {
        resetErrors();
        isLoading.value = true;
        try {
            if (editedIndex > -1) {
                await storeActions.update(editedIndex, itemToSubmit);
                toast.success('You have successfully edited the item!');
            } else {
                await storeActions.store(itemToSubmit);
                toast.success('You have successfully created a new item!');
            }
            localDialogVisible.value = false;
            editedItem.value = { ...defaultItem };
            if (closeCallback) closeCallback();
        } catch (error) {
            handleApiError(error);
            toast.error(error.response?.data?.message || 'An error occurred.');
        } finally {
            isLoading.value = false;
        }
    };

    return {
        isLoading,
        localDialogVisible,
        editedItem,
        openDialog,
        handleCancel,
        handleSubmit,
    };
}
