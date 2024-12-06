import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import { createPinia } from 'pinia';
import axios from './plugins/axios'
import formatDate from './plugins/date_formatter'
import Toast from './plugins/toast'
import debounce from './plugins/debounce'
import filesize from './plugins/filesize'

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(axios, {
                baseUrl: import.meta.env.APP_URL,
            })
            .use(formatDate)
            .use(Toast)
            .use(debounce)
            .use(filesize)
            .mount(el)
    },
});