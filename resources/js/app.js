import 'aos/dist/aos.css';
import 'flatpickr/dist/flatpickr.min.css';
import "vue-awesome-paginate/dist/style.css";

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import { createPinia } from 'pinia';
import axios from './plugins/axios';
import formatDate from './plugins/date_formatter'
import formatCurrency from './plugins/currency_formatter'
import Toast from './plugins/toast'
import debounce from './plugins/debounce'
import filesize from './plugins/filesize'
import VueAwesomePaginate from 'vue-awesome-paginate'

import AOS from 'aos';

AOS.init();

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(axios, {
                baseUrl: null,
            })
            .use(Toast)
            .use(debounce)
            .use(filesize)
            .use(VueAwesomePaginate)
            ;

        app.mixin({ methods: { route } })

        app.config.globalProperties.$formatCurrency = formatCurrency;
        app.config.globalProperties.$formatDate = formatDate;

        app.mount(el)
    },
});