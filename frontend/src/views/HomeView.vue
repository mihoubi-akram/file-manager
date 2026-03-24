<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout(): Promise<void> {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="page">
    <div class="card">
      <h1>Welcome, {{ auth.user?.name }}</h1>
      <p>{{ auth.user?.email }}</p>
      <button @click="handleLogout">Logout</button>
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
  background: #fff;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

h1 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  font-weight: 600;
}

p {
  margin: 0 0 1.5rem;
  color: #666;
  font-size: 0.9rem;
}

button {
  padding: 0.5rem 1.25rem;
  background: #111;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
}

button:hover {
  background: #333;
}
</style>
