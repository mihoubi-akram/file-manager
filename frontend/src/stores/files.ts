import { ref } from 'vue'
import { defineStore } from 'pinia'
import {
  listFiles,
  uploadFiles as apiUploadFiles,
  downloadFile as apiDownloadFile,
  deleteFile as apiDeleteFile,
} from '@/api/files'
import type { PaginationMeta, UserFile } from '@/types/file'

const PER_PAGE = 10

export const useFilesStore = defineStore('files', () => {
  const files = ref<UserFile[]>([])
  const meta = ref<PaginationMeta | null>(null)
  const search = ref('')
  const currentPage = ref(1)
  const loading = ref(false)
  const uploading = ref(false)
  const fileToDelete = ref<UserFile | null>(null)

  async function fetchFiles(): Promise<void> {
    loading.value = true
    try {
      const response = await listFiles({
        page: currentPage.value,
        per_page: PER_PAGE,
        search: search.value || undefined,
      })
      files.value = response.data
      meta.value = response.meta
    } finally {
      loading.value = false
    }
  }

  async function uploadFiles(pendingFiles: File[]): Promise<void> {
    uploading.value = true
    try {
      await apiUploadFiles(pendingFiles)
      currentPage.value = 1
      await fetchFiles()
    } finally {
      uploading.value = false
    }
  }

  async function downloadFile(file: UserFile): Promise<void> {
    await apiDownloadFile(file.id, file.name)
  }

  function confirmDelete(file: UserFile): void {
    fileToDelete.value = file
  }

  function cancelDelete(): void {
    fileToDelete.value = null
  }

  async function executeDelete(): Promise<void> {
    if (!fileToDelete.value) return
    await apiDeleteFile(fileToDelete.value.id)
    fileToDelete.value = null
    await fetchFiles()
  }

  function setSearch(query: string): void {
    search.value = query
    currentPage.value = 1
    fetchFiles()
  }

  function setPage(page: number): void {
    currentPage.value = page
    fetchFiles()
  }

  return {
    files,
    meta,
    search,
    currentPage,
    loading,
    uploading,
    fileToDelete,
    fetchFiles,
    uploadFiles,
    downloadFile,
    confirmDelete,
    cancelDelete,
    executeDelete,
    setSearch,
    setPage,
  }
})
