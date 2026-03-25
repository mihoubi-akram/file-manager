import http from '@/api/http'
import type { FilesQueryParams, PaginatedResponse, UserFile } from '@/types/file'

export async function listFiles(params: FilesQueryParams): Promise<PaginatedResponse<UserFile>> {
  const { data } = await http.get<PaginatedResponse<UserFile>>('/api/files', { params })
  return data
}

export async function uploadFiles(files: File[]): Promise<UserFile[]> {
  const form = new FormData()
  files.forEach((f) => form.append('files[]', f))
  const { data } = await http.post<{ data: UserFile[] }>('/api/files', form)
  return data.data
}

export async function downloadFile(id: string, name: string): Promise<void> {
  const { data } = await http.get<Blob>(`/api/files/${id}/download`, { responseType: 'blob' })
  const url = URL.createObjectURL(data)
  const a = document.createElement('a')
  a.href = url
  a.download = name
  a.click()
  setTimeout(() => URL.revokeObjectURL(url), 10_000)
}

export async function deleteFile(id: string): Promise<void> {
  await http.delete(`/api/files/${id}`)
}
