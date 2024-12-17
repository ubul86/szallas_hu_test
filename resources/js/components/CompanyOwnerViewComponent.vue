<template>

    <ToggleHeaderComponent :selectedHeaders="toggleHeaders" :headers="headers" @update:selectedHeaders="toggleHeaders = $event" />

    <v-data-table
        v-model="selected"
        :headers="computedHeaders"
        show-select
        :items="localOwners"
        v-model:search="search"
        :filter-keys="['name']"
        :mobile="smAndDown"

    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Company Owners</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    density="compact"
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    variant="solo-filled"
                    flat
                    hide-details
                    single-line
                ></v-text-field>

                <v-btn color="primary" dark v-bind="props" @click="openDialog">New Company Owner</v-btn>

            </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-icon class="me-2" size="small" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon size="small" @click="dialogDelete(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
        <template v-slot:[`item.active`]="{ item }">
            <v-icon
                :color="item.active ? 'green' : 'red'"
                small
            >
                {{ item.active ? 'mdi-check' : 'mdi-close' }}
            </v-icon>
        </template>
    </v-data-table>

    <edit-company-owner-dialog-form :edited-index="editedIndex" :dialog-visible="dialog" @close="onClose" :company-id="props.companyId"></edit-company-owner-dialog-form>

    <DialogOwnerDeleteComponent
        :is-dialog-delete-open="isDialogDeleteOpen"
        @update:isDialogDeleteOpen="isDialogDeleteOpen = $event"
        :item-id="editedItemId"
        @closeDelete="closeDelete"
        :company-id="props.companyId"
    />

</template>

<script setup>
import {ref, computed, nextTick, onMounted, onBeforeUnmount} from 'vue'
import ToggleHeaderComponent from '@/components/ToggleHeaderComponent.vue'
import { useDisplay } from 'vuetify'
import EditCompanyOwnerDialogForm from "@/components/dialogs/EditCompanyOwnerDialogForm.vue";
import {useCompanyOwnerStore} from "@/stores/company.owner.store.js";
import DialogOwnerDeleteComponent from "@/components/dialogs/DialogOwnerDeleteComponent.vue";

const isMobileView = ref(window.innerWidth < 960);

const checkScreenSize = () => {
    isMobileView.value = window.innerWidth < 960;
};

onMounted(() => {
    window.addEventListener('resize', checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreenSize);
});

const props = defineProps({
    addresses: {
        type: Array,
        default: () => []
    },
    companyId: Number,
});

const { smAndDown } = useDisplay()

const dialog = ref(false)

const companyOwnerStore = useCompanyOwnerStore();

const localOwners = computed(() => companyOwnerStore.company_owners);

const headers = [
    {
        title: 'Company Owner ID',
        align: 'start',
        key: 'id',
    },
    { title: 'Name', key: 'name', sortable: false },
    { title: 'Active', key: 'active', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false },
]

const selected = ref([])

const editedIndex = ref(-1)
const editedItemId = ref(null);

const search = ref('');

const toggleHeaders = ref(['id','name', 'active', 'actions']);

const computedHeaders = computed(() => {
    return headers.filter(header => toggleHeaders.value.includes(header.key));
})

const openDialog = () => {
    dialog.value = true;
}

const onClose = () => {
    dialog.value = false;
    editedIndex.value = null;
    editedItemId.value = null;
}

const editItem = (item) => {
    editedIndex.value = companyOwnerStore.company_owners.indexOf(item);
    editedItemId.value = item.id;
    dialog.value = true
}

const isDialogDeleteOpen = ref(false);

const dialogDelete = (item) => {
    isDialogDeleteOpen.value = true;
    editedIndex.value = companyOwnerStore.company_owners.indexOf(item)
    editedItemId.value = item.id;
};

const closeDelete = async() => {
    isDialogDeleteOpen.value = false;
    await nextTick();
    editedItemId.value = null;
    editedIndex.value = -1
};

</script>

<style>

.v-data-table__tr:nth-child(odd) {
    background-color: #e5e7eb;
}

.v-data-table__tr:nth-child(even) {
    background-color: #ffffff;
}

.used-time-container {
    align-items: center;
}

@media (max-width: 960px) {
    .used-time-container {
        align-items: flex-end;
    }
}

</style>
