import { defineStore } from 'pinia';
import companyOwnerService from "@/services/company.owner.service.js";
export const useCompanyOwnerStore = defineStore('companyOwner', {
    state: () => ({
        company_owners: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchItems(companyId, params) {
            const data = await companyOwnerService.fetchItems(companyId, params);
            this.company_owners = data.items;
            this.meta = data.meta;
        },

        async show(companyId, id) {
            const existingItem = this.company_owners.find(company_owner => company_owner.id === id);
            if (existingItem) {
                return existingItem;
            }

            const fetchedItem = await companyOwnerService.show(companyId, id);
            this.company_owners.push(fetchedItem);
            this.meta.total_items += 1;
            return fetchedItem;
        },

        async store(companyId, item) {
            const storedItem = await companyOwnerService.store(companyId, item);
            this.company_owners.push(storedItem);
        },

        async update(companyId, index, item) {
            this.company_owners[index] = await companyOwnerService.update(companyId, item);
        },

        async deleteItem(companyId, id) {
            await companyOwnerService.deleteItem(companyId, id);
            this.company_owners = [...this.company_owners.filter(company_owner => company_owner.id !== id)];
        },
    },
});
