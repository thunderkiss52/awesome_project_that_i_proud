export function formatDateTime(value) {
  if (!value) {
    return 'Not specified'
  }

  return new Intl.DateTimeFormat('ru-RU', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

export function toLocalInputValue(value) {
  if (!value) {
    return ''
  }

  const date = new Date(value)
  const offset = date.getTimezoneOffset()
  const local = new Date(date.getTime() - offset * 60000)

  return local.toISOString().slice(0, 16)
}

export function toApiDateTime(value) {
  if (!value) {
    return ''
  }

  return `${value.replace('T', ' ')}:00`
}
