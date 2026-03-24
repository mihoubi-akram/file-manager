<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import type { LoginPayload, ValidationErrors } from '@/types/auth'
import { isAxiosError } from 'axios'

const router = useRouter()
const auth = useAuthStore()

const form = reactive<LoginPayload>({
  email: '',
  password: '',
})

const errors = ref<ValidationErrors>({})
const submitting = ref(false)

async function handleSubmit(): Promise<void> {
  errors.value = {}
  submitting.value = true

  try {
    await auth.login(form)
    router.push({ name: 'home' })
  } catch (e) {
    if (isAxiosError(e) && e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Sign in</h1>

      <form @submit.prevent="handleSubmit" novalidate>
        <div class="field">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            autocomplete="email"
            :class="{ error: errors.email }"
          />
          <span v-if="errors.email" class="error-msg">{{ errors.email[0] }}</span>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            :class="{ error: errors.password }"
          />
          <span v-if="errors.password" class="error-msg">{{ errors.password[0] }}</span>
        </div>

        <button type="submit" :disabled="submitting">
          {{ submitting ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>

      <p class="footer">
        No account?
        <RouterLink :to="{ name: 'register' }">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
}

.card {
  width: 100%;
  max-width: 360px;
  background: #fff;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

h1 {
  margin: 0 0 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.field {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

label {
  font-size: 0.85rem;
  font-weight: 500;
  margin-bottom: 0.35rem;
  color: #333;
}

input {
  padding: 0.55rem 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.15s;
}

input:focus {
  border-color: #111;
}

input.error {
  border-color: #e53e3e;
}

.error-msg {
  margin-top: 0.3rem;
  font-size: 0.8rem;
  color: #e53e3e;
}

button {
  width: 100%;
  margin-top: 0.5rem;
  padding: 0.6rem;
  background: #111;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.15s;
}

button:hover:not(:disabled) {
  background: #333;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.footer {
  margin: 1.25rem 0 0;
  font-size: 0.85rem;
  color: #666;
  text-align: center;
}

.footer a {
  color: #111;
  font-weight: 500;
  text-decoration: none;
}

.footer a:hover {
  text-decoration: underline;
}
</style>
