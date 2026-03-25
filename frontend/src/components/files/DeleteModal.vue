<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { useFilesStore } from '@/stores/files'
import { computed } from 'vue'

const store = useFilesStore()
const open = computed(() => store.fileToDelete !== null)
</script>

<template>
  <Dialog :open="open" @update:open="(val) => { if (!val) store.cancelDelete() }">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Delete file</DialogTitle>
        <DialogDescription>
          Are you sure you want to delete
          <span class="font-semibold text-foreground">{{ store.fileToDelete?.name }}</span>?
          This cannot be undone.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button variant="outline" @click="store.cancelDelete()">Cancel</Button>
        <Button variant="destructive" @click="store.executeDelete()">Delete</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
