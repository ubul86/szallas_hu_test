<template>

    <ToggleHeaderComponent :selectedHeaders="toggleHeaders" :headers="headers" @update:selectedHeaders="toggleHeaders = $event" />

    <EditCompanyDialogForm :edited-index="editedIndex" :dialog-visible="dialog" @close="close" />

    <v-data-table-server
        v-model="selected"
        :headers="computedHeaders"
        show-select
        :items="companyStore.companies"
        v-model:search="search"
        :filter-keys="['name', 'activity']"
        :mobile="smAndDown"
        @update:options="loadItems"
        :items-per-page="companyStore.meta.items_per_page"
        :items-length="companyStore.meta.total_items"
        :loading="tableLoadingItems"

    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Companies</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-checkbox
                    v-model="filterIsActive"
                    color="green"
                    label="Active Companies"
                    :value="1"
                    hide-details
                ></v-checkbox>

                <v-checkbox
                    v-model="filterIsActive"
                    color="red"
                    label="Inactive Companies"
                    :value="0"
                    hide-details
                ></v-checkbox>
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

                <v-btn color="primary" dark v-bind="props" @click="openDialog">New Company</v-btn>

            </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-icon
                size="small"
                class="ms-2"
                @click="goToCompany(item.id)"
            >
                mdi-eye
            </v-icon>
            <v-icon size="small" @click="dialogDelete(item, index)">mdi-delete</v-icon>
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
        <template v-slot:[`item.address`]="{item}">
            {{ companyFirstAddress(item) }}
        </template>
        <template v-slot:[`item.company_owner`]="{item}">
            {{ companyFirstOwner(item) }}
        </template>
        <template v-slot:[`item.employees`]="{item}">
            {{ countedEmployees(item) }}
        </template>
    </v-data-table-server>

    <DialogDeleteComponent
        :is-dialog-delete-open="isDialogDeleteOpen"
        @update:isDialogDeleteOpen="isDialogDeleteOpen = $event"
        :item-id="editedItem?.id"
        @closeDelete="closeDelete"
    />

</template>

<script setup>
import { ref, reactive, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import DialogDeleteComponent from '@/components/dialogs/DialogDeleteComponent.vue'
import ToggleHeaderComponent from '@/components/ToggleHeaderComponent.vue'
import { useDisplay } from 'vuetify'
import EditCompanyDialogForm from '@/components/dialogs/EditCompanyDialogForm.vue'
import {useCompanyStore} from "@/stores/company.store.js";
import { useRouter } from 'vue-router';

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

const { smAndDown } = useDisplay()

const router = useRouter();

const dialog = ref(false)

const companyStore = useCompanyStore();

const headers = [
    {
        title: 'Company ID',
        align: 'start',
        key: 'id',
    },
    { title: 'Name', key: 'name', sortable: false },
    { title: 'Reg Num.', key: 'registration_number' },
    { title: 'Foundation Date', key: 'foundation_date' },
    { title: 'Address', key: 'address' },
    { title: 'Company Owner', key: 'company_owner' },
    { title: 'Employees', key: 'employees' },
    { title: 'Activity', key: 'activity' },
    { title: 'Active', key: 'active', align: 'center'},
    { title: 'Created At', key: 'created_at' },
    { title: 'Updated At', key: 'updated_at' },
    { title: 'Actions', key: 'actions', sortable: false },
]

const selected = ref([])

const editedIndex = ref(-1)
const editedItem = reactive({
    name: '',
    registration_number: '',
    foundation_date: null,
    activity: null,
    active: false,
})

const defaultItem = {
    name: '',
    registration_number: '',
    foundation_date: null,
    activity: null,
    active: false,
}

const tableLoadingItems = ref(true);

const search = ref('');

const filterIsActive = ref([0, 1]);

const toggleHeaders = ref(['id','name', 'registration_number', 'foundation_date','address', 'company_owner','employees', 'activity', 'active', 'created_at', 'updated_at', 'actions']);

const computedHeaders = computed(() => {
    return headers.filter(header => toggleHeaders.value.includes(header.key));
})

const tableParams = reactive({
    page: 1,
    itemsPerPage: 10,
    sortBy: [],
    search: '',
    additionalFilters: {},
});

const loadItems = async (params) => {

    tableLoadingItems.value = true;

    Object.assign(tableParams, params);

    const filters = {};

    filters.active = ['none'];

    if (filterIsActive.value.length) {
        filters.active = filterIsActive.value;
    }

    const combinedParams = {
        ...tableParams,
        filters
    };

    await companyStore.fetchCompanies(combinedParams)
    tableLoadingItems.value = false;
}

const companyFirstAddress = (item) => {
    const firstAddress = item.addresses && item.addresses.length ? item.addresses[0] : [];
    let concatenateAddress = '';
    if (firstAddress) {
        concatenateAddress = `${firstAddress.country} - ${firstAddress.city} - ${firstAddress.zip_code} - ${firstAddress.street_address}`
    }
    return concatenateAddress ?? 'N/A';
};

const companyFirstOwner = (item) => {
    const firstOwner = item.owners && item.owners.length ? item.owners[0] : [];
    return firstOwner?.name;
};

const countedEmployees = (item) => {
    return item.employees.length;
}

const openDialog = () => {
    dialog.value = true;
}

const close = async () => {
    dialog.value = false
    editedIndex.value = -1
}

const isDialogDeleteOpen = ref(false);

const dialogDelete = (item) => {
    isDialogDeleteOpen.value = true;
    editedIndex.value = companyStore.companies.indexOf(item)
    Object.assign(editedItem, item)
};

const closeDelete = async() => {
    isDialogDeleteOpen.value = false;
    await nextTick();
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
};

const goToCompany = (id) => {
    router.push({ name: 'CompanyView', params: { id } });
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
