export function formatDate(dateStr: string): string {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  if (isNaN(d.getTime())) return dateStr
  return d.toLocaleDateString('pl-PL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

export const formatError = (rawError: string, t: (key: string, params?: any) => string): string => {
  if (!rawError) return ''

  if (rawError.includes('ReferenceError')) {
    const match = rawError.match(/ReferenceError: (.*) is not defined/)
    if (match) {
      return t('errors.referenceError', { var: match[1] })
    }
  }

  if (rawError.includes('SyntaxError')) {
    if (rawError.includes('Unexpected token')) {
      return t('errors.syntaxErrorUnexpectedToken')
    }
    return t('errors.syntaxError') + `\n\n${rawError}`
  }

  if (rawError.includes('TypeError')) {
    return t('errors.typeError') + `\n\n${rawError}`
  }

  return rawError
}
export function formatAvatarUrl(url: string | null | undefined): string {
  if (!url) return ''
  if (url.startsWith('data:') || url.startsWith('http') || url.startsWith('/') || url.startsWith('blob:')) {
    return url
  }
  return '/' + url
}
