<script setup lang="ts">
import { computed } from 'vue'
import {
  Table,
  TableBody,
  TableCell,
  TableEmpty,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { useFilesStore } from '@/stores/files'
import { getBadgeClass, getMimeLabel, formatBytes, formatDate } from '@/lib/file-utils'

const store = useFilesStore()

const visiblePages = computed<(number | null)[]>(() => {
  const total = store.meta?.last_page ?? 1
  const current = store.meta?.current_page ?? 1

  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  const pages: (number | null)[] = [1]

  if (current > 3) pages.push(null)

  for (let p = Math.max(2, current - 1); p <= Math.min(total - 1, current + 1); p++) {
    pages.push(p)
  }

  if (current < total - 2) pages.push(null)

  pages.push(total)
  return pages
})
</script>

<template>
  <div class="rounded-xl border bg-card" :class="{ 'opacity-60 pointer-events-none': store.loading }">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Name</TableHead>
          <TableHead>Size</TableHead>
          <TableHead>Type</TableHead>
          <TableHead>Uploaded</TableHead>
          <TableHead class="text-right">Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableEmpty v-if="store.files.length === 0" :colspan="5">
          No files uploaded yet.
        </TableEmpty>
        <TableRow v-for="file in store.files" :key="file.id">
          <TableCell class="font-medium max-w-xs truncate">{{ file.name }}</TableCell>
          <TableCell class="text-muted-foreground">{{ formatBytes(file.size) }}</TableCell>
          <TableCell>
            <Badge :class="getBadgeClass(file.mime_type)" variant="outline" class="border-0">
              {{ getMimeLabel(file.mime_type) }}
            </Badge>
          </TableCell>
          <TableCell class="text-muted-foreground">{{ formatDate(file.created_at) }}</TableCell>
          <TableCell class="text-right">
            <div class="flex justify-end gap-2">
              <Button variant="ghost" size="sm" @click="store.downloadFile(file)">
                Download
              </Button>
              <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive" @click="store.confirmDelete(file)">
                Delete
              </Button>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <div v-if="store.meta" class="flex items-center justify-between border-t px-4 py-3">
      <p class="text-sm text-muted-foreground">
        {{ store.meta.total }} files
      </p>

      <div v-if="store.meta.last_page > 1" class="flex items-center gap-1">
        <Button
          variant="outline"
          size="sm"
          :disabled="store.meta.current_page === 1"
          @click="store.setPage(store.meta!.current_page - 1)"
        >
          Previous
        </Button>

        <template v-for="(page, i) in visiblePages" :key="i">
          <span v-if="page === null" class="px-2 text-sm text-muted-foreground">…</span>
          <Button
            v-else
            :variant="page === store.meta!.current_page ? 'default' : 'outline'"
            size="sm"
            @click="store.setPage(page)"
          >
            {{ page }}
          </Button>
        </template>

        <Button
          variant="outline"
          size="sm"
          :disabled="store.meta.current_page === store.meta.last_page"
          @click="store.setPage(store.meta!.current_page + 1)"
        >
          Next
        </Button>
      </div>
    </div>
  </div>
</template>
