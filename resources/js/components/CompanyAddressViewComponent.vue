<template>

    <ToggleHeaderComponent :selectedHeaders="toggleHeaders" :headers="headers" @update:selectedHeaders="toggleHeaders = $event" />

    <v-data-table
        v-model="selected"
        :headers="computedHeaders"
        show-select
        :items="localAddresses"
        v-model:search="search"
        :filter-keys="['name', 'activity']"
        :mobile="smAndDown"

    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Company Addresses</v-toolbar-title>
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

                <v-btn color="primary" dark v-bind="props" @click="openDialog">New Company Address</v-btn>

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

    <edit-company-address-dialog-form :edited-index="editedIndex" :dialog-visible="dialog" @close="onClose" :company-id="props.companyId"></edit-company-address-dialog-form>

    <DialogAddressDeleteComponent
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
import EditCompanyAddressDialogForm from "@/components/dialogs/EditCompanyAddressDialogForm.vue";
import {useCompanyAddressStore} from "@/stores/company.address.store.js";
import DialogAddressDeleteComponent from "@/components/dialogs/DialogAddressDeleteComponent.vue";

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

const companyAddressStore = useCompanyAddressStore();

const localAddresses = computed(() => companyAddressStore.company_addresses);

const headers = [
    {
        title: 'Company Address ID',
        align: 'start',
        key: 'id',
    },
    { title: 'Country', key: 'country', sortable: false },
    { title: 'City', key: 'city', sortable: false },
    { title: 'Street Address', key: 'street_address' },
    { title: 'Latitude', key: 'latitude' },
    { title: 'longitude', key: 'longitude' },
    { title: 'Actions', key: 'actions', sortable: false },
]

const selected = ref([])

const editedIndex = ref(-1)
const editedItemId = ref(null);

const search = ref('');

const toggleHeaders = ref(['id','country', 'city', 'street_address','latitude', 'longitude', 'actions']);

const computedHeaders = computed(() => {
    return headers.filter(header => toggleHeaders.value.includes(header.key));
})

const openDialog = () => {
    dialog.value = true;
}

const onClose = () => {
    dialog.value = false;
    editedIndex.value = -1;
    editedItemId.value = null;
}

const editItem = (item) => {
    editedIndex.value = companyAddressStore.company_addresses.indexOf(item);
    editedItemId.value = item.id;
    dialog.value = true
}

const isDialogDeleteOpen = ref(false);

const dialogDelete = (item) => {
    isDialogDeleteOpen.value = true;
    editedIndex.value = companyAddressStore.company_addresses.indexOf(item)
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
