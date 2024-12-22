import { get } from "@vueuse/core";
import { defineStore } from "pinia"
import { ref, computed, inject } from "vue"

export const useSubscribeStore = defineStore('subscribe', () => {
    const axios = inject("axios");
    const Toast = inject("Toast");
    const token = `Bearer ${localStorage.getItem("access_token")}`;
    const midtransClientKey = ref(import.meta.env.VITE_MIDTRANS_CLIENT_KEY || '')

    const createInvoiceStatus = ref("");
    const getInvoiceDetailStatus = ref("");
    const createSnapTokenStatus = ref("");
    const createInvoicePaymentStatus = ref("");
    const errorMessage = ref("");

    const invoice = ref(null);
    const snapToken = ref(null);

    async function createInvoice(planId, quantity) {
        createInvoiceStatus.value = "loading";

        try {
            const response = await axios.post("/api/invoice", {
                plan_id: planId,
                quantity,
            }, {
                headers: { Authorization: token },
            });

            invoice.value = response.data.result;

            createInvoiceStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            createInvoiceStatus.value = "error";
            errorMessage.value = errorText;

            return error.response?.data;
        }
    }

    async function getInvoiceDetail(invoiceId) {
        getInvoiceDetailStatus.value = "loading";

        try {
            const response = await axios.get(`/api/invoice/${invoiceId}`, {
                headers: { Authorization: token },
            });

            invoice.value = response.data.result;

            getInvoiceDetailStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            getInvoiceDetailStatus.value = "error";
            errorMessage.value = errorText;

            return error.response?.data;
        }
    }

    async function createSnapToken(invoiceId) {
        createSnapTokenStatus.value = "loading";

        try {
            const response = await axios.post("/api/subscribe/create-snap-token", {
                invoice_id: invoiceId,
            }, {
                headers: { Authorization: token },
            });

            snapToken.value = response.data.result.snap_token;
            invoice.value = response.data.result.invoice;

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            createSnapTokenStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            createSnapTokenStatus.value = "error";
            errorMessage.value = errorText;

            return error.response?.data;
        }
    }

    async function createInvoicePayment(invoiceId, paymentMethodId, amount, note) {
        createInvoicePaymentStatus.value = "loading";

        try {
            const response = await axios.post("/api/invoice-payment", {
                invoice_id: invoiceId,
                payment_method_id: paymentMethodId,
                amount: amount,
                note: note,
            }, {
                headers: { Authorization: token },
            });

            invoice.value = response.data.result.invoice;

            Toast.fire({
                icon: "success",
                title: response.data.meta.message,
            });

            createInvoicePaymentStatus.value = "success";

            return response.data;
        } catch (error) {
            const errorText = error.response?.data?.meta?.message || "Terjadi kesalahan";

            Toast.fire({
                icon: "warning",
                title: errorText,
            });

            createInvoicePaymentStatus.value = "error";
            errorMessage.value = errorText;

            return error.response?.data;
        }
    }

    return {
        createInvoiceStatus,
        createSnapTokenStatus,
        createInvoicePaymentStatus,
        errorMessage,
        createInvoice,
        getInvoiceDetail,
        createSnapToken,
        createInvoicePayment,
        midtransClientKey,
        invoice,
        snapToken,
    }
})