// Utility string/date helpers
export function formatDate(input) {
  if (!input) return ''
  const d = new Date(input)
  if (Number.isNaN(d.getTime())) return input
  // Format: DD/MM/YYYY HH:mm
  const pad = (n) => String(n).padStart(2, '0')
  const day = pad(d.getDate())
  const month = pad(d.getMonth() + 1)
  const year = d.getFullYear()
  const hours = pad(d.getHours())
  const minutes = pad(d.getMinutes())
  return `${day}/${month}/${year} ${hours}:${minutes}`
}

export default { formatDate }
