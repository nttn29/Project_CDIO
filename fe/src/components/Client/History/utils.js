export function formatDate(dateString) {
  if (!dateString) return ''
  try {
    const d = new Date(dateString)
    return d.toLocaleString()
  } catch (e) {
    return dateString
  }
}
