export type ArchiveWord = { time: number; norm: string }

const LOOKAHEAD = 8
const WINDOW_LEAD_MS = 300
const WINDOW_TRAIL_MS = 1_000

export const normalizeWord = (value: string): string =>
  value.toLowerCase().replace(/[^\p{L}\p{N}]/gu, '')

export const selectWindow = (
  words: Array<ArchiveWord>,
  segmentStart: number,
  nextSegmentStart: number | null
): Array<ArchiveWord> => {
  const from = segmentStart - WINDOW_LEAD_MS
  const to = nextSegmentStart === null ? Infinity : nextSegmentStart + WINDOW_TRAIL_MS
  return words.filter((word) => word.time >= from && word.time <= to)
}

/**
 * Assigns a start time to every token of a transcript segment.
 * Tokens are matched in order against the archive words; unmatched tokens
 * (edited text, numbers, additions) get interpolated times between their neighbours.
 */
export const alignTokens = (
  tokens: Array<string>,
  words: Array<ArchiveWord>,
  fallbackStart: number,
  fallbackEnd: number
): Array<number> => {
  const matched: Array<number | null> = tokens.map(() => null)
  let cursor = 0

  tokens.forEach((token, index) => {
    if (token === '') return
    const limit = Math.min(cursor + LOOKAHEAD, words.length)
    for (let candidate = cursor; candidate < limit; candidate += 1) {
      if (words[candidate].norm === token) {
        matched[index] = words[candidate].time
        cursor = candidate + 1
        break
      }
    }
  })

  const times: Array<number> = []
  let previousIndex = -1
  let previousTime = fallbackStart

  const fill = (untilIndex: number, untilTime: number): void => {
    const span = untilIndex - previousIndex
    for (let index = previousIndex + 1; index < untilIndex; index += 1) {
      const ratio = (index - previousIndex) / span
      times[index] = previousTime + (untilTime - previousTime) * ratio
    }
  }

  matched.forEach((time, index) => {
    if (time === null) return
    fill(index, Math.max(time, previousTime))
    times[index] = Math.max(time, previousTime)
    previousIndex = index
    previousTime = times[index]
  })

  fill(tokens.length, Math.max(fallbackEnd, previousTime))
  return times
}

export const findActiveIndex = (times: Array<number>, playtime: number): number => {
  let low = 0
  let high = times.length - 1
  let result = -1

  while (low <= high) {
    const middle = (low + high) >> 1
    if (times[middle] <= playtime) {
      result = middle
      low = middle + 1
    } else {
      high = middle - 1
    }
  }

  return result
}
