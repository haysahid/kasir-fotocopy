import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useConfigStore = defineStore('config', () => {
    const title = ref(import.meta.env.VITE_APP_NAME || 'Mari Kasir')
    const copyrightYear = ref(import.meta.env.VITE_COPYRIGHT_YEAR || 2024)
    const copyrightName = ref(import.meta.env.VITE_COPYRIGHT_NAME || 'ISOKODING')
    const copyrightUrl = ref(import.meta.env.VITE_COPYRIGHT_URL)
    const supportedBy = ref(import.meta.env.VITE_SUPPORTED_BY)
    const supportedByUrl = ref(import.meta.env.VITE_SUPPORTED_BY_URL)

    const imagePrefix = ref(`${import.meta.env.VITE_APP_URL}/storage/`)
    const getImageUrl = (path) => `${imagePrefix.value}${path}`

    const months = ref([
        { value: 1, text: 'Januari' },
        { value: 2, text: 'Februari' },
        { value: 3, text: 'Maret' },
        { value: 4, text: 'April' },
        { value: 5, text: 'Mei' },
        { value: 6, text: 'Juni' },
        { value: 7, text: 'Juli' },
        { value: 8, text: 'Agustus' },
        { value: 9, text: 'September' },
        { value: 10, text: 'Oktober' },
        { value: 11, text: 'November' },
        { value: 12, text: 'Desember' }
    ])

    const getMonthText = (number) => months.value[number - 1]?.text || '-'

    return {
        title,
        copyrightYear,
        copyrightName,
        copyrightUrl,
        supportedBy,
        supportedByUrl,
        months,
        getMonthText,
        imagePrefix,
        getImageUrl,
    }
})
