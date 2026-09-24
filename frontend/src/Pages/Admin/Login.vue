<script setup>
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: null })

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/admin/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <div class="admin-login">
    <form class="admin-login__card" @submit.prevent="submit">
      <p class="admin-eyebrow">WG Shop</p>
      <h1 class="admin-login__title">Admin sign in</h1>

      <div v-if="Object.keys(form.errors).length" class="admin-alert admin-alert--danger">
        {{ Object.values(form.errors)[0] }}
      </div>

      <label class="admin-label" for="email">E-Mail Address</label>
      <input id="email" v-model="form.email" type="email" class="admin-input" required autofocus />

      <label class="admin-label" for="password">Password</label>
      <input id="password" v-model="form.password" type="password" class="admin-input" required />

      <label class="admin-login__remember">
        <input v-model="form.remember" type="checkbox" />
        <span>Remember Me</span>
      </label>

      <button class="admin-btn admin-btn--primary admin-login__submit" type="submit" :disabled="form.processing">
        {{ form.processing ? 'Signing in…' : 'Login' }}
      </button>

      <a class="admin-login__link" href="/admin/password/reset">Forgot Your Password?</a>
    </form>
  </div>
</template>

<style scoped>
/* .admin-login / .admin-login__card live in admin.css (the Blade password-reset
   pages reuse them). Only the bits unique to this page are scoped here. */
.admin-login__title {
  margin: 0 0 1.25rem;
  font-size: 1.5rem;
  color: #16324f;
}

.admin-login__remember {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0.75rem 0 1.25rem;
  font-size: 0.875rem;
  color: #334155;
}

.admin-login__submit {
  width: 100%;
  justify-content: center;
}

.admin-login__link {
  margin-top: 1rem;
  align-self: center;
  font-size: 0.8rem;
  color: #0284c7;
}
</style>
