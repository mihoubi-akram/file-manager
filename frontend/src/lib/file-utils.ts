export const MIME_LABELS: Record<string, string> = {
  'application/pdf': 'PDF',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
  'image/png': 'PNG',
  'image/jpeg': 'JPG',
  'application/vnd.oasis.opendocument.text': 'ODT',
}

export const ACCEPTED_MIME_TYPES = Object.keys(MIME_LABELS)

export const LABEL_COLORS: Record<string, string> = {
  PDF: 'bg-red-100 text-red-700',
  DOCX: 'bg-teal-100 text-teal-700',
  PNG: 'bg-blue-100 text-blue-700',
  JPG: 'bg-blue-100 text-blue-700',
  ODT: 'bg-purple-100 text-purple-700',
}

export function getMimeLabel(mimeType: string): string {
  return MIME_LABELS[mimeType] ?? mimeType.split('/')[1]?.toUpperCase() ?? '?'
}

export function getBadgeClass(mimeType: string): string {
  const label = getMimeLabel(mimeType)
  return LABEL_COLORS[label] ?? 'bg-gray-100 text-gray-700'
}

export function formatBytes(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1_048_576) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / 1_048_576).toFixed(1)} MB`
}

export function formatDate(iso: string): string {
  return new Date(iso).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
