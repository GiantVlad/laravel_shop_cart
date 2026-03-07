<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    title: {
        type: String,
        default: 'Admin',
    },
})

const page = usePage()

const navigation = [
    { label: 'Dashboard', href: '/admin' },
    { label: 'Categories', href: '/admin/categories' },
    { label: 'Products', href: '/admin/products' },
    { label: 'Users', href: '/admin/users' },
    { label: 'Orders', href: '/admin/orders' },
    { label: 'Payment Methods', href: '/admin/payment-methods' },
    { label: 'Shipping', href: '/admin/shipping-methods' },
]

const flashMessage = computed(() => page.props.flash?.message)
const flashError = computed(() => page.props.flash?.error)
const adminName = computed(() => page.props.auth?.admin?.name ?? 'Admin')
</script>

<template>
    <Head :title="title" />

    <div class="admin-shell">
        <header class="admin-topbar">
            <div>
                <p class="admin-eyebrow">WG Shop</p>
                <h1 class="admin-title">{{ title }}</h1>
            </div>

            <div class="admin-user">
                <span>{{ adminName }}</span>
                <a class="btn btn-sm btn-outline-light" href="/admin/logout">Logout</a>
            </div>
        </header>

        <div class="admin-body">
            <aside class="admin-sidebar">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="admin-nav-link"
                    :class="{ 'is-active': $page.url === item.href }"
                >
                    {{ item.label }}
                </Link>
            </aside>

            <main class="admin-content">
                <div v-if="flashMessage" class="alert alert-success admin-alert">
                    {{ flashMessage }}
                </div>
                <div v-if="flashError" class="alert alert-danger admin-alert">
                    {{ flashError }}
                </div>

                <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger admin-alert">
                    <div v-for="(message, key) in $page.props.errors" :key="key">
                        {{ message }}
                    </div>
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.admin-shell {
    min-height: 100vh;
    background:
        radial-gradient(circle at top left, rgba(13, 110, 253, 0.16), transparent 32%),
        linear-gradient(180deg, #f3f6fb 0%, #eef3f7 100%);
    color: #16324f;
}

.admin-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #0b2545 0%, #134074 100%);
    color: #f8fbff;
    box-shadow: 0 18px 40px rgba(11, 37, 69, 0.18);
}

.admin-eyebrow {
    margin: 0;
    font-size: 0.75rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    opacity: 0.72;
}

.admin-title {
    margin: 0.25rem 0 0;
    font-size: clamp(1.75rem, 2vw, 2.5rem);
    font-weight: 700;
}

.admin-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
}

.admin-body {
    display: grid;
    grid-template-columns: 240px minmax(0, 1fr);
    gap: 1.5rem;
    padding: 1.5rem 2rem 2rem;
}

.admin-sidebar {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(12px);
    box-shadow: 0 18px 50px rgba(54, 78, 108, 0.12);
}

.admin-nav-link {
    padding: 0.85rem 1rem;
    border-radius: 0.85rem;
    color: #29527a;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.admin-nav-link:hover,
.admin-nav-link.is-active {
    background: #0d6efd;
    color: #fff;
    transform: translateX(3px);
}

.admin-content {
    min-width: 0;
}

.admin-alert {
    border: 0;
    border-radius: 1rem;
}

@media (max-width: 991px) {
    .admin-body {
        grid-template-columns: 1fr;
        padding: 1rem;
    }

    .admin-sidebar {
        overflow-x: auto;
        flex-direction: row;
    }

    .admin-nav-link {
        white-space: nowrap;
    }

    .admin-topbar {
        padding: 1.25rem 1rem;
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
