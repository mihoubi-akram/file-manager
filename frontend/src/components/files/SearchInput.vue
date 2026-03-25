<script setup lang="ts">
import { ref, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { useFilesStore } from '@/stores/files'

const store = useFilesStore()
const value = ref('')
let debounceTimer: ReturnType<typeof setTimeout> | null = null

watch(value, (newVal) => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    store.setSearch(newVal)
  }, 300)
})
</script>

<template>
  <Input
    v-model="value"
    type="search"
    placeholder="Search files…"
    class="w-64"
  />
</template>
