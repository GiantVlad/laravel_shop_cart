import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

import 'primeicons/primeicons.css'
import 'primeflex/primeflex.css'
import './assets/app.css'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const csrfToken = document.head.querySelector('meta[name="csrf-token"]')

if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content
}

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: 'none'
                    }
                }
            })
            .component('Card', Card)
            .component('Button', Button)
            .component('InputText', InputText)
            .mount(el)
    },
})
