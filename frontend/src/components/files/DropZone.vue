<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { useFilesStore } from '@/stores/files'
import { ACCEPTED_MIME_TYPES } from '@/lib/file-utils'

const MAX_FILES = 5

const store = useFilesStore()
const pendingFiles = ref<File[]>([])
const isDragging = ref(false)

const acceptAttribute = ACCEPTED_MIME_TYPES.join(',')

function addFiles(incoming: FileList | null): void {
  if (!incoming) return
  const available = MAX_FILES - pendingFiles.value.length
  const toAdd = Array.from(incoming).slice(0, available)
  pendingFiles.value = [...pendingFiles.value, ...toAdd]
}

function removeFile(index: number): void {
  pendingFiles.value = pendingFiles.value.filter((_, i) => i !== index)
}

function onDrop(event: DragEvent): void {
  isDragging.value = false
  addFiles(event.dataTransfer?.files ?? null)
}

async function handleUpload(): Promise<void> {
  if (pendingFiles.value.length === 0) return
  await store.uploadFiles(pendingFiles.value)
  pendingFiles.value = []
}
</script>

<template>
  <div class="rounded-xl border-2 border-dashed p-6 transition-colors"
    :class="isDragging ? 'border-primary bg-primary/5' : 'border-border bg-card'"
    @dragover.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @drop.prevent="onDrop"
  >
    <div class="flex flex-col items-center gap-3 text-center">
      <p class="text-sm text-muted-foreground">
        Drag &amp; drop files here, or
        <label class="cursor-pointer font-medium text-foreground underline-offset-2 hover:underline">
          browse
          <input
            type="file"
            multiple
            :accept="acceptAttribute"
            class="sr-only"
            @change="addFiles(($event.target as HTMLInputElement).files)"
          />
        </label>
      </p>
      <p class="text-xs text-muted-foreground">PDF, DOCX, PNG, JPG, ODT — up to {{ MAX_FILES }} files</p>
    </div>

    <div v-if="pendingFiles.length > 0" class="mt-4 flex flex-wrap gap-2">
      <span
        v-for="(file, i) in pendingFiles"
        :key="i"
        class="flex items-center gap-1.5 rounded-full border bg-muted px-3 py-1 text-xs font-medium"
      >
        {{ file.name }}
        <button
          type="button"
          class="text-muted-foreground hover:text-foreground"
          @click="removeFile(i)"
        >
          ✕
        </button>
      </span>
    </div>

    <div class="mt-4 flex justify-end">
      <Button
        :disabled="pendingFiles.length === 0 || store.uploading"
        @click="handleUpload"
      >
        {{ store.uploading ? 'Uploading…' : `Upload${pendingFiles.length > 0 ? ` (${pendingFiles.length})` : ''}` }}
      </Button>
    </div>
  </div>
</template>
