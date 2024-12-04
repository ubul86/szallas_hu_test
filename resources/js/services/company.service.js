import { publicApi } from "./api";

class CompanyService {
    fetchCompanies(params) {
        return publicApi
            .get("/company", {params})
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    store(item) {
        return publicApi.post(`/company`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    update(item) {
        return publicApi.put(`/company/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }

    deleteItem(id) {
        return publicApi
            .delete(`/company/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                throw error;
            });
    }
}

export default new CompanyService();
