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

    show(id) {
        return publicApi
            .get(`/company-address/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    store(item) {
        return publicApi.post(`/company-address`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    update(item) {
        return publicApi.put(`/company-address/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    deleteItem(id) {
        return publicApi
            .delete(`/company-address/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new CompanyAddressService();
