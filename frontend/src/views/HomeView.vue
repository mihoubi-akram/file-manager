<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useFilesStore } from '@/stores/files'
import { Button } from '@/components/ui/button'
import DropZone from '@/components/files/DropZone.vue'
import SearchInput from '@/components/files/SearchInput.vue'
import FileTable from '@/components/files/FileTable.vue'
import DeleteModal from '@/components/files/DeleteModal.vue'

const auth = useAuthStore()
const filesStore = useFilesStore()
const router = useRouter()

onMounted(() => filesStore.fetchFiles())

async function handleLogout(): Promise<void> {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <header class="sticky top-0 z-10 border-b bg-card px-6 py-3 flex items-center justify-between">
      <span class="text-sm font-semibold uppercase">{{ auth.user?.name }}</span>
      <Button variant="ghost" size="sm" @click="handleLogout">Logout</Button>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-8 space-y-6">
      <DropZone />

      <div class="flex justify-end">
        <SearchInput />
      </div>

      <FileTable />
    </main>

    <DeleteModal />
  </div>
</template>
