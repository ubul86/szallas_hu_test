import { defineStore } from 'pinia';
import companyEmployeeService from "@/services/company.employee.service.js";
export const useCompanyEmployeeStore = defineStore('companyEmployee', {
    state: () => ({
        company_employees: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchItems(companyId, params) {
            const data = await companyEmployeeService.fetchItems(companyId, params);
            this.company_employees = data.items;
            this.meta = data.meta;
        },

        async show(companyId, id) {
            const existingItem = this.company_employees.find(company_employee => company_employee.id === id);
            if (existingItem) {
                return existingItem;
            }

            const fetchedItem = await companyEmployeeService.show(companyId, id);
            this.company_employees.push(fetchedItem);
            this.meta.total_items += 1;
            return fetchedItem;
        },

        async store(companyId, item) {
            const storedItem = await companyEmployeeService.store(companyId, item);
            this.company_employees.push(storedItem);
        },

        async update(companyId, index, item) {
            this.company_employees[index] = await companyEmployeeService.update(companyId, item);
        },

        async deleteItem(companyId, id) {
            await companyEmployeeService.deleteItem(companyId, id);
            this.company_employees = [...this.company_employees.filter(company_employee => company_employee.id !== id)];
        },
    },
});
