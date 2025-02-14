import { defineStore } from 'pinia';
import companyService from '@/services/company.service.js';
export const useCompanyStore = defineStore('company', {
    state: () => ({
        companies: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchCompanies(params) {
            const data = await companyService.fetchCompanies(params);
            this.companies = data.items;
            this.meta = data.meta;
        },

        async show(id) {
            const existingItem = this.companies.find(company => company.id === id);
            if (existingItem) {
                return existingItem;
            }

            const fetchedItem = await companyService.show(id);
            this.companies.push(fetchedItem);
            this.meta.total_items += 1;
            return fetchedItem;
        },

        async store(item) {
            const storedItem = await companyService.store(item);
            this.companies.push(storedItem);
        },

        async update(index, item) {
            this.companies[index] = await companyService.update(item);
        },

        async deleteItem(id) {
            await companyService.deleteItem(id);
            this.companies = this.companies.filter(company => company.id !== id);
        },
    },
});
