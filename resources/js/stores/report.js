import { defineStore } from "pinia"
import { ref, inject } from "vue"

export const useReportStore = defineStore('report-store', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;

    // Get report states
    const data = ref({});
    const getReportStatus = ref("");

    // Generate pdf states
    const generatePdfStatus = ref("");

    // Error message state
    const errorMessage = ref("");

    async function getPurchaseReport(startDate, endDate) {
        try {
            getReportStatus.value = "loading";

            const response = await axios.get("/api/report/purchase", {
                headers: { Authorization: token },
                params: {
                    start_date: startDate,
                    end_date: endDate,
                },
            });

            data.value = response.data.result;

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            getReportStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            getReportStatus.value = "error";
            errorMessage.value = errorText;

            return {};
        }
    }

    async function getSalesReport(startDate, endDate) {
        try {
            getReportStatus.value = "loading";

            const response = await axios.get("/api/report/sales", {
                headers: { Authorization: token },
                params: {
                    start_date: startDate,
                    end_date: endDate,
                },
            });

            data.value = response.data.result;

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            getReportStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            getReportStatus.value = "error";
            errorMessage.value = errorText;

            return {};
        }
    }

    async function generatePdf(content) {
        try {
            generatePdfStatus.value = "loading";

            let formData = new FormData();
            formData.append("content", content);

            const response = await axios.post("/api/report-pdf",
                formData,
                {
                    headers: {
                        Authorization: token,
                        Accept: "application/pdf"
                    },
                }
            );

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            generatePdfStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            generatePdfStatus.value = "error";
            errorMessage.value = errorText;

            return {};
        }
    }


    function clearReportStore() {
        data.value = {}
        getReportStatus.value = ""
        errorMessage.value = ""
    }

    return {
        data,
        getReportStatus,
        generatePdfStatus,
        errorMessage,
        getPurchaseReport,
        getSalesReport,
        generatePdf,
        clearReportStore,
    }
})