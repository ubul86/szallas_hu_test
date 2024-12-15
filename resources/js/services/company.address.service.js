import { publicApi } from "./api";

class CompanyAddressService {
    fetchItems(companyId, params) {
        return publicApi
            .get(`/company/${companyId}/company-address`, {params})
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    show(companyId, id) {
        return publicApi
            .get(`/company/${companyId}/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    store(companyId, item) {
        return publicApi.post(`/company/${companyId}/company-address`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    update(companyId, item) {
        return publicApi.put(`/company/${companyId}/company-address/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    deleteItem(companyId, id) {
        return publicApi
            .delete(`/company/${companyId}/company-address/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new CompanyAddressService();
