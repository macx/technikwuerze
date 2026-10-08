const TIME_HASH_PATTERN = /^#t=(\d+(?:\.\d+)?)$/

export const parseTimeHash = (hash: string): number | null => {
  const match = TIME_HASH_PATTERN.exec(hash)
  return match ? Math.floor(Number(match[1])) : null
}

export const buildTimeUrl = (
  base: Pick<URL, 'origin' | 'pathname' | 'search'>,
  seconds: number
) => {
  const url = `${base.origin}${base.pathname}${base.search}`
  const rounded = Math.floor(seconds)
  return rounded > 0 ? `${url}#t=${rounded}` : url
}
