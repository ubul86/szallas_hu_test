import { defineStore } from 'pinia';
import companyAddressService from "@/services/company.address.service.js";
export const useCompanyAddressStore = defineStore('companyAddress', {
    state: () => ({
        company_addresses: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchItems(companyId, params) {
            const data = await companyAddressService.fetchItems(companyId, params);
            this.company_addresses = data.items;
            this.meta = data.meta;
        },

        async show(id) {
            const existingItem = this.company_addresses.find(companyAddress => companyAddress.id === id);
            if (existingItem) {
                return existingItem;
            }

            const fetchedItem = await companyAddressService.show(id);
            this.company_addresses.push(fetchedItem);
            this.meta.total_items += 1;
            return fetchedItem;
        },

        async store(item) {
            const storedItem = await companyAddressService.store(item);
            this.company_addresses.push(storedItem);
        },

        async update(index, item) {
            this.company_addresses[index] = await companyAddressService.update(item);
        },

        async deleteItem(id) {
            await companyAddressService.deleteItem(id);
            this.company_addresses = this.company_addresses.filter(companyAddress => companyAddress.id !== id);
        },
    },
});
