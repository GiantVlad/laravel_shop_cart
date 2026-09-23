<template>
  <div class="auth-page px-3 py-6 flex align-items-center justify-content-center">
    <div class="auth-card border-1 border-surface-200 border-round-2xl p-5 shadow-1 w-full">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="inline-flex align-items-center justify-content-center bg-primary-50 border-circle mb-3" style="width: 52px; height: 52px;">
          <i class="pi pi-user-plus text-primary text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-900 m-0 mb-1">Create your account</h2>
        <p class="text-500 text-sm m-0">It only takes a moment</p>
      </div>

      <!-- Validation errors -->
      <div v-if="errors && Object.keys(errors).length" class="p-3 mb-4 bg-red-50 border-round-xl border-1 border-red-200 text-red-700 text-xs">
        <ul class="m-0 pl-3">
          <li v-for="(err, key) in errors" :key="key">{{ err }}</li>
        </ul>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="flex flex-column gap-3">
        <div>
          <label for="name" class="block text-sm font-semibold text-900 mb-1">Name</label>
          <div class="input-icon-wrapper">
            <i class="pi pi-user input-icon"></i>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              autocomplete="name"
              placeholder="Jane Doe"
              class="form-input"
              :class="{ 'input-error': errors?.name }"
            />
          </div>
          <small v-if="errors?.name" class="text-red-500 text-xs mt-1 block">{{ errors.name }}</small>
        </div>

        <div>
          <label for="email" class="block text-sm font-semibold text-900 mb-1">Email address</label>
          <div class="input-icon-wrapper">
            <i class="pi pi-envelope input-icon"></i>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autocomplete="email"
              placeholder="name@example.com"
              class="form-input"
              :class="{ 'input-error': errors?.email }"
            />
          </div>
          <small v-if="errors?.email" class="text-red-500 text-xs mt-1 block">{{ errors.email }}</small>
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-900 mb-1">Password</label>
          <div class="input-icon-wrapper">
            <i class="pi pi-lock input-icon"></i>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="new-password"
              placeholder="At least 6 characters"
              class="form-input"
              :class="{ 'input-error': errors?.password }"
            />
          </div>
          <small v-if="errors?.password" class="text-red-500 text-xs mt-1 block">{{ errors.password }}</small>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-semibold text-900 mb-1">Confirm password</label>
          <div class="input-icon-wrapper">
            <i class="pi pi-lock input-icon"></i>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              autocomplete="new-password"
              placeholder="Repeat your password"
              class="form-input"
            />
          </div>
        </div>

        <button type="submit" :disabled="loading" class="submit-btn">
          <i v-if="loading" class="pi pi-spin pi-spinner mr-2"></i>
          <span>{{ loading ? 'Creating account...' : 'Sign up' }}</span>
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-4 pt-3 border-top-1 border-surface-200">
        <p class="text-sm text-500 m-0">
          Already have an account?
          <Link href="/login" class="text-primary font-semibold no-underline hover:underline ml-1">
            Sign in
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, router } from '@inertiajs/vue3'
import Layout from '../Layouts/AppLayout.vue'

export default {
  name: 'Register',
  components: { Link },
  layout: Layout,
  props: {
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      loading: false,
    }
  },
  methods: {
    submit() {
      this.loading = true
      router.post('/register', this.form, {
        onFinish: () => {
          this.loading = false
        },
      })
    },
  },
}
</script>
