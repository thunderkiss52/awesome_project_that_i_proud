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

  return new Date(value).toISOString()
}

export function nowInputValue() {
  return toLocalInputValue(new Date())
}

export function addMinutesToInputValue(value, minutes) {
  const baseDate = value ? new Date(value) : new Date()

  return toLocalInputValue(new Date(baseDate.getTime() + minutes * 60000))
}

export function timezoneLabel() {
  return Intl.DateTimeFormat('ru-RU', {
    timeZoneName: 'short',
  })
    .formatToParts(new Date())
    .find((part) => part.type === 'timeZoneName')?.value || 'local time'
}

export function formatDayLabel(value) {
  if (!value) {
    return ''
  }

  return new Intl.DateTimeFormat('ru-RU', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
  }).format(new Date(value))
}

export function formatTime(value) {
  if (!value) {
    return ''
  }

  return new Intl.DateTimeFormat('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(value))
}
