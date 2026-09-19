<template>
  <div class="login-popup">
    <button @click="close" class="login-close" aria-label="Close">×</button>
    <form @submit.prevent="submit" class="login-form">
      <h2>Login</h2>
      <label>Email</label>
      <input v-model="form.email" type="email" required />
      <label>Password</label>
      <input v-model="form.password" type="password" required />
      <button type="submit" class="btn-login">Login</button>
    </form>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
export default {
  data() {
    return { form: { email: '', password: '' } };
  },
  methods: {
    close() { router.visit('/shop'); },
    submit() {
      router.post('/login', this.form);
    }
  }
};
</script>

<style scoped>
.login-popup {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  display: flex; align-items: center; justify-content: center;
}
.login-close { position: absolute; top: 1rem; right: 1.5rem; font-size: 2rem; color: #fff; background: none; border: none; cursor: pointer; }
.login-form { background: #fff; padding: 2rem; border-radius: 1rem; width: 320px; }
.login-form h2 { margin-bottom: 1rem; }
.login-form label { display: block; margin-top: 0.75rem; font-size: 0.85rem; }
.login-form input { width: 100%; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #ccc; }
.btn-login { margin-top: 1rem; width: 100%; padding: 0.6rem; background: #1c1a17; color: #fff; border: none; border-radius: 0.5rem; font-weight: 600; }
</style>
