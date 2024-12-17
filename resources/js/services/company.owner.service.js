import { publicApi } from "./api";

class CompanyOwnerService {
    fetchItems(companyId, params) {
        return publicApi
            .get(`/company/${companyId}/company-owner`, {params})
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    show(companyId, id) {
        return publicApi
            .get(`/company/${companyId}/company-owner/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    store(companyId, item) {
        return publicApi.post(`/company/${companyId}/company-owner`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    update(companyId, item) {
        return publicApi.put(`/company/${companyId}/company-owner/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    deleteItem(companyId, id) {
        return publicApi
            .delete(`/company/${companyId}/company-owner/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new CompanyOwnerService();
