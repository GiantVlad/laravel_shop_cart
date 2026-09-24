<template>
  <div class="auth-page px-3 py-6 flex align-items-center justify-content-center">
    <div class="auth-card border-1 border-surface-200 border-round-2xl p-5 shadow-1 w-full">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="inline-flex align-items-center justify-content-center bg-primary-50 border-circle mb-3" style="width: 52px; height: 52px;">
          <i class="pi pi-user text-primary text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-900 m-0 mb-1">Sign in to your account</h2>
        <p class="text-500 text-sm m-0">Enter your credentials to continue</p>
      </div>

      <!-- General / Flash Error -->
      <div v-if="errors && Object.keys(errors).length" class="p-3 mb-4 bg-red-50 border-round-xl border-1 border-red-200 text-red-700 text-xs">
        <ul class="m-0 pl-3">
          <li v-for="(err, key) in errors" :key="key">{{ err }}</li>
        </ul>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="flex flex-column gap-3">
        <!-- Email -->
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

        <!-- Password -->
        <div>
          <div class="flex align-items-center justify-content-between mb-1">
            <label for="password" class="text-sm font-semibold text-900">Password</label>
            <a href="/password/reset" class="text-xs text-primary no-underline hover:underline font-medium">Forgot?</a>
          </div>
          <div class="input-icon-wrapper">
            <i class="pi pi-lock input-icon"></i>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="form-input"
              :class="{ 'input-error': errors?.password }"
            />
          </div>
          <small v-if="errors?.password" class="text-red-500 text-xs mt-1 block">{{ errors.password }}</small>
        </div>

        <!-- Remember Me -->
        <div class="flex align-items-center gap-2 my-1">
          <input
            id="remember"
            v-model="form.remember"
            type="checkbox"
            class="cursor-pointer"
            style="width: 1rem; height: 1rem; accent-color: #0284c7;"
          />
          <label for="remember" class="text-sm text-600 cursor-pointer user-select-none">Remember me</label>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="submit-btn"
        >
          <i v-if="loading" class="pi pi-spin pi-spinner mr-2"></i>
          <span>{{ loading ? 'Signing in...' : 'Sign in' }}</span>
        </button>
      </form>

      <!-- Footer / Switch to Register -->
      <div class="text-center mt-4 pt-3 border-top-1 border-surface-200">
        <p class="text-sm text-500 m-0">
          Don't have an account?
          <Link href="/register" class="text-primary font-semibold no-underline hover:underline ml-1">
            Sign up
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
  name: 'Login',
  components: { Link },
  layout: Layout,
  props: {
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false,
      },
      loading: false,
    }
  },
  methods: {
    submit() {
      this.loading = true
      router.post('/login', this.form, {
        onFinish: () => {
          this.loading = false
        }
      })
    }
  }
}
</script>
