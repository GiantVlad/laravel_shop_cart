import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import { definePreset } from '@primevue/themes'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

// Aura ships an emerald primary; the shop UI uses a sky-blue accent (#0284c7).
// Align the theme so PrimeVue components (paginator, buttons, focus rings) match.
const ShopPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '#f0f9ff',
            100: '#e0f2fe',
            200: '#bae6fd',
            300: '#7dd3fc',
            400: '#38bdf8',
            500: '#0ea5e9',
            600: '#0284c7',
            700: '#0369a1',
            800: '#075985',
            900: '#0c4a6e',
            950: '#082f49',
        },
    },
})

import 'primeicons/primeicons.css'
import 'primeflex/primeflex.css'
import './assets/app.css'
import './assets/auth.css'
import './assets/admin.css'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// CSRF is deliberately NOT set from the meta tag here.
//
// A static 'X-CSRF-TOKEN' header is captured once at boot, but Inertia never
// re-runs this file, so the header goes stale the moment the session is
// migrated (logging in/out rotates the token) and every later POST fails with
// 419 "Page Expired". VerifyCsrfToken prefers the X-CSRF-TOKEN header over the
// cookie, so the stale value wins and the fresh cookie is ignored.
//
// Instead let axios send X-XSRF-TOKEN from the XSRF-TOKEN cookie, which
// Laravel re-issues on every response - it is always current.
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'

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
                    preset: ShopPreset,
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
